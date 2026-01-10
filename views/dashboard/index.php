<?php include 'views/layouts/header.php'; ?>

<?php
session_start();
if (!empty($_SESSION['success_message'])):
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['success_message']);
endif;
?>

<h1 class="mb-4"><?= $title ?? 'Mitglieder-Dashboard' ?></h1>

<!-- =======================
     Diagramm
======================= -->
<div class="card mb-4">
    <div class="card-header fw-bold">Statusübersicht</div>
    <div class="card-body">
        <canvas id="memberChart" style="max-height:300px;"></canvas>
    </div>
</div>

<!-- =======================
     Karten-Übersicht
======================= -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-warning h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Neue Anträge</h5>
                <h2 class="text-warning"><?= $stats['pending'] ?? 0 ?>%</h2>
                <p class="text-muted">noch nicht bearbeitet</p>
            </div>
            <div class="progress rounded-0" style="height:6px;">
                <div class="progress-bar bg-warning" style="width:<?= $stats['pending'] ?? 0 ?>%;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-info h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Unvollständige Datensätze</h5>
                <h2 class="text-info"><?= $stats['incomplete'] ?? 0 ?>%</h2>
                <p class="text-muted">müssen ergänzt werden</p>
            </div>
            <div class="progress rounded-0" style="height:6px;">
                <div class="progress-bar bg-info" style="width:<?= $stats['incomplete'] ?? 0 ?>%;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-secondary h-100">
            <div class="card-body text-center">
                <h5 class="card-title">Nicht abgeschlossen</h5>
                <h2 class="text-secondary"><?= $stats['notfinished'] ?? 0 ?>%</h2>
                <p class="text-muted">Karte noch nicht verschickt</p>
            </div>
            <div class="progress rounded-0" style="height:6px;">
                <div class="progress-bar bg-secondary" style="width:<?= $stats['notfinished'] ?? 0 ?>%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- =======================
     Tabellen-Bereich
======================= -->
<div class="row">
    <!-- Neue Anträge -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark fw-bold">🟠 Neue Anträge</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr><th>Name</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pending as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['full_name'] ?? ($m['Prenom'].' '.$m['Nom'])) ?></td>
                            <td><?= htmlspecialchars($m['status_text'] ?? $m['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pending)): ?>
                        <tr><td colspan="2" class="text-center p-3">Keine neuen Anträge</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Unvollständige Datensätze -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-dark fw-bold">🔵 Unvollständige Datensätze</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr><th>Name</th><th>Fehlende Daten</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($incomplete as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['full_name'] ?? ($m['Prenom'].' '.$m['Nom'])) ?></td>
                            <td>
                                <?php if (!empty($m['missing_fields'])): ?>
                                    <?php foreach ($m['missing_fields'] as $field): ?>
                                        <span class="badge bg-danger me-1"><?= htmlspecialchars($field) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="badge bg-success">Vollständig</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($incomplete)): ?>
                        <tr><td colspan="2" class="text-center p-3">Alle Datensätze vollständig</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Noch nicht abgeschlossen -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-secondary shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold">⏳ Noch nicht abgeschlossen</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Karte</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notFinished as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['full_name'] ?? ($m['Prenom'].' '.$m['Nom'])) ?></td>
                            <td><?= htmlspecialchars($m['status_text'] ?? $m['status'] ?? '') ?></td>
                            <td>
                                <?php if ($m['card_sent'] ?? false): ?>
                                    <span class="badge bg-success">Versendet</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Fehlt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>member/edit/<?= $m['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($notFinished)): ?>
                        <tr><td colspan="3" class="text-center p-3">Alle Mitgliedskarten verschickt</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- =======================
     Chart.js Script
======================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('memberChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Neue Anträge', 'Unvollständige Datensätze', 'Nicht abgeschlossen'],
                datasets: [{
                    data: [
                        <?= $stats['pending'] ?? 0 ?>,
                        <?= $stats['incomplete'] ?? 0 ?>,
                        <?= $stats['notfinished'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(255,193,7,0.8)',
                        'rgba(13,202,240,0.8)',
                        'rgba(108,117,125,0.8)'
                    ],
                    borderColor: [
                        'rgba(255,193,7,1)',
                        'rgba(13,202,240,1)',
                        'rgba(108,117,125,1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: v => v + '%' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    });
</script>

<?php include 'views/layouts/footer.php'; ?>

