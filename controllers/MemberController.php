<?php
require_once 'models/Member.php';

class MemberController extends Controller
{
    public function edit($id)
    {
        $memberModel = new Member();
        $member = $memberModel->getById($id);
        if (!$member) {
            http_response_code(404);
            die("Mitglied nicht gefunden");
        }
        $this->view('member/edit', [
            'member' => $member,
            'title' => 'Mitglied bearbeiten'
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Member();

            $data = [
                'Membre'     => $_POST['Membre'] ?? '',
                'Nom'        => $_POST['Nom'] ?? '',
                'Prenom'     => $_POST['Prenom'] ?? '',
                'Username'   => $_POST['Username'] ?? '',
                'E-Mail'     => $_POST['E-Mail'] ?? '',
                'Telephone'  => $_POST['Telephone'] ?? '',
                'Date de naissance' => $_POST['Date_de_naissance'] ?? null,
                'Matricule'  => $_POST['Matricule'] ?? '',
                'Lieu de naissance' => $_POST['Lieu_de_naissance'] ?? '',
                'Pays de naissance' => $_POST['Pays_de_naissance'] ?? '',
                'Numero'     => $_POST['Numero'] ?? '',
                'Rue'        => $_POST['Rue'] ?? '',
                'Code postal'=> $_POST['Code_postal'] ?? '',
                'Localite'   => $_POST['Localite'] ?? '',
                'Pays'       => $_POST['Pays'] ?? '',
                'Cot Comite' => $_POST['Cot_Comite'] ?? 0,
                'Cot 2024'   => $_POST['Cot_2024'] ?? 0,
                'Cot 2025'   => $_POST['Cot_2025'] ?? 0,
                'Cot 2026'   => $_POST['Cot_2026'] ?? 0,
                'cartemembre_delivre' => $_POST['cartemembre_delivre'] ?? '',
                'status'     => $_POST['status'] ?? 'in_bearbeitung'
            ];

            $model->updateMemberFull($id, $data);

            // ✅ Erfolgsmeldung via Session
            session_start();
            $_SESSION['success_message'] = "Änderungen erfolgreich gespeichert!";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
    }
}