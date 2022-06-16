<?php


namespace mickeySTRANGE\phpUtils\SimpleHttp\Entity;

/**
 * Class SimpleHttpResponseHeader
 * @package mickeySTRANGE\phpUtils\SimpleHttp
 */
class SimpleHttpResponseHeader {

  private string $responseHeader;
  private string $statusLine;
  private array $headers = [];

  /**
   * SimpleHttpResponseHeader constructor.
   * @param string $responseHeader
   */
  public function __construct(string $responseHeader) {
    $this->responseHeader = $responseHeader;
    $this->_init();
  }

  /**
   * init headers for single getters
   */
  private function _init() {

    $isFirst = true;

    foreach (preg_split("/[\r\n]+/", $this->responseHeader) as $line) {

      if (strlen($line) === 0) {
        continue;
      }

      if ($isFirst) {
        $this->statusLine = $line;
        $isFirst = false;
        continue;
      }

      $key = substr($line, 0, strpos($line, ":"));
      $value = trim(substr($line, strpos($line, ":") + 1));

      $this->headers[$key][] = $value;
    }
  }

  /**
   * @return string
   */
  public function getLocation(): string {

    if (!array_key_exists("Location", $this->headers)) {
      return "";
    } else {
      return $this->headers["Location"][0];
    }
  }

  /**
   * @return array
   */
  public function getSetCookie(): array {

    if (!array_key_exists("Set-Cookie", $this->headers)) {
      return [];
    } else {
      return $this->headers["Set-Cookie"];
    }
  }


}
