<?php


namespace mickeySTRANGE\phpUtils\Utils;

/**
 * Class StringUtils
 * @package mickeySTRANGE\phpUtils\GeneralUtils
 */
class StringUtils
{

  /**
   * @param string $string
   * @return string
   */
  public static function addQuoteForCsv(string $string): string
  {
    return '"' . str_replace('"', '""', $string) . '"';
  }

  /**
   * @param $path
   * @return false|string
   */
  public static function getParentDirectory($path): bool|string
  {
    return substr($path, 0, strrpos($path, "/"));
  }

}
