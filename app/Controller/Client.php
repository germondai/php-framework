<?php

declare(strict_types=1);

namespace App\Controller;

use eftec\bladeone\BladeOne;
use Utils\Helpers\Helper;

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

            $this->blade->directive('link', function ($link) {
                return 'href="<?= \Utils\Helpers\Helper::link(' . $link . '); ?>"';
            });

            # solve blade view destination
            $controller = str_replace(__CLASS__ . '\\', '', get_class($this));
            $controller = $controller === 'Index' ? '' : $controller . '.';
            $view = 'Pages.' . $controller . $view;

            # display view
            try {
                echo $this->blade->run($view, (array) $this->template);
                return;
            } catch (\Exception $e) {
            }
        }

        echo "Page wasn't found!";
    }
}
