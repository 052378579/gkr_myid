<?php

namespace App\Controllers;

class GoController extends BaseController
{
    public function erp($kode)
    {
        return redirect()->to("http://103.39.49.86:82/desk#Form/Item/" . $kode);
    }
}

