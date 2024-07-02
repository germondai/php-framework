<?php

declare(strict_types=1);

namespace App\Controller;

use eftec\bladeone\BladeOne;
use Utils\Helpers\Helper;

abstract class Client extends Base
{
    protected \stdClass $template;
    protected BladeOne $blade;
    protected string $view;

    public function __construct()
    {
        # run parent construct
        parent::__construct();

        # define template var
        $this->template = new \stdClass();

        # define requested view
        $this->view = !empty($this->request[0]) ? $this->request[0] : 'index';

        # create blade instance
        $views = Helper::getBasePath() . 'app/View';
        $cache = Helper::getBasePath() . 'temp';
        $this->blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

        # add custom directives
        $this->blade->directive('link', function ($link) {
            return 'href="<?= \Utils\Helpers\Helper::link(' . $link . '); ?>"';
        });
    }

    protected function solveView(string $view): string
    {
        $controller = str_replace(__CLASS__ . '\\', '', get_class($this));
        $controller = $controller === 'Index' ? '' : $controller . '.';

        return 'Pages.' . $controller . $view;
    }

    public function run(): void
    {
        # construct render fn
        $fn = 'render' . ucfirst($this->view);

        if (method_exists($this, $fn)) {
            # call render
            $this->$fn();

            # solve blade view destination
            $view = $this->solveView($this->view);

            # display view
            try {
                echo $this->blade->run($view, (array) $this->template);
                return;
            } catch (\Exception $e) {
                # failed to load view
            }
        }

        # render error 404 page if view not found
        if ($this->view !== '404')
            $this->redirect('error/404');

        # if error 404 doesn't exist echo error
        echo "ERROR: Page wasn't found!";
    }

    public function redirect(string $destination): void
    {
        die(header('location: ' . Helper::link($destination)));
    }
}
