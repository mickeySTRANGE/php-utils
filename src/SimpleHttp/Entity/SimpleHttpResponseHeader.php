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
   * @param string $name
   * @return string
   */
  private function getSingleHeader(string $name): string {

    if (!array_key_exists($name, $this->headers)) {
      return "";
    } else {
      return $this->headers[$name][0];
    }
  }

  /**
   * @param string $name
   * @return array
   */
  private function getMultipleHeader(string $name): array {

    if (!array_key_exists($name, $this->headers)) {
      return [];
    } else {
      return $this->headers[$name];
    }
  }

  /**
   * @return string
   */
  public function getLocation(): string {

    return $this->getSingleHeader("Location");
  }

  /**
   * @return array
   */
  public function getSetCookie(): array {

    return $this->getMultipleHeader("Set-Cookie");
  }

  /**
   * @return string
   */
  public function getContentLength(): string {

    return $this->getSingleHeader("Content-Length");
  }

  /**
   * @return string
   */
  public function getTransferEncoding(): string {

    return $this->getSingleHeader("Transfer-Encoding");
  }

}
