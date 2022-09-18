<?php

namespace mickeySTRANGE\phpUtils\Router;

use Exception;
use mickeySTRANGE\phpUtils\Config\Config;
use mickeySTRANGE\phpUtils\Exceptions\RouterForwardHandleException;


/**
 * Class BaseController
 */
abstract class BaseController
{

    private string $viewFileDirectory = "";
    private string $viewLogicClass = "";
    private array $viewLogicParam = [];

    private bool $redirected = false;

    protected string $formClass;
    protected BaseForm $form;

    abstract function main();

    /**
     * @throws RouterForwardHandleException
     */
    public function execute()
    {

        try {
            $this->preMain();
            if (!$this->isValidCsrfToken()) {
                $this->errorCsrfToken();
            }
            if (!$this->isValidParameter()) {
                $this->errorParameter();
            }

            $this->main();
            $this->postMain();

            $viewLogicName = $this->getViewLogicClass();
            if (!$this->redirected && !empty($viewLogicName)) {
                /** @var BaseViewLogic $viewLogic */
                $viewLogic = new $viewLogicName($this->viewLogicParam);
                $viewLogic->setViewFileDirectory($this->getViewFileDirectory());
                $viewLogic->execute();
            }

        } catch (Exception $e) {
            if ($e instanceof RouterForwardHandleException) {
                throw $e;
            }
            $this->catchException($e);
        }

        $this->end();
    }

    /**
     * フォワードする
     * @param $controllerName
     * @throws RouterForwardHandleException
     */
    protected function forward($controllerName)
    {
        if (Config::getIsUseOB()) {
            ob_clean();
        }
        $e = new RouterForwardHandleException();
        $e->setController($controllerName);
        throw $e;
    }

    /**
     * リダイレクトする
     * @param $url
     */
    protected function redirect($url)
    {

        if (Config::getIsUseOB()) {
            ob_clean();
        }
        $this->redirected = true;
        header('Location: ' . $url, true, 302);
    }

    /**
     * @return BaseForm
     */
    protected function getForm(): BaseForm
    {

        if (!isset($this->form)) {
            $formClass = $this->formClass;
            $this->form = new $formClass($_GET, $_POST);
        }
        return $this->form;
    }

    /**
     * 前処理
     */
    protected function preMain()
    {
    }

    /**
     * トークンチェックを実施
     *
     * @param string $tokenKey
     * @return bool
     */
    private function isValidCsrfToken($tokenKey = '_token'): bool
    {

        error_log(1);
        if (!$this->isCsrfTokenCheckController()) {
            return true;
        }

        error_log(2);
        if (@$_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log($_SERVER['REQUEST_METHOD']);
            return false;
        }

        error_log(3);
        if (!array_key_exists($tokenKey, $_POST) || !array_key_exists($tokenKey, $_SESSION)) {
            error_log(var_export($_POST, true));
            error_log(var_export($_SESSION, true));
            return false;
        }

        error_log(4);
        error_log($_POST[$tokenKey]);
        error_log($_SESSION[$tokenKey]);
        if (strlen($_POST[$tokenKey]) === 0 || strlen($_SESSION[$tokenKey]) === 0) {
            return false;
        }

        error_log(5);
        if ($_POST[$tokenKey] !== $_SESSION[$tokenKey]) {
            return false;
        }
        error_log(6);

        return true;
    }

    /**
     * トークンエラーになった場合の処理
     */
    protected function errorCsrfToken()
    {
    }

    /**
     * パラメータ入力チェックを実施
     *
     * @return bool
     */
    private function isValidParameter(): bool
    {

        if (!class_exists($this->formClass)) {
            return true;
        }

        if (method_exists($this->formClass, "isValidParameter")) {
            return true;
        }

        return $this->getForm()->isValidParameter();
    }

    /**
     * パラメータ入力チェックエラーになった場合の処理
     */
    protected function errorParameter()
    {
    }

    /**
     * 後処理
     */
    protected function postMain()
    {
    }

    /**
     * 終了処理
     */
    protected function end()
    {
    }

    /**
     * 例外処理
     * @param Exception $e
     */
    protected function catchException(Exception $e)
    {
    }

    /**
     * @return false
     */
    protected function isCsrfTokenCheckController(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getViewFileDirectory(): string
    {
        return $this->viewFileDirectory;
    }

    /**
     * @param string $viewFileDirectory
     */
    public function setViewFileDirectory(string $viewFileDirectory): void
    {
        $this->viewFileDirectory = $viewFileDirectory;
    }

    /**
     * @return string
     */
    public function getViewLogicClass(): string
    {
        return $this->viewLogicClass;
    }

    /**
     * @param string $viewLogicClass
     * @param array $viewLogicParam
     */
    public function setViewLogic(string $viewLogicClass, array $viewLogicParam = []): void
    {
        $this->viewLogicClass = $viewLogicClass;
        $this->viewLogicParam = $viewLogicParam;
    }

    /**
     * @param string $formClass
     */
    public function setFormClass(string $formClass): void
    {
        $this->formClass = $formClass;
    }
}
