<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        return view('admin.settings');
    }
}
