<?php

namespace App\Utils;

class JWT
{
    private static string $secretKey = 'the_secret_key';

    public static function generateToken(array $payload): string
    {
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);

        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secretKey, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    public static function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        $signature = self::base64UrlDecode($signatureEncoded);

        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secretKey, true);

        if (hash_equals($expectedSignature, $signature)) {
            return $payload;
        }

        return null;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $data = strtr($data, '-_', '+/');
        return base64_decode(str_pad($data, strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
