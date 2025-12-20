<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DesignType;

class HomeController extends Controller
{
    public function index()
    {
        $designTypes = DesignType::orderBy('created_at', 'asc')->get();

        return view('home', compact('designTypes'));
    }
}
