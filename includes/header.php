<?php require_once __DIR__ . '/config.php'; ?>

<!DOCTYPE html>
<html lang="<?= $_GET["page"]["lang"] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $_GET["page"]["description"] ?? 'PHP Starter' ?>">
    <meta name="keywords" content="<?= $_GET["page"]["keywords"] ?? 'PHP, Starter' ?>">
    <title><?= !empty($_GET["page"]["title"]) ? $_GET["page"]["title"] . " | PHP Starter" : "PHP Starter" ?></title>
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="<?= $linkPath . "assets/css/style.css" ?>">
    <?php
    foreach ($_GET["page"]["css"] ?? [] as $css) {
        echo '<link href="' . $linkPath . $css . '" rel="stylesheet">';
    }
    ?>
</head>
<body>