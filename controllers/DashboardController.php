<?php
require_once 'models/Member.php';

class DashboardController extends Controller
{
    public function index()
    {
        $memberModel = new Member();

        $pending = $memberModel->getPendingApplications();
        $incomplete = $memberModel->getIncompleteData();
        $notFinished = $memberModel->getNotCompleted();
        $all = $memberModel->getAll();

        // Prozentberechnungen für grafische Anzeige
        $total = count($all) ?: 1;
        $percent_pending = round(count($pending) / $total * 100);
        $percent_incomplete = round(count($incomplete) / $total * 100);
        $percent_notfinished = round(count($notFinished) / $total * 100);

        $data = [
            'title' => 'Mitglieder-Dashboard',
            'pending' => $pending,
            'incomplete' => $incomplete,
            'notFinished' => $notFinished,
            'stats' => [
                'pending' => $percent_pending,
                'incomplete' => $percent_incomplete,
                'notfinished' => $percent_notfinished,
            ]
        ];

        $this->view('dashboard/index', $data);
    }
}

