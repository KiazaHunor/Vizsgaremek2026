<?php
// Fejlécek beállítása
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');

// Preflight kérések kezelése
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// JWT titkos kulcs
define("JWS_SECRET", "nagyon_titkos_kulcs_123_13C_bejelentkezo");

// JWT létrehozása
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

// JWT ellenőrzése
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