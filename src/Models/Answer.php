<?php

namespace App\Models;

use PDO;
use App\Database\DatabaseConnection;

class Answer extends MainModel
{
    public int $question_id;
    public string $option_text;
    public bool $is_correct;

    protected static string $tableName = 'answers';
    protected static array $nullable = [];
    protected static array $uniques = [];


    public static function findByQuestion(int $question_id): array
    {
        $conn = DatabaseConnection::getConnection();
        $stmt = $conn->prepare("SELECT * FROM " . static::$tableName . " WHERE question_id = ?");
        $stmt->execute([$question_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($results as $result) {
            $objects[] = new static($result);
        }
        return $objects;
    }


    public static function checkCorrectAnswers(array $answerIds): array
    {
        if (empty($answerIds)) {
            return [];
        }
        $conn = DatabaseConnection::getConnection();
        $placeholders = implode(',', array_fill(0, count($answerIds), '?'));
        $sql = "SELECT id, is_correct FROM " . static::$tableName . " WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        foreach ($answerIds as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $correctnessMap = [];
        foreach ($results as $result) {
            $correctnessMap[$result['id']] = $result['is_correct'] ? "correct" : "wrong";
        }
        $resultArray = [];
        foreach ($answerIds as $id) {
            $resultArray[] = $correctnessMap[$id] ?? "wrong";
        }
        return $resultArray;
    }
}
