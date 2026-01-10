<?php require_once 'config/config.php'; ?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Meine Webapp' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>">WebApp</a>
        <div class="navbar-nav">
            <a class="nav-link" href="<?= BASE_URL ?>">Home</a>
            <a class="nav-link" href="<?= BASE_URL ?>user">Benutzer</a>
            <a class="nav-link" href="<?= BASE_URL ?>dashboard">Dashboard</a>
        </div>
    </div>
</nav>
<div class="container mt-4">