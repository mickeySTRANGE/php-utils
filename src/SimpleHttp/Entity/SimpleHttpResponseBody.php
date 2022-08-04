<?php


namespace mickeySTRANGE\phpUtils\SimpleHttp\Entity;

/**
 * Class SimpleHttpResponseBody
 * @package mickeySTRANGE\phpUtils\SimpleHttp
 */
class SimpleHttpResponseBody {

  private string $responseBody;

  /**
   * SimpleHttpResponseBody constructor.
   * @param string $responseBody
   */
  public function __construct(string $responseBody) {
    $this->responseBody = $responseBody;
  }

  /**
   * @return string
   */
  public function getResponseBodyStr(): string {
    return $this->responseBody;
  }
}
