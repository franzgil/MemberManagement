<?php
class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Willkommen auf der Startseite',
            'message' => 'Dies ist eine einfache MVC-Struktur mit PHP & Bootstrap.'
        ];

        $this->view('home/index', $data);
    }
}