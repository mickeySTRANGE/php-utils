<?php

namespace mickeySTRANGE\phpUtils\SimpleLogger;

use mickeySTRANGE\phpUtils\GeneralUtils\Constants;
use mickeySTRANGE\phpUtils\GeneralUtils\DirectoryUtils;

/**
 * Class SimpleLogger
 * @package mickeySTRANGE\phpUtils\SimpleLogger
 */
class SimpleLogger {

  private const LOG_LEVEL_STR
    = [
      LOG_ERR     => "ERR",
      LOG_WARNING => "WARN",
      LOG_INFO    => "INFO"
    ];

  private static string $logFilePath = "work/defaultlog.log";


  /**
   * @param string $logFilePath
   */
  public static function setLogFilePath(string $logFilePath) {
    $callFile = debug_backtrace()[0]["file"];
    $callDir = DirectoryUtils::getParentDirectory($callFile);
    $targetLogFile = $callDir . "/" . $logFilePath;
    if (!is_file($targetLogFile)) {
      mkdir(DirectoryUtils::getParentDirectory($targetLogFile), 0777, true);
      file_put_contents($targetLogFile, "");
    }
    self::$logFilePath = $targetLogFile;
  }

  /**
   * @param string $message
   */
  public static function info(string $message) {

    self::_appendLog(LOG_INFO, $message);
  }

  /**
   * @param string $message
   */
  public static function err(string $message) {

    self::_appendLog(LOG_ERR, $message);
  }

  /**
   * @param int    $logLevel
   * @param string $message
   */
  private static function _appendLog(int $logLevel, string $message) {
    $date = date('Y-m-d H:i:s');
    $pid = getmypid();
    $logLevelStr = self::LOG_LEVEL_STR[$logLevel];

    $callFile = debug_backtrace()[1]["file"];
    $callDir = DirectoryUtils::getParentDirectory($callFile);
    $fileName = substr($callFile, strlen($callDir));

    $message = "$date [$pid]: $logLevelStr [$fileName]$message";

    file_put_contents(self::$logFilePath, $message . Constants::CRLF, FILE_APPEND);
  }

}
