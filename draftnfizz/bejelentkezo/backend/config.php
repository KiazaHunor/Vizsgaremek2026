<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


define("JWS_SECRET", "nagyon_titkos_kulcs_123_13C_bejelentkezo");

define('PUBLIC_BASE_URL', 'https://draftn-fizz.ady.pepa.hu');
define('LOCAL_BASE_URL', 'https://draftn-fizz.local.pepa.hu');

function get_base_url() {
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if ($host === 'draftn-fizz.local.pepa.hu') {
        return LOCAL_BASE_URL;
    }

    return PUBLIC_BASE_URL;
}

function create_jwt($payload) {
    $header = base64_encode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
    $body = base64_encode(json_encode($payload));
    
    $signature = hash_hmac(
        "sha256",
        "$header.$body",
        JWS_SECRET,
        true
    );
    
    return "$header.$body." . base64_encode($signature);
}

function verify_jwt($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    
    list($header, $body, $signature) = $parts;
    $expected_signature = hash_hmac(
        "sha256",
        "$header.$body",
        JWS_SECRET,
        true
    );
    
    return hash_equals(base64_decode($signature), $expected_signature);
}