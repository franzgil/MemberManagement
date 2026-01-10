<?php
require_once 'models/User.php';

class UserController extends Controller
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAll();

        $data = [
            'title' => 'Benutzerliste',
            'users' => $users
        ];

        $this->view('user/index', $data);
    }

    public function show($id)
    {
        $userModel = new User();
        $user = $userModel->getById($id);

        if (!$user) {
            http_response_code(404);
            die("Benutzer nicht gefunden.");
        }

        $this->view('user/index', [
            'title' => 'Benutzer anzeigen',
            'users' => [$user]
        ]);
    }
}