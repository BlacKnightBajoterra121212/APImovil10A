<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class rutasController extends Controller
{
    public function dashboardAdmin()
    {
        return view('dashboard.admin');
    }
}
