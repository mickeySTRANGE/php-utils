<?php


namespace mickeySTRANGE\phpUtils\Config;


/**
 * Class Config
 * @package mickeySTRANGE\phpUtils\Config
 */
class Config
{

  private static array $config = [];

  const CONFIG_PREFIX = 'mickeySTRANGE.phpUtils.';

  /**
   * @param string $path
   */
  public static function loadIniFile(string $path)
  {
    self::$config = array_merge(self::$config, parse_ini_file($path));
  }

  /**
   * @return bool
   */
  public static function getIsUseOB(): bool
  {
    return (bool)self::getConfig('php.isUseOB', '1');
  }

  /**
   * @return string
   */
  public static function getDbms(): string
  {
    return self::getConfig('db.dbms');
  }

  /**
   * @return string
   */
  public static function getPgsqlHost(): string
  {
    return self::getConfig('pgsql.host');
  }

  /**
   * @return string
   */
  public static function getPgsqlDbname(): string
  {
    return self::getConfig('pgsql.dbname');
  }

  /**
   * @return string
   */
  public static function getPgsqlUser(): string
  {
    return self::getConfig('pgsql.user');
  }

  /**
   * @return string
   */
  public static function getPgsqlPass(): string
  {
    return self::getConfig('pgsql.pass');
  }

  /**
   * @param string $key
   * @param string $default
   * @return string
   */
  private static function getConfig(string $key, string $default = ''): string
  {
    $key = self::CONFIG_PREFIX . $key;

    if (array_key_exists($key, self::$config)) {
      return self::$config[$key];
    }

    $fromIni = ini_get($key);
    if ($fromIni !== false) {
      self::$config[$key] = $fromIni;
      return $fromIni;
    }

    self::$config[$key] = $default;
    return $default;
  }
}
