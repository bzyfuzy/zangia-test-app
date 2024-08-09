<?php

namespace App\Middlewares;

use App\Utils\JWT;

class Auth
{
    public static function checkAuth(): callable
    {
        return function ($request, $response) {

            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $token = null;
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
            if (!$token && isset($_SESSION['token'])) {
                $token = $_SESSION['token'];
            }

            if (!$token) {
                $response->redirect("/login");
            }

            try {
                $payload = JWT::decodeToken($token);
            } catch (\Exception $e) {
                $response->redirect("/login");
                return false;
            }

            if ($payload && isset($payload['user_id'])) {
                return true;
            }
            $response->redirect("/login");
            return false;
        };
    }

    public static function checkAdmin(): callable
    {
        return function ($request, $response) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $token = null;
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
            if (!$token && isset($_SESSION['token'])) {
                $token = $_SESSION['token'];
            }
            if (!$token) {
                $response->setStatusCode(403);
                $response->send("Unauthorized: Хандах эрх байхгүй байна!");
                return;
            }
            try {
                $payload = JWT::decodeToken($token);
            } catch (\Exception $e) {
                $response->setStatusCode(403);
                $response->send("Invalid token: " . $e->getMessage());
                return;
            }
            if ($payload && isset($payload['user_id']) && $payload['role'] === 'ADMIN') {
                return;
            }
            $response->setStatusCode(403);
            $response->send("Unauthorized: Хандах эрх байхгүй байна!");
        };
    }

    public static function logout()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            unset($_SERVER['HTTP_AUTHORIZATION']);
        }
        unset($_SESSION['token']);
        session_unset();
        session_destroy();
    }
}
