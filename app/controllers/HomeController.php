<?php

require_once "../app/core/Controller.php";

class HomeController extends Controller
{

    public function index()
    {
        $this->view('home/index');
    }

    public function about()
    {
        $this->view('home/about');
    }

    public function contact()
    {
        $this->view('home/contact');
    }

}