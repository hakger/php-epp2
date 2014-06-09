<?php
/**
 *
 * @author Gavin Brown <gavin.brown@nospam.centralnic.com>
 * @author Gunter Grodotzki <gunter@afri.cc>
 * @license GPL
 */
namespace AfriCC\EPP\Frame\Command\Delete;

use AfriCC\EPP\Frame\Command\Delete;

class Domain extends Delete
{
    public function __construct()
    {
        parent::__construct('domain');
    }
}
