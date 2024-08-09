<?php

namespace App\Models;

use PDO;
use App\Utils\JWT;
use App\Models\MainModel;
use App\Database\DatabaseConnection;

class User extends MainModel
{
    public int $phone;
    public string $email, $first_name, $last_name, $password;
    public bool $is_admin = false;


    protected static string $tableName = 'users';
    protected static array $nullable = ["first_name", "last_name"];
    protected static array $uniques = ["email", "phone"];

    public function save(): void
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        parent::save();
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }


    public static function login(string $username, string $password): ?self
    {
        if (!$username || !$password) {
            return null;
        }
        $user = static::findOne(['or' => [['email' => $username], ['phone' => $username]]]);
        if (!$user) {
            return null;
        } else
            if ($user->verifyPassword($password)) {
            $payload = [
                'user_id' => $user->id,
                'role' =>  $user->is_admin ? "ADMIN" : "CLIENT",
                'iat' => time(),
                'exp' => time() + 3600 // 1 hour expiration
            ];
            $token = JWT::generateToken($payload);
            $_SESSION['token'] = $token;
            return $user;
        }
        return null;
    }

    public static function register()
    {
        $phone = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $last_name = $_POST['lastName'];
        $first_name = $_POST['firstName'];
        $conn = DatabaseConnection::getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            return 'Бүртгэлтэй имэйл хаяг байна';
        }
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            return 'Бүртгэлтэй утасны дугаар байна';
        }
        $data = [
            'phone' => $phone,
            'email' => $email,
            'password' => $password,
            'last_name' => $last_name,
            'first_name' => $first_name
        ];
        $user = new self($data);
        $user->save();
        $payload = [
            'user_id' => $user->id,
            'role' =>  $user->is_admin ? "ADMIN" : "CLIENT",
            'iat' => time(),
            'exp' => time() + 3600 // 1 hour expiration
        ];
        $token = JWT::generateToken($payload);
        $_SESSION['token'] = $token;
        return null;
    }
}
