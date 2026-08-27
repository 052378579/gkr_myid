<?php

namespace App\Controllers;

class TrendController extends BaseController
{
    public function index(): string
    {
        return view('trend');
    }
}
