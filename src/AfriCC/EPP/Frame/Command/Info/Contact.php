<?php

/**
 * This file is part of the php-epp2 library.
 *
 * (c) Gunter Grodotzki <gunter@afri.cc>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace AfriCC\EPP\Frame\Command\Info;

use AfriCC\EPP\Frame\Command\Info as InfoCommand;

/**
 * @see http://tools.ietf.org/html/rfc5733#section-3.1.2
 */
class Contact extends InfoCommand
{
    public function setId($id)
    {
        $this->set('contact:id', $id);
    }

    /**
     * Set contact authinfo
     * 
     * @param string $pw authinfo
     * @param string $roid If specified, authinfo is of domain whose registrant is this contac
     */
    public function setAuthInfo($pw, $roid = null)
    {
        $node = $this->set('contact:authInfo/contact:pw', $pw);
        
        if ($roid !== null) {
            $node->setAttribute('roid', $roid);
        }
    }
}
