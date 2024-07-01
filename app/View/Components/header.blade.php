<?php

# imports
use Utils\Helpers\PageHelper;

# define fallback icon
PageHelper::setIcon("assets/img/favicon.ico");

# define fallback project name (this will be displayed in page title)
PageHelper::setProjectName("PHP Full-Stack Framework");

# define fallback metas
PageHelper::setMetas([
    "description" => "PHP Full-Stack MVC Framework with Doctrine, Vite, and much more...",
    "keywords" => "api, php, template, jwt, crud, framework, orm, rest, mvc, migrations, hmr, doctrine, nette, blade, ts, entities, db, fullstack, tailwindcss, vite"
]);

# define assets
PageHelper::setAssets([
    'scss/app.scss',
    'js/main.js',
    'ts/app.ts',
]);

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
PageHelper::renderAssets();
?>
</head>
<body>
    <header></header>