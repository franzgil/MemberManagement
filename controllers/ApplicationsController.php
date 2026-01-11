<?php

require_once __DIR__ . '/../models/Member.php';

class ApplicationsController
{
    public function manage()
    {
        $memberModel = new Member();
        $members = $memberModel->getMembersWithAddress();

        require_once __DIR__ . '/../views/applications/manage.php';
    }

    public function updateStatus()
    {
        if (!isset($_POST['member_id'])) {
            die("Missing member_id");
        }

        $id = (int)$_POST['member_id'];

        $memberModel = new Member();
        $memberModel->setFormStatusAccepted($id);
        $memberModel->setLegacyStatusAccepted($id);

        header("Location: /applications/manage");
        exit;
    }
}
