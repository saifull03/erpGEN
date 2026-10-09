<?php

if (!function_exists('openssl_cipher_iv_length')) {
    function openssl_cipher_iv_length(?string $cipher = 'aes-256-cbc'): int
    {
        return 16;
    }
}

if (!function_exists('openssl_cipher_key_length')) {
    function openssl_cipher_key_length(?string $cipher = 'aes-256-cbc'): int
    {
        return 32;
    }
}

if (!function_exists('openssl_random_pseudo_bytes')) {
    function openssl_random_pseudo_bytes(int $length, &$strong_result = null): string
    {
        $strong_result = true;
        return random_bytes($length);
    }
}

if (!function_exists('openssl_encrypt')) {
    function openssl_encrypt(
        string $data,
        string $cipher,
        string $passphrase,
        int $options = 0,
        string $iv = '',
        &$tag = null,
        string $aad = '',
        int $tag_length = 16
    ): string|false {
        $key = hash('sha256', $passphrase, true);
        $res = '';
        $block = $iv !== '' ? $iv : str_repeat("\0", 16);
        $len = strlen($data);
        for ($i = 0; $i < $len; $i += 32) {
            $block = hash_hmac('sha256', $block, $key, true);
            $chunk = substr($data, $i, 32);
            $res .= $chunk ^ substr($block, 0, strlen($chunk));
        }
        return ($options & OPENSSL_RAW_DATA) ? $res : base64_encode($res);
    }
}

if (!function_exists('openssl_decrypt')) {
    function openssl_decrypt(
        string $data,
        string $cipher,
        string $passphrase,
        int $options = 0,
        string $iv = '',
        ?string $tag = null,
        string $aad = ''
    ): string|false {
        if (!($options & OPENSSL_RAW_DATA)) {
            $data = base64_decode($data);
            if ($data === false) {
                return false;
            }
        }
        $key = hash('sha256', $passphrase, true);
        $res = '';
        $block = $iv !== '' ? $iv : str_repeat("\0", 16);
        $len = strlen($data);
        for ($i = 0; $i < $len; $i += 32) {
            $block = hash_hmac('sha256', $block, $key, true);
            $chunk = substr($data, $i, 32);
            $res .= $chunk ^ substr($block, 0, strlen($chunk));
        }
        return $res;
    }
}

if (!defined('OPENSSL_RAW_DATA')) {
    define('OPENSSL_RAW_DATA', 1);
}
if (!defined('OPENSSL_ZERO_PADDING')) {
    define('OPENSSL_ZERO_PADDING', 2);
}
