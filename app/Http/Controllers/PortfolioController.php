<?php

namespace App\Http\Controllers;

use App\Models\Portfolio; // Pastikan ini ada
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        // 1. Ambil data dari database
        $portfolios = Portfolio::latest()->get(); 
        
        // 2. Kirim variabel 'portfolios' ke view
        return view('portfolio', compact('portfolios')); 
    }
}