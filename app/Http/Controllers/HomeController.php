<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $data['userType']="User";
        return view('home',$data);
    }

    public function adminHome(){
        $data['userType']="Admin";
        return view('home',$data);
    }
    public function managerHome(){
        $data['userType']="Manager";

        return view('home',$data);
    }
}
