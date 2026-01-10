<?php
require_once 'config/database.php';
class Member
{
    private $db;

    private function enrich($rows)
    {
        $list = [];

        foreach ($rows as $row) {

            // 🧾 Vollständiger Name
            $row['full_name'] = trim(($row['Prenom'] ?? '') . ' ' . ($row['Nom'] ?? ''));

            // 💳 Karte verschickt?
            $row['card_sent'] = ($row['cartemembre_delivre'] === 'O');

            // 🟩 hübscher Text / Badge
            $row['card_status'] = $row['card_sent'] ? '✅ Karte verschickt' : '❌ Fehlt';

            // 🧭 Text für Status–Anzeige
            $statusMap = [
                'beantragt'      => '🟠 Beantragt',
                'in_bearbeitung' => '🔵 In Bearbeitung',
                'bearbeitet'     => '🟡 Bearbeitet',
                'abgeschlossen'  => '🟢 Abgeschlossen'
            ];
            $row['status_text'] = $statusMap[$row['status']] ?? 'Unbekannt';


            // 🧩 Fehlende Felder erkennen
            $missing = [];
            if (empty($row['E-Mail']))           $missing[] = 'E-Mail';
            if (empty($row['Telephone']))        $missing[] = 'Telefon';
            if (empty($row['Rue']))              $missing[] = 'Straße';
            if (empty($row['Code postal']))      $missing[] = 'Postleitzahl';
            if (empty($row['Localite']))         $missing[] = 'Ort';
            if (empty($row['Pays']))             $missing[] = 'Land';
            if (empty($row['Date de naissance'])
                || $row['Date de naissance'] === '1900-01-00') $missing[] = 'Geburtsdatum';

            $row['missing_fields'] = $missing;
            $row['missing_count']  = count($missing);

            $list[] = $row;
        }

        return $list;
    }

    public function __construct()
    {
        $this->db = Database::getConnection(); // ✅ sichere Verbindung holen
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM gf_membres");
        return $this->enrich($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // 🟠 Mitglieder mit noch keinem Beitrag (neuer Antrag)
    public function getPendingApplications()
    {
        $stmt = $this->db->query("SELECT * FROM gf_membres WHERE status = 'beantragt'");
        return $this->enrich($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // 🔵 Mitglieder mit unvollständigen Daten
    public function getIncompleteData()
    {
        $stmt = $this->db->query("SELECT * FROM gf_membres");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $enriched = $this->enrich($rows);

        // nur Datensätze mit fehlenden Feldern behalten
        return array_filter($enriched, fn($m) => $m['missing_count'] > 0);
    }

    // ⚫ Mitglieder, bei denen die Karte noch nicht verschickt wurde
    public function getNotCompleted()
    {
        $stmt = $this->db->query("
            SELECT * FROM gf_membres
            WHERE (`cartemembre_delivre` IS NULL
               OR `cartemembre_delivre` = ''
               OR `cartemembre_delivre` <> 'O')
        ");
        return $this->enrich($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM gf_membres WHERE id = ?");
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $enriched = $this->enrich($rows);
        return $enriched[0] ?? null;
    }

    public function updateMember($id, $data)
    {
        $sql = "UPDATE gf_membres 
            SET `E-Mail` = :email, 
                `Telephone` = :phone, 
                `Rue` = :street, 
                `Code postal` = :postal, 
                `Localite` = :city, 
                `Pays` = :country,
                `status` = :status
            WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email'   => $data['E-Mail'],
            ':phone'   => $data['Telephone'],
            ':street'  => $data['Rue'],
            ':postal'  => $data['Code postal'],
            ':city'    => $data['Localite'],
            ':country' => $data['Pays'],
            ':status'  => $data['status'],
            ':id'      => $id
        ]);
    }

    public function updateMemberFull($id, $data)
    {
        $sql = "UPDATE gf_membres SET 
        `Membre` = :Membre,
        `Nom` = :Nom,
        `Prenom` = :Prenom,
        `Username` = :Username,
        `E-Mail` = :Email,
        `Telephone` = :Telephone,
        `Date de naissance` = :DateNaissance,
        `Matricule` = :Matricule,
        `Lieu de naissance` = :LieuNaissance,
        `Pays de naissance` = :PaysNaissance,
        `Numero` = :Numero,
        `Rue` = :Rue,
        `Code postal` = :CodePostal,
        `Localite` = :Localite,
        `Pays` = :Pays,
        `Cot Comite` = :CotComite,
        `Cot 2024` = :Cot2024,
        `Cot 2025` = :Cot2025,
        `Cot 2026` = :Cot2026,
        `cartemembre_delivre` = :Carte,
        `status` = :Status
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':Membre' => $data['Membre'],
            ':Nom' => $data['Nom'],
            ':Prenom' => $data['Prenom'],
            ':Username' => $data['Username'],
            ':Email' => $data['E-Mail'],
            ':Telephone' => $data['Telephone'],
            ':DateNaissance' => $data['Date de naissance'],
            ':Matricule' => $data['Matricule'],
            ':LieuNaissance' => $data['Lieu de naissance'],
            ':PaysNaissance' => $data['Pays de naissance'],
            ':Numero' => $data['Numero'],
            ':Rue' => $data['Rue'],
            ':CodePostal' => $data['Code postal'],
            ':Localite' => $data['Localite'],
            ':Pays' => $data['Pays'],
            ':CotComite' => $data['Cot Comite'],
            ':Cot2024' => $data['Cot 2024'],
            ':Cot2025' => $data['Cot 2025'],
            ':Cot2026' => $data['Cot 2026'],
            ':Carte' => $data['cartemembre_delivre'],
            ':Status' => $data['status'],
            ':id' => $id
        ]);
    }
}



