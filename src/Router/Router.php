<?php

namespace mickeySTRANGE\phpUtils\Router;

use mickeySTRANGE\phpUtils\Exceptions\RouterException;
use mickeySTRANGE\phpUtils\Config\Config;


/**
 * Class Router
 */
class Router
{

  private string $defaultController = "";
  private string $controllerNamespace = "";
  private string $formNamespace = "";
  private string $viewFileDirectory = "";

  /**
   * execute
   *
   * @throws RouterException
   */
  public function execute()
  {

    if (Config::getIsUseOB()) {
      ob_start();
    }

    preg_match("/(https?:\/\/|)([^\/?#]+|)([^?#]*)(\??[^#]*)(#?.*)/", $_SERVER['REQUEST_URI'], $matches);
    $path = $matches[3];
    if (strrpos($path, '/') != 0) {
      throw new RouterException();
    }

    $controllerName = strlen($path) > 1 ? substr($path, 1) : $this->defaultController;
    $controllerClass = $this->controllerNamespace . '\\' . $controllerName . "Controller";
    $formClass = $this->formNamespace . '\\' . $controllerName . "Form";

    /** @var BaseController $controller */
    $controller = new $controllerClass;
    $controller->setViewFileDirectory($this->viewFileDirectory);
    $controller->setFormClass($formClass);
    $controller->execute();
  }

  /**
   * @param string $defaultController
   */
  public function setDefaultController(string $defaultController): void
  {
    $this->defaultController = $defaultController;
  }

  /**
   * @param string $viewFileDirectory
   */
  public function setViewFileDirectory(string $viewFileDirectory): void
  {
    $this->viewFileDirectory = $viewFileDirectory;
  }

  /**
   * @param string $controllerNamespace
   */
  public function setControllerNamespace(string $controllerNamespace): void
  {
    $this->controllerNamespace = $controllerNamespace;
  }

  /**
   * @param string $formNamespace
   */
  public function setFormNamespace(string $formNamespace): void
  {
    $this->formNamespace = $formNamespace;
  }
}
