<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Student Management System'
        ];
        return view('landing', $data);
    }
}
