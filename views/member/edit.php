<?php include 'views/layouts/header.php'; ?>

    <h2 class="mb-4"><?= $title ?></h2>

    <form method="post"
          action="<?= BASE_URL ?>member/update/<?= htmlspecialchars($member['id']) ?>"
          class="needs-validation" novalidate>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Mitgliedstyp</label>
                <input type="text" name="Membre" class="form-control" value="<?= htmlspecialchars($member['Membre'] ?? '') ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php
                    $statuses = ['beantragt'=>'Beantragt','in_bearbeitung'=>'In Bearbeitung','bearbeitet'=>'Bearbeitet','abgeschlossen'=>'Abgeschlossen'];
                    foreach ($statuses as $key=>$label): ?>
                        <option value="<?= $key ?>" <?= ($member['status']===$key)?'selected':'' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Karte geliefert (O = Ja)</label>
                <input type="text" name="cartemembre_delivre" class="form-control"
                       value="<?= htmlspecialchars($member['cartemembre_delivre'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Vorname</label>
                <input type="text" name="Prenom" class="form-control" value="<?= htmlspecialchars($member['Prenom'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nachname</label>
                <input type="text" name="Nom" class="form-control" value="<?= htmlspecialchars($member['Nom'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="Username" class="form-control" value="<?= htmlspecialchars($member['Username'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">E-Mail</label>
                <input type="email" name="E-Mail" class="form-control" value="<?= htmlspecialchars($member['E-Mail'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Telefon</label>
                <input type="text" name="Telephone" class="form-control" value="<?= htmlspecialchars($member['Telephone'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Geburtsdatum</label>
                <input type="date" name="Date_de_naissance" class="form-control" value="<?= htmlspecialchars($member['Date de naissance'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Matricule</label>
                <input type="text" name="Matricule" class="form-control" value="<?= htmlspecialchars($member['Matricule'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Geburtsort</label>
                <input type="text" name="Lieu_de_naissance" class="form-control" value="<?= htmlspecialchars($member['Lieu de naissance'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Geburtsland</label>
                <input type="text" name="Pays_de_naissance" class="form-control" value="<?= htmlspecialchars($member['Pays de naissance'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 mb-3">
                <label class="form-label">Nr.</label>
                <input type="text" name="Numero" class="form-control" value="<?= htmlspecialchars($member['Numero'] ?? '') ?>">
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">Straße</label>
                <input type="text" name="Rue" class="form-control" value="<?= htmlspecialchars($member['Rue'] ?? '') ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">PLZ</label>
                <input type="text" name="Code_postal" class="form-control" value="<?= htmlspecialchars($member['Code postal'] ?? '') ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Ort</label>
                <input type="text" name="Localite" class="form-control" value="<?= htmlspecialchars($member['Localite'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Land</label>
                <input type="text" name="Pays" class="form-control" value="<?= htmlspecialchars($member['Pays'] ?? '') ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Cot Comite</label>
                <input type="number" name="Cot_Comite" class="form-control" value="<?= htmlspecialchars($member['Cot Comite'] ?? 0) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Cot 2024</label>
                <input type="number" name="Cot_2024" class="form-control" value="<?= htmlspecialchars($member['Cot 2024'] ?? 0) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Cot 2025</label>
                <input type="number" name="Cot_2025" class="form-control" value="<?= htmlspecialchars($member['Cot 2025'] ?? 0) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Cot 2026</label>
                <input type="number" name="Cot_2026" class="form-control" value="<?= htmlspecialchars($member['Cot 2026'] ?? 0) ?>">
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between">
            <a href="<?= BASE_URL ?>dashboard" class="btn btn-secondary">Abbrechen</a>
            <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        </div>
    </form>

<?php include 'views/layouts/footer.php'; ?>