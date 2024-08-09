<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

use App\Web\App;
use App\Middlewares\Auth;


$app = new App();

$app->get("/", "ExamController::index", [Auth::checkAuth()]);
$app->get("/exam/{id}", "ExamController::taxeExam", [Auth::checkAuth()]);
$app->get("/api/exams", "ExamController::fetchList", [Auth::checkAuth()]);
$app->post("/api/exam", "ExamController::resultExam", [Auth::checkAuth()]);

$app->get('/login', "Login::get");
$app->post('/login', "Login::post");

$app->get('/test', "view::test");

$app->get('/register', "Register::get");
$app->post('/register', "Register::post");

$app->post('/test', "ExamController::createExam");

$app->get('/logout', function ($_, $response) {
    Auth::logout();
    $response->redirect('/login');
});

$app->get('/admin', "view::admin.view", [Auth::checkAuth(), Auth::checkAdmin()]);
$app->get('/admin/exam/{exam_id}', "ExamController::editExam", [Auth::checkAuth(), Auth::checkAdmin()]);
$app->post('/admin', "ExamController::createExam", [Auth::checkAuth(), Auth::checkAdmin()]);

$app->run();
