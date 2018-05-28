<?php

/**
 * This file is part of the php-epp2 library.
 *
 * (c) Gunter Grodotzki <gunter@afri.cc>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace AfriCC\EPP;

use AfriCC\EPP\Frame\Command\Logout as LogoutCommand;
use AfriCC\EPP\Frame\ResponseFactory;
use \Exception;

/**
 * A high level TCP (SSL) based client for the Extensible Provisioning Protocol (EPP)
 *
 * @see http://tools.ietf.org/html/rfc5734
 */
class Client extends AbstractClient implements ClientInterface
{
    protected $socket;
    protected $chunk_size;

    public function __construct(array $config)
    {
        parent::__construct($config);
        
        if (!empty($config['chunk_size'])) {
            $this->chunk_size = (int)$config['chunk_size'];
        } else {
            $this->chunk_size = 1024;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Open a new connection to the EPP server
     */
    public function connect($newPassword = false)
    {
        $proto = $this->ssl ? 'ssl' : 'tcp';
        
        if ($this->ssl) {

            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);

            if ($this->local_cert !== null) {
                stream_context_set_option($context, 'ssl', 'local_cert', $this->local_cert);

                if ($this->passphrase) {
                    stream_context_set_option($context, 'ssl', 'passphrase', $this->passphrase);
                }
            }
            if ($this->ca_cert !== null){
                stream_context_set_option($context, 'ssl', 'cafile', $this->ca_cert);
            }
            if ($this->pk_cert !== null){
                stream_context_set_option($context, 'ssl', 'local_pk', $this->pk_cert);
            }
        } 

        $target = sprintf('%s://%s:%d', $proto, $this->host, $this->port ? $this->port : 700);

        $errno = 0;
        $errstr = '';
        if (isset($context) && is_resource($context)) {
            $this->socket = @stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT, $context);
        } else {
            $this->socket = @stream_socket_client($target, $errno, $errstr, $this->connect_timeout, STREAM_CLIENT_CONNECT);
        }

        if ($this->socket === false) {
            throw new Exception($errstr, $errno);
        }

        // set stream time out
        if (!stream_set_timeout($this->socket, $this->timeout)) {
            throw new Exception('unable to set stream timeout');
        }

        // set to non-blocking
        if (!stream_set_blocking($this->socket, 0)) {
            throw new Exception('unable to set blocking');
        }

        // get greeting
        $greeting = $this->getFrame();

        // login
        $this->login($newPassword);

        // return greeting
        return $greeting;
    }

    /**
     * Closes a previously opened EPP connection
     */
    public function close()
    {
        if ($this->active()) {
            // send logout frame
            $this->request(new LogoutCommand());

            return fclose($this->socket);
        }

        return false;
    }

    /**
     * Get an EPP frame from the server.
     */
    public function getFrame()
    {
        $header = $this->recv(4);

        // Unpack first 4 bytes which is our length
        $unpacked = unpack('N', $header);
        $length = $unpacked[1];

        if ($length < 5) {
            throw new Exception(sprintf('Got a bad frame header length of %d bytes from peer', $length));
        } else {
            $length -= 4;

            return ResponseFactory::build($this->recv($length));
        }
    }

    /**
     * sends a XML-based frame to the server
     *
     * @param FrameInterface $frame the frame to send to the server
     */
    public function sendFrame(FrameInterface $frame)
    {
        // some frames might require a client transaction identifier, so let us
        // inject it before sending the frame
        if ($frame instanceof TransactionAwareInterface) {
            $frame->setClientTransactionId($this->generateClientTransactionId());
        }

        $buffer = (string) $frame;
        $header = pack('N', mb_strlen($buffer, 'ASCII') + 4);

        return $this->send($header . $buffer);
    }

    /**
     * a wrapper around sendFrame() and getFrame()
     */
    public function request(FrameInterface $frame)
    {
        $this->sendFrame($frame);

        return $this->getFrame();
    }

    

    protected function log($message, $color = '0;32')
    {
        if ($message === '' || !$this->debug) {
            return;
        }
        echo sprintf("\033[%sm%s\033[0m", $color, $message);
    }
    
    /**
     * check if socket is still active
     *
     * @return bool
     */
    private function active()
    {
        return !is_resource($this->socket) || feof($this->socket) ? false : true;
    }

    /**
     * receive socket data
     *
     * @param int $length
     *
     * @throws Exception
     *
     * @return string
     */
    private function recv($length)
    {
        $result = '';

        $info = stream_get_meta_data($this->socket);
        $hard_time_limit = time() + $this->timeout + 2;

        while (!$info['timed_out'] && !feof($this->socket)) {
            // Try read remaining data from socket
            $buffer = @fread($this->socket, $length - mb_strlen($result, 'ASCII'));

            // If the buffer actually contains something then add it to the result
            if ($buffer !== false) {
                
                    $this->log($buffer);
                

                $result .= $buffer;

                // break if all data received
                if (mb_strlen($result, 'ASCII') === $length) {
                    break;
                }
            } else {
                // sleep 0.25s
                usleep(250000);
            }

            // update metadata
            $info = stream_get_meta_data($this->socket);
            if (time() >= $hard_time_limit) {
                throw new Exception('Timeout while reading from EPP Server');
            }
        }

        // check for timeout
        if ($info['timed_out']) {
            throw new Exception('Timeout while reading data from socket');
        }

        return $result;
    }

    /**
     * send data to socket
     *
     * @param string $buffer
     */
    private function send($buffer)
    {
        $info = stream_get_meta_data($this->socket);
        $hard_time_limit = time() + $this->timeout + 2;
        $length = mb_strlen($buffer, 'ASCII');

        $pos = 0;
        while (!$info['timed_out'] && !feof($this->socket)) {
            // Some servers don't like a lot of data, so keep it small per chunk
            $wlen = $length - $pos;

            if ($wlen > $this->chunk_size) {
                $wlen = $this->chunk_size;
            }

            // try write remaining data from socket
            $written = @fwrite($this->socket, mb_substr($buffer, $pos, $wlen, 'ASCII'), $wlen);

            // If we read something, bump up the position
            if ($written) {
                if ($this->debug) {
                    $this->log(mb_substr($buffer, $pos, $wlen, 'ASCII'), '1;31');
                }
                $pos += $written;

                // break if all written
                if ($pos === $length) {
                    break;
                }
            } else {
                // sleep 0.25s
                usleep(250000);
            }

            // update metadata
            $info = stream_get_meta_data($this->socket);
            if (time() >= $hard_time_limit) {
                throw new Exception('Timeout while writing to EPP Server');
            }
        }

        // check for timeout
        if ($info['timed_out']) {
            throw new Exception('Timeout while writing data to socket');
        }

        if ($pos !== $length) {
            throw new Exception('Writing short %d bytes', $length - $pos);
        }

        return $pos;
    }
}
