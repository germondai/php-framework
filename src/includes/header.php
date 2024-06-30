<?php

# imports
use Utils\PageHelper;

# define fallback icon
PageHelper::setIcon("assets/img/favicon.ico");

# define fallback project name (this will be displayed in page title)
PageHelper::setProjectName("PHP Starter");

# define fallback metas
PageHelper::setMetas([
    "description" => "PHP Starter pack with Ready-to-Use functions like TailwindCSS",
    "keywords" => "PHP, Starter, Tailwind, CSS, TailwindCSS, Nette, Tracy, Env"
]);

# define styles
// PageHelper::setStyles(["assets/css/style.css"]);

?>

<!DOCTYPE html>
<html lang="<?= PageHelper::getLang() ?>">
<head>
    <?php
    PageHelper::renderCharset();
    PageHelper::renderMetas();
    PageHelper::renderTitle();
    PageHelper::renderIcon();
    PageHelper::renderStyles();
    ?>
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <link rel="stylesheet" href="http://localhost:5173/src/assets/css/style.css">
</head>
<body>
    <header></header>