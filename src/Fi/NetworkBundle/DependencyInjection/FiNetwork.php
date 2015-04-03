<?php

namespace Fi\NetworkBundle\DependencyInjection;

class FiNetwork {

    private $ip;

    public function __construct($ip) {
        $this->ip = $ip;
    }

    public function getHostName() {
        return gethostbyaddr($this->ip);
    }

    public function isUp() {
        exec(sprintf('ping -c 1 -W 5 %s', $this->ip), $res, $rval);
        return $rval === 0;
    }

    public function getIp() {
        return $this->ip;
    }

}
