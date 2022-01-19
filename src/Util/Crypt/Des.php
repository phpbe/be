<?php

namespace Be\Util\Crypt;


class Des
{
    private static $name = 'DES';
    private static $cipherMethod = 'DES-CBC';

    use Traits\OpenSSL;
}
