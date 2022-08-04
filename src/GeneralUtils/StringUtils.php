<?php


namespace mickeySTRANGE\phpUtils\GeneralUtils;

/**
 * Class StringUtils
 * @package mickeySTRANGE\phpUtils\GeneralUtils
 */
class StringUtils {

  /**
   * @param string $string
   * @return string
   */
  public static function addQuoteForCsv(string $string): string {
    return '"' . str_replace('"', '""', $string) . '"';
  }

}
