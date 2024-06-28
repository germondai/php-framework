<?php

declare(strict_types=1);

namespace App\Controller;

use Utils\Helper;

abstract class Client extends Base
{
    protected \stdClass $template;

    public function run(): void
    {
        # define template var
        $this->template = new \stdClass();

        # solve view
        $view = !empty($this->request[0]) ? $this->request[0] : 'index';

        # construct render fn
        $fn = 'render' . ucfirst($view);

        if (method_exists($this, $fn)) {
            # call render
            $this->$fn();

            # extract template vars
            extract((array) $this->template);

            # display view
            include_once Helper::getBasePath() . 'src/includes/header.php';
            include_once Helper::getBasePath() . "app/View/{$view}.php";
            include_once Helper::getBasePath() . 'src/includes/footer.php';
        } else
            echo "Page wasn't found!";
    }
}