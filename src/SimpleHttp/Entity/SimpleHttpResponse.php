<?php


namespace mickeySTRANGE\phpUtils\SimpleHttp\Entity;

/**
 * Class SimpleHttpResponse
 * @package mickeySTRANGE\phpUtils\SimpleHttp
 */
class SimpleHttpResponse {

  private string $requestMethod;
  private string $requestHeader;
  private string $requestBody;
  private int $statusCode;
  private SimpleHttpResponseHeader $responseHeader;
  private SimpleHttpResponseBody $responseBody;

  /** @var SimpleHttpResponse[] */
  private array $redirectHistory = [];


  /**
   * SimpleHttpResponse constructor.
   * @param string                   $requestMethod
   * @param string                   $requestHeader
   * @param string                   $requestBody
   * @param int                      $statusCode
   * @param SimpleHttpResponseHeader $responseHeader
   * @param SimpleHttpResponseBody   $responseBody
   */
  public function __construct(
    string $requestMethod,
    string $requestHeader,
    string $requestBody,
    int $statusCode,
    SimpleHttpResponseHeader $responseHeader,
    SimpleHttpResponseBody $responseBody
  ) {
    $this->requestMethod = $requestMethod;
    $this->requestHeader = $requestHeader;
    $this->requestBody = $requestBody;
    $this->statusCode = $statusCode;
    $this->responseHeader = $responseHeader;
    $this->responseBody = $responseBody;
  }

  /**
   * @param SimpleHttpResponse $response
   */
  public function addRedirectHistory(SimpleHttpResponse $response) {

    $this->redirectHistory[] = $response;
  }


  /************************************************************************
   * setter and getters...
   ************************************************************************/

  /**
   * @return string
   */
  public function getRequestMethod(): string {
    return $this->requestMethod;
  }

  /**
   * @return string
   */
  public function getRequestHeader(): string {
    return $this->requestHeader;
  }

  /**
   * @return string
   */
  public function getRequestBody(): string {
    return $this->requestBody;
  }

  /**
   * @return int
   */
  public function getStatusCode(): int {
    return $this->statusCode;
  }

  /**
   * @return SimpleHttpResponseHeader
   */
  public function getResponseHeader(): SimpleHttpResponseHeader {
    return $this->responseHeader;
  }

  /**
   * @return SimpleHttpResponseBody
   */
  public function getResponseBody(): SimpleHttpResponseBody {
    return $this->responseBody;
  }

  /**
   * @return SimpleHttpResponse[]
   */
  public function getRedirectHistory(): array {
    return $this->redirectHistory;
  }
}
