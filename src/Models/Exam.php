<?php

namespace App\Models;

use PDO;
use App\Database\DatabaseConnection;

class Exam extends MainModel
{
    public string $title;
    public ?string $description = null;
    public ?string $thumbnail = null;

    protected static string $tableName = 'exams';
    protected static array $nullable = ['description', 'thumbnail'];
    protected static array $uniques = ['title'];


    public static function insert_with_questions(array $data): self
    {
        $new_exam = new static([
            "title" => $data["title"],
            "description" => $data["description"],
            "thumbnail" => $data["thumbnail"]
        ]);
        $new_exam->save();
        foreach ($data["questions"] as $q_data) {
            Question::insert_with_answers($q_data, $new_exam->id);
        }
        return $new_exam;
    }

    public static function prepare_exam(int $id): array
    {
        $exam = static::findOneByID($id);
        $questions = Question::listQuestionsWithAnswersByExamRandomly($id);
        return ["exam" => $exam, "questions" => $questions];
    }

    public static function getExamWithQuestionsAndAnswers(int $examId): array
    {
        $conn = DatabaseConnection::getConnection();

        // SQL query to get exam details
        $examSql = "
        SELECT id, title, description, created_at
        FROM exams
        WHERE id = ? AND is_deleted = 0
        LIMIT 1
    ";

        // Prepare and execute exam query
        $stmt = $conn->prepare($examSql);
        $stmt->bindValue(1, $examId, PDO::PARAM_INT);
        $stmt->execute();
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            return []; // Return empty array if no exam is found
        }

        // SQL query to get all questions and their associated answers for the given exam_id
        $questionsSql = "
        SELECT q.id as question_id, q.title as question_title, q.question_text, q.image, q.video, 
               a.id as answer_id, a.option_text, a.is_correct
        FROM questions q
        LEFT JOIN answers a ON q.id = a.question_id
        WHERE q.exam_id = ? AND q.is_deleted = 0
        ORDER BY q.id, a.is_correct DESC, RAND()
    ";

        $stmt = $conn->prepare($questionsSql);
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

        return [
            'exam' => $exam,
            'questions' => array_values($questionsWithAnswers),
        ];
    }

    public static function admin_exam(int $id)
    {
        $exam_result = static::getExamWithQuestionsAndAnswers($id);
        $reports = Report::find(["exam_id" => $id]);
        $reports_result = array_map(function ($report) {
            $count = substr_count($report->corrects, 'correct');
            $questions = json_decode($report->questions_ids);

            return [
                "corrected" => $count,
                "sumQuestions" => count($questions),
                "completed_seconds" => $report->completed_second,
                "user_id" => $report->user_id
            ];
        }, $reports);
        return [
            "exam_data" => $exam_result,
            "reports_result" => $reports_result,
        ];
    }
}
