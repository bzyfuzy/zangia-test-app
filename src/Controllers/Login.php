<?php

namespace App\Controllers;

use App\Models\User;

class Login
{
    public function get($req, $res)
    {
        $res->render("login");
    }

    public function post($request, $response)
    {
        $username = $request->getBodyField("username");
        $password = $request->getBodyField("password");
        if (User::login($username, $password)) {
            $response->redirect('/');
        } else {
            $response->render('login', ["error" => true]);
        }
    }
}
