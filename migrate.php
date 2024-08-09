<?php

namespace App\Migrations;

require_once(__DIR__ . "/autoload.php");

use PDO;
use App\Database\DatabaseConnection;
use App\Models\DBModel;
use App\Models\User; // Include all your models here

class Migration
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getConnection();
    }

    public function migrate(): void
    {
        $this->migrateModel(User::class);
        // Add more models to migrate here
    }

    private function migrateModel(string $modelClass): void
    {
        if (!is_subclass_of($modelClass, DBModel::class)) {
            throw new \Exception("Class $modelClass must be a subclass of DBModel.");
        }

        $tableName = $modelClass::getTableName();
        $properties = $modelClass::getProperties();

        $nulls = $modelClass::getNullable();
        $uniques = $modelClass::getUniques();
        $fields = [];
        foreach ($properties as $name => $type) {
            $field = "$name ";
            if (in_array($name, ["created_at", "updated_at"])) {
                continue;
            }
            switch ($type) {
                case 'int':
                    $field .= "INT";
                    if ($name === 'id') {
                        $field .= " AUTO_INCREMENT PRIMARY KEY";
                    }
                    break;
                case 'string':
                    $field .= "VARCHAR(255)";
                    break;
                case 'bool':
                    $field .= "BOOLEAN";
                    break;
                case 'float':
                    $field .= "FLOAT";
                    break;
                default:
                    throw new \Exception("Unsupported property type: $type");
            }
            if (in_array($name, $nulls)) {
                $field .= " NULL";
            } else {
                if ($name !== 'id') {
                    $field .= " NOT NULL";
                }
                if (in_array($name, $uniques)) {
                    $field .= " UNIQUE";
                }
            }


            $fields[] = $field;
        }

        // var_dump($fields);

        // Adding timestamp fields
        $fields[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $fields[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

        $query = "CREATE TABLE IF NOT EXISTS $tableName (" . implode(", ", $fields) . ") ENGINE=INNODB;";

        $this->conn->exec($query);

        $data = [
            'phone' => 99832585,
            'email' => "admin@email.com",
            'password' => password_hash("qwep[]", PASSWORD_DEFAULT),
            'is_admin' => 1,
            'last_name' => "Default",
            'first_name' => "Admin",
            'is_deleted' => 0
        ];
        $sql = "INSERT INTO users (phone, email, is_admin, last_name, first_name, password, is_deleted) VALUES (:phone, :email, :is_admin, :last_name, :first_name, :password, :is_deleted)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);

        echo "done!";
    }
}

$migration = new Migration();
$migration->migrate();
