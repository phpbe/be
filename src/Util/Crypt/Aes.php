<?php

namespace Be\Util\Crypt;

class Aes
{
    private static $name = 'AES';
    private static $cipherMethod = 'AES-256-CBC';

    use Traits\OpenSSL;
}
