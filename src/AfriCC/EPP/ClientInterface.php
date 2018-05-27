<?php
use AfriCC\EPP\FrameInterface;

namespace\AfriCC\EPP;

interface ClientInterface
{

    public function connect($newPassword = false);

    public function close();

    public function request(FrameInterface $frame);
}

