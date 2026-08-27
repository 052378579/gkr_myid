<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class VersiController extends BaseController
{
    public function index()
    {
        return view('admin/versi_admin', ['version' => $this->getAppVersion()]);
    }
}
