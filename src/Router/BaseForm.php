<?php

namespace mickeySTRANGE\phpUtils\Router;


abstract class BaseForm {

  private array $getParam;
  private array $postParam;

  private bool $overrideWithPostParameter = true;

  /**
   * BaseForm constructor.
   * @param array $getParam
   * @param array $postParam
   */
  public function __construct(array $getParam, array $postParam) {
    $this->getParam = $getParam;
    $this->postParam = $postParam;
  }

  /**
   * @param $name
   * @return string|array|null
   */
  protected function getParam($name) {

    $get = array_key_exists($name, $this->getParam) ? $this->getParam[$name] : null;
    $post = array_key_exists($name, $this->postParam) ? $this->postParam[$name] : null;

    if ($post !== null) {
      return $post;
    } else {
      return $get;
    }
  }
}
