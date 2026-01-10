<?php include 'views/layouts/header.php'; ?>

    <div class="p-4 bg-primary text-white rounded">
        <h1><?= $title ?></h1>
        <p><?= $message ?></p>
        <a class="btn btn-light" href="<?= BASE_URL ?>user">Zur Benutzerseite</a>
    </div>

<?php include 'views/layouts/footer.php'; ?>