<?php include 'views/layouts/header.php'; ?>

    <h2><?= $title ?></h2>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php include 'views/layouts/footer.php'; ?>