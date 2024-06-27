<?php

# imports
use Utils\PageHelper;

# require config
require_once __DIR__ . '/config.php';

# define scripts
PageHelper::setScripts([
    "assets/js/jquery-3.7.1.min.js",
    "assets/js/main.js"
]);

?>
        
        <footer></footer>
        <?php
        PageHelper::renderScripts();
        ?>
    </body>
</html>