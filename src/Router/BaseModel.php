<?php


namespace mickeySTRANGE\phpUtils\Router;


/**
 * Class BaseModel
 * @package mickeySTRANGE\NewBookNotice\FW\model
 */
abstract class BaseModel
{

  /**
   * called by PDO
   *
   * @param string $name
   * @param $value
   */
  public function __set(string $name, $value): void
  {
    $fieldName = $this->snakeToCamel($name);
    $this->$fieldName = $value;
  }

  /**
   * @param string $str
   * @return string
   */
  private function snakeToCamel(string $str): string
  {
    return lcfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
  }
}
