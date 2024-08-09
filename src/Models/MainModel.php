<?php

namespace App\Models;

use PDO;
use App\Database\DatabaseConnection;

class MainModel
{
    public int $id;
    public string $created_at, $updated_at;
    public bool $is_deleted = false;

    protected static string $tableName;
    protected static array $nullable;
    protected static array $uniques;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        if (!isset($this->created_at) || !isset($this->updated_at)) {
            $this->set_timestamp();
        }
    }

    public function save(): void
    {
        $conn = DatabaseConnection::getConnection();
        $keys = array_keys(get_object_vars($this));
        $placeholders = array_fill(0, count($keys), '?');
        $values = array_map(fn($val) => is_bool($val) ? ($val ? 1 : 0) : $val, array_values(get_object_vars($this)));
        $query = "INSERT INTO " . static::$tableName . " (" . implode(", ", $keys) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $conn->prepare($query);
        $stmt->execute($values);
        $this->id = $conn->lastInsertId();
    }

    public function toJSON(): string
    {
        return json_encode(get_object_vars($this));
    }



    private function set_timestamp(): void
    {
        if (!isset($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public static function getProperties(): array
    {
        $reflect = new \ReflectionClass(static::class);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $props = [];
        foreach ($properties as $property) {
            $props[$property->getName()] = $property->getType()->getName();
        }
        return $props;
    }

    public static function getTableName(): string
    {
        return static::$tableName;
    }

    public static function getNullable()
    {
        return static::$nullable;
    }

    public static function getUniques()
    {
        return static::$uniques;
    }

    public static function find(array $query): array
    {
        $conn = DatabaseConnection::getConnection();
        $whereClauses = [];
        $params = [];
        foreach ($query as $key => $value) {
            $whereClauses[] = "$key = ?";
            $params[] = $value;
        }
        $whereSql = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM " . static::$tableName . " WHERE " . $whereSql;
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($results as $result) {
            $objects[] = new static($result);
        }

        return $objects;
    }

    public static function findOneByID(int $id): ?self
    {
        $conn = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM " . static::$tableName . " WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? new static($result) : null;
    }

    public static function delete(int $id): bool
    {
        $conn = DatabaseConnection::getConnection();
        $sql = "UPDATE " . static::$tableName . " SET is_deleted = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function findOne(array $query): ?static
    {
        $conn = DatabaseConnection::getConnection();
        $whereClauses = [];
        $params = [];
        if (isset($query['and'])) {
            foreach ($query['and'] as $key => $value) {
                $whereClauses[] = "$key = ?";
                $params[] = $value;
            }
        }

        if (isset($query['or'])) {
            $orClauses = [];
            foreach ($query['or'] as $condition) {
                $orSubClauses = [];
                foreach ($condition as $key => $value) {
                    $orSubClauses[] = "$key = ?";
                    $params[] = $value;
                }
                $orClauses[] = "(" . implode(' OR ', $orSubClauses) . ")";
            }
            $whereClauses[] = "(" . implode(' OR ', $orClauses) . ")";
        }

        $whereSql = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM " . static::$tableName . " WHERE " . $whereSql . " LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new static($result) : null;
    }

    public static function paginatedList(int $page = 1, int $perPage = 20): array
    {
        $conn = DatabaseConnection::getConnection();
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM " . static::$tableName . " WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($results as $result) {
            $objects[] = new static($result);
        }
        return $objects;
    }
}
