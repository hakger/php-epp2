<?php
namespace AfriCC\EPP;

use AfriCC\EPP\Frame\ResponseFactory;
use AfriCC\EPP\Frame\Response as ResponseFrame;
use AfriCC\EPP\Frame\Command\Login as LoginCommand;
use AfriCC\EPP\Frame\Command\Logout as LogoutCommand;

class HTTPClient
{
    protected $curl;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $services;
    protected $serviceExtensions;
    protected $ssl;
    protected $local_cert;
    protected $ca_cert;
    protected $pk_cert;
    protected $debug;
    protected $connect_timeout;
    protected $timeout;
    protected $cookiejar;
    
    public function __construct(array $config)
    {
        if (!empty($config['host'])) {
            $this->host = (string)$config['host'];
        }
        
        if (!empty($config['port'])) {
            $this->port = (int)$config['port'];
        } else {
            $this->port = false;
        }
        
        if (!empty($config['username'])) {
            $this->username = (string)$config['username'];
        }
        
        if (!empty($config['password'])) {
            $this->password = (string)$config['password'];
        }
        
        if (!empty($config['services']) && is_array($config['services'])) {
            $this->services = $config['services'];
            
            if (!empty($config['serviceExtensions']) &&
                is_array($config['serviceExtensions'])) {
                    $this->serviceExtensions = $config['serviceExtensions'];
                }
        }
        
        if (!empty($config['ssl'])) {
            $this->ssl = true;
        } else {
            $this->ssl = false;
        }
        
        if (!empty($config['local_cert'])) {
            if (is_array($config['local_cert'])) {
                $lc = $config['local_cert'];
                $this->local_cert = $lc['cert'];
                $this->ca_cert = $lc['ca'];
                $this->pk_cert = $lc['pk'];
            } else {
                $this->local_cert = (string)$config['local_cert'];
                $this->ca_cert = false;
                $this->pk_cert = false;
            }
            
            if (!is_readable($this->local_cert)) {
                throw new \Exception(
                    sprintf('unable to read local_cert: %s', $this->local_cert)
                    );
            }
        }
        
        if (!empty($config['cookiejar'])) {
            $this->cookiejar = $config['cookiejar'];
        } else {
            $this->cookiejar = tempnam(sys_get_temp_dir(), 'ehc');
        }
        
        if (!is_readable($this->cookiejar) || !is_writable($this->cookiejar)) {
            throw new \Exception(
                sprintf(
                    'unable to read/write cookiejar: %s',
                    $this->cookiejar
                    )
                );
        }
        
        if (!empty($config['debug'])) {
            $this->debug = true;
        } else {
            $this->debug = false;
        }
        
        if (!empty($config['connect_timeout'])) {
            $this->connect_timeout = (int)$config['connect_timeout'];
        } else {
            $this->connect_timeout = 30;
        }
        
        if (!empty($config['timeout'])) {
            $this->timeout = (int)$config['timeout'];
        } else {
            $this->timeout = 60;
        }
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Open a new connection to the EPP server
     */
    public function connect($newPassword=false)
    {
        if ($this->ssl || \parse_url($this->host, PHP_URL_SCHEME) == 'https') {
            $proto = 'https';
            $this->ssl = true;
            
        } else {
            $proto = 'http';
            $this->ssl = false;
        }
        
        $this->curl = curl_init($this->host);
        
        if ($this->curl === false) {
            throw new \Exception('Cannot initialize cURL extension');
        }
        
        // set stream time out
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt(
            $this->curl,
            CURLOPT_CONNECTTIMEOUT,
            $this->connect_timeout
            );
        
        // set necessary options
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        
        // cookies
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookiejar);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookiejar);
        
        // certs
        if ($this->ssl) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($this->curl, CURLOPT_SSLKEYTYPE, 'PEM');
            
            if ($this->ca_cert) {
                curl_setopt($this->curl, CURLOPT_CAINFO, $this->ca_cert);
            }
            if ($this->pk_cert) {
                curl_setopt($this->curl, CURLOPT_SSLKEY, $this->pk_cert);
            }
            curl_setopt($this->curl, CURLOPT_SSLCERT, $this->local_cert);
        }
        
        
        // get greeting
        $greeting = $this->request(new \AfriCC\EPP\Frame\Hello());
        
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
            return curl_close($this->curl);
        }
        return false;
    }
    
    /**
     * sends a XML-based frame to the server
     * @param FrameInterface $frame the frame to send to the server
     */
    public function send(FrameInterface $frame)
    {
        
        $content = (string)$frame;
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $content);
        
        return curl_exec($this->curl);
    }
    
    /**
     * request via EPP
     *
     * @return ResponseFrame Description
     */
    public function request(FrameInterface $frame)
    {
        
        if ($frame instanceof TransactionAwareInterface) {
            $frame->setClientTransactionId(
                $this->generateClientTransactionId()
                );
        }
        
        $return = $this->send($frame);
        
        if ($return === false) {
            $code = curl_errno($this->curl);
            $msg = curl_error($this->curl);
            throw new \Exception($msg, $code);
        }
        
        return ResponseFactory::build($return);
    }
    
    /**
     * check if socket is still active
     * @return boolean
     */
    public function active()
    {
        return is_resource($this->curl);
    }
    
    protected function login($newPassword=false)
    {
        // send login command
        $login = new LoginCommand();
        $login->setClientId($this->username);
        $login->setPassword($this->password);
        if($newPassword){
            $login->setNewPassword($newPassword);
        }
        $login->setVersion('1.0');
        $login->setLanguage('en');
        
        if (!empty($this->services) && is_array($this->services)) {
            foreach ($this->services as $urn) {
                $login->addService($urn);
            }
            
            if (!empty($this->serviceExtensions) &&
                is_array($this->serviceExtensions)) {
                    foreach ($this->serviceExtensions as $extension) {
                        $login->addServiceExtension($extension);
                    }
                }
        }
        
        $response = $this->request($login);
        if ($this->debug) {
            error_log($login);
            error_log($response);
        }
        unset($login);
        
        // check if login was successful
        if (!($response instanceof ResponseFrame)) {
            if ($this->debug) {
                error_log(print_r($response, true));
                var_dump($response);
            }
            throw new \Exception(
                'there was a problem logging onto the EPP server'
                );
        } elseif ($response->code() !== 1000) {
            throw new \Exception($response->message(), $response->code());
        }
    }
    
    protected function generateClientTransactionId()
    {
        return Random::id(64, $this->username);
    }
}

