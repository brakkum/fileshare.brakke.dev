<?php

function encrypt($plain_text, $password) {
    $method = "AES-256-CBC";
    $key = hash('sha256', $password, true);
    $iv = openssl_random_pseudo_bytes(16);

    $cipher_text = openssl_encrypt($plain_text, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $cipher_text . $iv, $key, true);

    return $iv . $hash . $cipher_text;
}

function decrypt($iv_hash_cipher_text, $password) {
    $method = "AES-256-CBC";
    $iv = substr($iv_hash_cipher_text, 0, 16);
    $hash = substr($iv_hash_cipher_text, 16, 32);
    $cipher_text = substr($iv_hash_cipher_text, 48);
    $key = hash('sha256', $password, true);

    if (!hash_equals(hash_hmac('sha256', $cipher_text . $iv, $key, true), $hash)) return null;

    return openssl_decrypt($cipher_text, $method, $key, OPENSSL_RAW_DATA, $iv);
}
