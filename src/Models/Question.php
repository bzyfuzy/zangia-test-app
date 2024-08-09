<?php

namespace App\Models;

use PDO;
use App\Database\DatabaseConnection;

if (!function_exists('quest_cb')) {
    function quest_cb($answer, $id)
    {
        $answer["is_correct"] = $answer["is_correct"] ? 1 : 0;
        $answer["question_id"] = $id;
        return $answer;
    }
}
class Question extends MainModel
{
    public int $exam_id;
    public string $title;
    public ?string $question_text = null;
    public ?string $image = null;
    public ?string $video = null;

    protected static string $tableName = 'questions';
    protected static array $nullable = ['question_text', 'image', 'video'];
    protected static array $uniques = ['title'];

    public static function insert_with_answers($data, $exam_id)
    {
        $new_question = new static([
            "exam_id" => $exam_id,
            "title" => $data["question_title"],
            "question_text" => $data["question_text"],
            "image" => $data["question_image"],
            "video" => $data["question_video"],
        ]);
        $new_question->save();
        $conn = DatabaseConnection::getConnection();
        $sql = 'INSERT INTO answers(option_text, is_correct, question_id) VALUES(:option_text, :is_correct, :question_id)';
        $stmt = $conn->prepare($sql);

        $answers = array_map(fn($answer) => quest_cb($answer, $new_question->id), $data['answers']);
        foreach ($answers as $row) {
            $stmt->execute($row);
        }
        return $new_question;
    }

    public static function listRandom(int $limit = 10): array
    {
        $conn = DatabaseConnection::getConnection();
        $sql = "SELECT * FROM " . static::$tableName . " ORDER BY RAND() LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $objects = [];
        foreach ($results as $result) {
            $objects[] = new static($result);
        }
        return $objects;
    }

    public static function listQuestionsWithAnswersByExamRandomly(int $examId, int $limit = 10): array
    {
        $conn = DatabaseConnection::getConnection();
        $sql = "
        WITH ValidQuestions AS (
            SELECT q.id AS question_id
            FROM questions q
            LEFT JOIN answers a ON q.id = a.question_id
            WHERE q.exam_id = ? AND q.is_deleted = 0
            GROUP BY q.id
            HAVING COUNT(a.id) >= 4
        ),
        AllAnswers AS (
            SELECT q.id AS question_id, q.title AS question_title, q.question_text, q.image, q.video, a.id AS answer_id, a.option_text, a.is_correct
            FROM ValidQuestions v
            JOIN questions q ON v.question_id = q.id
            LEFT JOIN answers a ON q.id = a.question_id
        )
        SELECT question_id, question_title, question_text, image, video, answer_id, option_text, is_correct
        FROM AllAnswers
        ORDER BY RAND()
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $examId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $questionsWithAnswers = [];

        foreach ($results as $result) {
            $questionId = $result['question_id'];
            if (!isset($questionsWithAnswers[$questionId])) {
                $questionsWithAnswers[$questionId] = [
                    'question' => [
                        'id' => $result['question_id'],
                        'title' => $result['question_title'],
                        'text' => $result['question_text'],
                        'image' => $result['image'],
                        'video' => $result['video'],
                    ],
                    'answers' => [],
                ];
            }
            if (isset($result['answer_id'])) {
                $questionsWithAnswers[$questionId]['answers'][] = [
                    'id' => $result['answer_id'],
                    'text' => $result['option_text'],
                    'is_correct' => $result['is_correct'] == 1,
                ];
            }
        }

        return array_values($questionsWithAnswers);
    }
}
