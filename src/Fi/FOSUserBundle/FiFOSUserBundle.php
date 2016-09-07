<?php

// src/Fi/OverBundle/FiOverBundle.php
namespace Fi\FOSUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FiFOSUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
