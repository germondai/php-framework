<?php

declare(strict_types=1);

namespace App\Controller;

use eftec\bladeone\BladeOne;
use Utils\Helper;

abstract class Client extends Base
{
    protected \stdClass $template;

    protected BladeOne $blade;

    public function __construct()
    {
        # run parent construct
        parent::__construct();

        # define template var
        $this->template = new \stdClass();

        # create blade instance
        $views = Helper::getBasePath() . 'app/View';
        $cache = Helper::getBasePath() . 'temp';
        $this->blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
    }

    public function run(): void
    {
        # solve view
        $view = !empty($this->request[0]) ? $this->request[0] : 'index';

        # construct render fn
        $fn = 'render' . ucfirst($view);

        if (method_exists($this, $fn)) {
            # call render
            $this->$fn();

            # display view
            try {
                include_once Helper::getBasePath() . 'src/includes/header.php';
                echo $this->blade->run($view, (array) $this->template);
                include_once Helper::getBasePath() . 'src/includes/footer.php';
                return;
            } catch (\Exception $e) {
            }
        }

        echo "Page wasn't found!";
    }
}