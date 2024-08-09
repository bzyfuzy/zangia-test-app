<?php

namespace App\Models;

class Report extends MainModel
{
    public int $exam_id, $user_id, $completed_second;
    public string $corrects, $questions_ids;
    protected static string $tableName = 'reports';
}
