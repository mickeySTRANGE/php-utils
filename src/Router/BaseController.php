<?php

namespace mickeySTRANGE\phpUtils\Router;

use Exception;
use mickeySTRANGE\phpUtils\Config\Config;


/**
 * Class BaseController
 */
abstract class BaseController {

  private string $viewFileDirectory = "";
  private string $viewLogicClass = "";
  private array $viewLogicParam = [];

  private bool $redirected = false;

  protected string $formClass;
  protected BaseForm $form;

  abstract function main();

  public function execute() {

    try {
      $this->preMain();
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
      $this->catchException($e);
    }

    $this->end();
  }

  /**
   * リダイレクトする
   * @param $url
   */
  protected function redirect($url) {

    if (Config::getIsUseOB()) {
      ob_clean();
    }
    $this->redirected = true;
    header('Location: ' . $url, true, 302);
  }

  /**
   * @return BaseForm
   */
  protected function getForm(): BaseForm {

    if (!isset($this->form)) {
      $formClass = $this->formClass;
      $this->form = new $formClass($_GET, $_POST);
    }
    return $this->form;
  }

  /**
   * 前処理
   */
  protected function preMain() {
  }

  /**
   * 後処理
   */
  protected function postMain() {
  }

  /**
   * 終了処理
   */
  protected function end() {
  }

  /**
   * 例外処理
   * @param Exception $e
   */
  protected function catchException(Exception $e) {
  }

  /**
   * @return string
   */
  public function getViewFileDirectory(): string {
    return $this->viewFileDirectory;
  }

  /**
   * @param string $viewFileDirectory
   */
  public function setViewFileDirectory(string $viewFileDirectory): void {
    $this->viewFileDirectory = $viewFileDirectory;
  }

  /**
   * @return string
   */
  public function getViewLogicClass(): string {
    return $this->viewLogicClass;
  }

  /**
   * @param string $viewLogicClass
   * @param array  $viewLogicParam
   */
  public function setViewLogic(string $viewLogicClass, array $viewLogicParam = []): void {
    $this->viewLogicClass = $viewLogicClass;
    $this->viewLogicParam = $viewLogicParam;
  }

  /**
   * @param string $formClass
   */
  public function setFormClass(string $formClass): void {
    $this->formClass = $formClass;
  }
}
