<?php

namespace mickeySTRANGE\phpUtils\Router;

use eftec\bladeone\BladeOne;
use Exception;

/**
 * Class BaseViewLogic
 */
abstract class BaseViewLogic
{

    private string $viewFileDirectory = "";

    protected array $controllerParam;
    protected array $viewData = [];

    /**
     * BaseViewLogic constructor.
     * @param array $controllerParam
     */
    public function __construct(array $controllerParam)
    {
        $this->controllerParam = $controllerParam;
    }

    protected function main()
    {
    }

    public function execute()
    {

        $this->preMain();
        $this->main();
        $this->postMain();

        $views = $this->viewFileDirectory;
        $cache = $this->viewFileDirectory . '/cache';

        preg_match('/\\\\([^\\\\]+)ViewLogic$/', get_class($this), $match);
        $view = $this->camelToSnake($match[1]);

        try {
            $token = openssl_random_pseudo_bytes(32);
            $tokenName = '_token';
            $_SESSION[$tokenName] = $token;

            $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
            $blade->csrf_token = $token;
            echo $blade->run($view, $this->viewData);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @param $str
     * @return string
     */
    public function camelToSnake($str): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', $str)), '_');
    }

    /**
     * @param string $viewFileDirectory
     */
    public function setViewFileDirectory(string $viewFileDirectory): void
    {
        $this->viewFileDirectory = $viewFileDirectory;
    }

    /**
     * 前処理
     */
    protected function preMain()
    {
    }

    /**
     * 後処理
     */
    protected function postMain()
    {
    }

}
