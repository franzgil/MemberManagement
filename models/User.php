<?php
class User
{
    private $users = [
        ['id' => 1, 'name' => 'Max Mustermann', 'email' => 'max@example.com'],
        ['id' => 2, 'name' => 'Anna Müller', 'email' => 'anna@example.com']
    ];

    public function getAll()
    {
        return $this->users;
    }

    public function getById($id)
    {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
}