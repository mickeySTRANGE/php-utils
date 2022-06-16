<?php

namespace mickeySTRANGE\phpUtils\GeneralUtils;

/**
 * Class DirectoryUtils
 * @package mickeySTRANGE\phpUtils
 */
class DirectoryUtils {

  /**
   * @param $path
   * @return false|string
   */
  public static function getParentDirectory($path): bool|string {
    return substr($path, 0, strrpos($path, "/"));
  }

}
