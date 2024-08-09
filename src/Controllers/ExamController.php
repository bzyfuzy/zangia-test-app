<?php

namespace App\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Report;
use App\Utils\JWT;
use App\Web\Request;
use App\Web\Response;

function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function parseExamFormData($formData)
{
    $data = [
        "title" => sanitize($formData['title']),
        "description" => sanitize($formData['description']),
        "thumbnail" => "",
        "questions" => []
    ];

    foreach ($formData['questions'] as $questionIndex => $question) {
        $questionData = [
            "question_title" => sanitize($question['question_title']),
            "question_text" => sanitize($question['question_text']),
            "question_image" => sanitize($question['question_image']),
            "question_video" => sanitize($question['question_video']),
            "answers" => []
        ];
        $questionData['answers'][] = [
            "option_text" => sanitize($question['correct_answer']),
            "is_correct" => true
        ];
        foreach ($question['answers'] as $answerText) {
            $questionData['answers'][] = [
                "option_text" => sanitize($answerText),
                "is_correct" => false
            ];
        }
        $data['questions'][] = $questionData;
    }
    return $data;
}

class ExamController
{
    public function index($req, $res)
    {
        $payload = JWT::decodeToken($_SESSION['token']);
        $is_admin = $payload['role'] == "ADMIN";
        $res->render("index", ["is_admin" => $is_admin]);
    }

    public function taxeExam(Request $req, Response $res)
    {
        $exam_id = intval($req->params["id"]);
        $data = Exam::prepare_exam($exam_id);
        $res->render("exam", ["data" => $data]);
    }

    public function fetchList($req, $res)
    {
        $page = $_GET["page"] ?? 1;
        $result = Exam::paginatedList((int)$page);
        $res->send($result);
    }


    public function createExam($req, $res)
    {
        $thumbnail_uri = "";
        if ($_FILES['exam_image']) {
            $name = $_FILES['exam_image']['name'];
            $tmp_name =  $_FILES['exam_image']['tmp_name'];
            $location = __DIR__ . "/../../public/uploads/";
            $new_name = $location . time() . "_" . rand(1000, 9999) . "_" . $name;
            if (move_uploaded_file($tmp_name, $new_name)) {
                $thumbnail_uri = "/uploads/" . $new_name;
            } else {
                $thumbnail_uri = "/images/default.png";
            }
        } else {
            $thumbnail_uri = "/images/default.png";
        }


        $parsedData = parseExamFormData($_POST);
        $parsedData["thumbnail"] = $thumbnail_uri;
        $exam = Exam::insert_with_questions($parsedData);
        $res->render("admin");
    }

    public function resultExam(Request $req, $res)
    {
        $req_data = $req->getBody();
        $answer_result = Answer::checkCorrectAnswers($req_data["answers"]);
        $payload = JWT::decodeToken($_SESSION['token']);
        $user_id = $payload['user_id'];
        $report = new Report([
            "user_id" => $user_id,
            "exam_id" => $req_data["exam_id"],
            "questions_ids" => json_encode($req_data["questions"]),
            "completed_second" => $req_data["completed"],
            "corrects" => json_encode($answer_result)
        ]);
        $report->save();
        $counts = array_count_values($answer_result);
        $correctCount = isset($counts["correct"]) ? $counts["correct"] : 0;
        $result = [
            "correct_answers" => $correctCount,
            "status" => "success",
        ];
        // echo json_encode($result);
        $res->send($result);
    }
    public function editExam($req, $res)
    {
        $exam_id = intval($req->params["exam_id"]);
        $result = Exam::admin_exam($exam_id);
        // $res->send($result);
        $res->render("admin.edit", $result);
    }
}
