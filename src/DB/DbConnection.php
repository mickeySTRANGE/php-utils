<?php


namespace mickeySTRANGE\phpUtils\DB;


use mickeySTRANGE\phpUtils\Config\Config;
use PDO;

/**
 * Class DbConnection
 * @package mickeySTRANGE\NewBookNotice\FW\model
 */
class DbConnection
{

  private static PDO $connection;

  /**
   * @return PDO
   */
  public static function get(): PDO
  {

    if (!isset(self::$connection)) {
      $dbms = Config::getDbms();

      if ($dbms === 'pgsql') {
        $host = Config::getPgsqlHost();
        $dbname = Config::getPgsqlDbname();
        $user = Config::getPgsqlUser();
        $pass = Config::getPgsqlPass();

        $dsn = 'pgsql:host=' . $host . ';dbname=' . $dbname;
        self::$connection = new PDO($dsn, $user, $pass);
      }
    }
    return self::$connection;
  }
}
