<?php

namespace mickeySTRANGE\phpUtils\Exceptions;

use Exception;

class RouterForwardHandleException extends Exception {

  private string $controller;

  /**
   * @return string
   */
  public function getController(): string {
    return $this->controller;
  }

  /**
   * @param string $controller
   */
  public function setController(string $controller): void {
    $this->controller = $controller;
  }
}
