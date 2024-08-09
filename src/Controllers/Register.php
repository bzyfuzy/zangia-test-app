<?php

namespace App\Controllers;

use App\Models\User;

class Register
{
    public function get($req, $res)
    {
        $res->render("register");
    }

    public function post($req, $res)
    {
        $error = User::register();
        if (is_null($error)) {
            $res->redirect('/');
        } else {
            $res->render("register", ["error" => $error]);
        }
    }
}
