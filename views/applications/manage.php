<div class="container mt-4">
    <h2>Mitgliederverwaltung</h2>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Telefon</th>
            <th>Adresse</th>
            <th>Status</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['firstname'] . ' ' . $m['lastname']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= htmlspecialchars($m['phone']) ?></td>
                <td>
                    <?= htmlspecialchars($m['street']) ?><br>
                    <?= htmlspecialchars($m['zip'] . ' ' . $m['city']) ?><br>
                    <?= htmlspecialchars($m['country']) ?>
                </td>
                <td>
                    <?= $m['form_status'] ?: '—' ?>
                </td>
                <td>
                    <?php if ($m['form_status'] !== 'accepted'): ?>
                        <form method="post" action="members/updateStatus">
                            <input type="hidden" name="member_id" value="<?= $m['id'] ?>">
                            <button class="btn btn-success btn-sm">Akzeptieren</button>
                        </form>
                    <?php else: ?>
                        <span class="text-success fw-bold">Akzeptiert</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
