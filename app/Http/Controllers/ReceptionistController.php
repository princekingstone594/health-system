<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ReceptionistController extends Controller
{
    public function dashboard()
    {
        $this->authorizeRole('receptionist');
        
        return view('receptionist.dashboard');
    }
}