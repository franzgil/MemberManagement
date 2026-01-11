<?php
require_once 'config/database.php';
class Member
{
    private $db;

    private function enrichMembers($rows)
    {
        foreach ($rows as &$m) {

            // Vollständiger Name
            $m['full_name'] = trim($m['firstname'] . ' ' . $m['lastname']);

            // Status-Text (PHP 7 kompatibel)
            if ($m['status'] === 'accepted') {
                $m['status_text'] = '🟢 Angenommen';
            } elseif ($m['status'] === 'declined') {
                $m['status_text'] = '🔴 Abgelehnt';
            } else {
                $m['status_text'] = '🟠 Unbekannt';
            }

            // Fehlende Felder prüfen
            $missing = [];
            if (empty($m['email']))      $missing[] = 'E-Mail';
            if (empty($m['phone']))      $missing[] = 'Telefon';
            if (empty($m['birthdate']))  $missing[] = 'Geburtsdatum';
            if (empty($m['nationality'])) $missing[] = 'Nationalität';

            $m['missing_fields'] = $missing;
            $m['missing_count']  = count($missing);

            // Karte verschickt? (members hat kein Feld dafür)
            $m['card_sent'] = false;
        }

        return $rows;
    }


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

    public function getMembersWithAddress()
    {
        $sql = "
        SELECT 
            m.id,
            m.firstname,
            m.lastname,
            m.email,
            m.phone,
            m.form_status,
            a.street,
            a.zip,
            a.city,
            a.country
        FROM members m
        LEFT JOIN member_address a ON a.member_id = m.id
        ORDER BY m.lastname ASC
    ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setFormStatusAccepted($id)
    {
        $sql = "UPDATE members SET form_status = 'accepted' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    public function setLegacyStatusAccepted($id)
    {
        $sql = "UPDATE gf_membres SET status = 'accepted' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }





    // 🟠 Mitglieder mit noch keinem Beitrag (neuer Antrag)
    public function getPendingApplications()
    {
        $sql = "
        SELECT 
            id,
            firstname,
            lastname,
            email,
            phone,
            birthdate,
            nationality,
            form_status AS status,
            created_at
        FROM members
        WHERE form_status != 'accepted' OR form_status IS NULL
        ORDER BY created_at DESC
        
    ";

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->enrichMembers($rows);
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



