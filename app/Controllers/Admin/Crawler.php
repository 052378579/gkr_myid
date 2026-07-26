<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Crawler extends BaseController
{
    public function index()
    {
        return view('admin/crawl_admin');
    }
}
