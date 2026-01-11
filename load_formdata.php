<?php
/**
 * Einfaches Importskript für wcf1_form_response -> members / member_address
 */

// === DB-Konfiguration ===
$dsn     = 'mysql:host=localhost;dbname=vid10000_afol55;charset=utf8mb4';
$dbUser  = 'vid10000_afol55';
$dbPass  = 'GibbesNz176*concept*';

// === Mapping JSON-Key -> Feldname ===
$fieldMap = [
    22 => 'lastname',
    23 => 'firstname',
    24 => 'birthdate',
    25 => 'nationality',
    26 => 'consent',
    27 => 'username',
    28 => 'street',
    29 => 'zip',
    30 => 'region',
    31 => 'city',
    32 => 'phone',
    33 => 'email'
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('DB-Verbindung fehlgeschlagen: ' . $e->getMessage());
}

// Hilfsfunktion: Log schreiben
function logImport(PDO $pdo, int $responseID, string $status, string $message = null): void {
    $stmt = $pdo->prepare("
        INSERT INTO import_log (responseID, status, message, created_at)
        VALUES (:responseID, :status, :message, NOW())
    ");
    $stmt->execute([
        ':responseID' => $responseID,
        ':status'     => $status,
        ':message'    => $message
    ]);
}

// Hole alle Antworten für Formular 3
$sql = "
    SELECT responseID, formID, userID, username, time, fields, status
    FROM wcf1_form_response
    WHERE formID = 3
";
$stmt = $pdo->query($sql);

$importCount   = 0;
$duplicateCount = 0;
$errorCount     = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $responseID = (int)$row['responseID'];

    // JSON decodieren
    $data = json_decode($row['fields'], true);
    if (!is_array($data)) {
        logImport($pdo, $responseID, 'error', 'JSON konnte nicht decodiert werden');
        $errorCount++;
        continue;
    }

    // Rohdaten in flaches Array mappen
    $mapped = [];
    foreach ($fieldMap as $key => $fieldName) {
        if (!array_key_exists($key, $data)) {
            $mapped[$fieldName] = null;
            continue;
        }

        $value = $data[$key];

        // multiSelect -> String
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        // Flag für consent
        if ($fieldName === 'consent') {
            $value = (int)!empty($value);
        }

        // Unicode-Flags / Sonderzeichen ggf. bereinigen
        if ($fieldName === 'nationality') {
            // Beispiel: "🇫🇷 Français " -> "Français"
            $value = preg_replace('/^[\x{1F1E6}-\x{1F1FF}]+\s*/u', '', (string)$value);
            $value = trim($value);
        }

        $mapped[$fieldName] = $value;
    }

    // Pflichtfelder prüfen (kannst du erweitern)
    $required = ['firstname', 'lastname', 'email'];
    $missing  = [];
    foreach ($required as $field) {
        if (empty($mapped[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        logImport($pdo, $responseID, 'error', 'Fehlende Pflichtfelder: ' . implode(', ', $missing));
        $errorCount++;
        continue;
    }

    // Doppelte E-Mail prüfen
    $checkStmt = $pdo->prepare("SELECT id FROM members WHERE email = :email");
    $checkStmt->execute([':email' => $mapped['email']]);
    $existingMember = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingMember) {
        logImport($pdo, $responseID, 'duplicate_email', 'E-Mail bereits vorhanden: ' . $mapped['email']);
        $duplicateCount++;
        continue;
    }

    // Import in einer Transaktion
    try {
        $pdo->beginTransaction();

        // Member einfügen
        $memberStmt = $pdo->prepare("
            INSERT INTO members
                (woltlab_user_id, firstname, lastname, birthdate, nationality,
                 username, email, phone, consent, form_status, created_at, updated_at)
            VALUES
                (:woltlab_user_id, :firstname, :lastname, :birthdate, :nationality,
                 :username, :email, :phone, :consent, :form_status, NOW(), NOW())
        ");

        $memberStmt->execute([
            ':woltlab_user_id' => $row['userID'] ?: null,
            ':firstname'       => $mapped['firstname'],
            ':lastname'        => $mapped['lastname'],
            ':birthdate'       => !empty($mapped['birthdate']) ? $mapped['birthdate'] : null,
            ':nationality'     => $mapped['nationality'],
            ':username'        => $mapped['username'] ?: $row['username'],
            ':email'           => $mapped['email'],
            ':phone'           => $mapped['phone'],
            ':consent'         => $mapped['consent'] ?? 0,
            ':form_status'     => $row['status'] ?: null
        ]);

        $memberId = (int)$pdo->lastInsertId();

        // Adresse einfügen
        $addrStmt = $pdo->prepare("
            INSERT INTO member_address
                (member_id, street, zip, city, country, region)
            VALUES
                (:member_id, :street, :zip, :city, :country, :region)
        ");

        $addrStmt->execute([
            ':member_id' => $memberId,
            ':street'    => $mapped['street'] ?? '',
            ':zip'       => $mapped['zip'] ?? '',
            ':city'      => $mapped['city'] ?? '',
            ':country'   => $mapped['country'] ?? 'UNKNOWN',
            ':region'    => $mapped['region'] ?? null
        ]);

        $pdo->commit();

        logImport($pdo, $responseID, 'success', 'Import erfolgreich, member_id=' . $memberId);
        $importCount++;

    } catch (Exception $e) {
        $pdo->rollBack();
        logImport($pdo, $responseID, 'error', 'Exception: ' . $e->getMessage());
        $errorCount++;
        continue;
    }
}

echo "Fertig.\n";
echo "Importiert:   {$importCount}\n";
echo "Duplikate:    {$duplicateCount}\n";
echo "Fehler:       {$errorCount}\n";
