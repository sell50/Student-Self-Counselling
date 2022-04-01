<?php

class HomeController extends Controller
{
    public function get(): Response
    {
        return $this->render('index', [
            'majors' => Major::all(),
        ]);
    }
}
