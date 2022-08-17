<?php

namespace mickeySTRANGE\phpUtils\SimpleHttp;

use JetBrains\PhpStorm\ArrayShape;
use mickeySTRANGE\phpUtils\GeneralUtils\Constants;
use mickeySTRANGE\phpUtils\SimpleHttp\Entity\SimpleHttpCookie;
use mickeySTRANGE\phpUtils\SimpleHttp\Entity\SimpleHttpResponse;
use mickeySTRANGE\phpUtils\SimpleHttp\Entity\SimpleHttpResponseBody;
use mickeySTRANGE\phpUtils\SimpleHttp\Entity\SimpleHttpResponseHeader;
use mickeySTRANGE\phpUtils\SimpleHttp\Exceptions\SimpleHttpConnectionException;
use mickeySTRANGE\phpUtils\SimpleHttp\Exceptions\SimpleHttpInvalidIUrlException;

/**
 * Class SimpleHttpHandler
 * @package mickeySTRANGE\phpUtils\SimpleHttp
 */
class SimpleHttpHandler {

  private const METHOD_GET = "GET";
  private const METHOD_POST = "POST";
  private const MAX_REDIRECT_DEPTH = 10;

  private bool $useReferer = false;
  private bool $useCookie = false;
  private bool $sslVerify = true;
  private bool $logOutput = true;

  private string $referer = "";
  /** @var SimpleHttpCookie[] */
  private array $cookie = [];

  private const REDIRECT_STATUS = [301, 302, 303, 307, 308];

  /**
   * @param string $url
   * @param array  $headers
   * @return SimpleHttpResponse
   * @throws SimpleHttpConnectionException
   * @throws SimpleHttpInvalidIUrlException
   */
  public function get(string $url, array $headers = []): SimpleHttpResponse {

    $formated = $this->_formatUrl($url);
    return self::_sendWithRedirect(self::METHOD_GET, $formated, $headers, '');
  }


  /**
   * @param string $url
   * @param array  $headers
   * @param array  $body
   * @param bool   $isJson
   * @return SimpleHttpResponse
   * @throws SimpleHttpConnectionException
   * @throws SimpleHttpInvalidIUrlException
   */
  public function post(string $url, array $headers = [], array $body = [], bool $isJson = false
  ): SimpleHttpResponse {

    $formated = $this->_formatUrl($url);

    if ($isJson) {
      $requestBody = json_encode($body);
    } else {
      $requestBody = http_build_query($body);
      $headers["Content-Type"] = "application/x-www-form-urlencoded";
    }

    return $this->_sendWithRedirect(self::METHOD_POST, $formated, $headers, $requestBody);
  }

  /**
   * @param bool $useReferer
   */
  public function setUseReferer(bool $useReferer): void {
    $this->useReferer = $useReferer;
  }

  /**
   * @param bool $useCookie
   */
  public function setUseCookie(bool $useCookie): void {
    $this->useCookie = $useCookie;
  }

  /**
   * @param bool $sslVerify
   */
  public function setSslVerify(bool $sslVerify): void {
    $this->sslVerify = $sslVerify;
  }

  /**
   * @param bool $logOutput
   */
  public function setLogOutput(bool $logOutput): void {
    $this->logOutput = $logOutput;
  }

  /**
   * @param $url
   * @return string
   * @throws SimpleHttpInvalidIUrlException
   */
  private function _formatUrl($url): string {

    $anaUrl = $this->_analyzeUrl($url);
    $protocol = $anaUrl["protocol"];
    $domain = $anaUrl["domain"];
    $path = $anaUrl["path"];
    $query = $anaUrl["query"];

    if ($protocol === "http://") {
      return $protocol . $domain . $path . $query;
    } elseif ($protocol === "https://" || $protocol === "") {
      return "https://" . $domain . $path . $query;
    }

    throw new SimpleHttpInvalidIUrlException();
  }


  /**
   * @param $url
   * @return array
   */
  #[ArrayShape([
    "protocol" => "mixed",
    "domain"   => "mixed",
    "path"     => "mixed",
    "query"    => "mixed",
    "fragment" => "mixed"
  ])] private function _analyzeUrl(
    $url
  ): array {

    preg_match("/(https?:\/\/|)([^\/?#]+|)([^?#]*)(\??[^#]*)(#?.*)/", $url, $matches);
    $protocol = $matches[1];
    $domain = $matches[2];
    $path = $matches[3];
    $query = $matches[4];
    $fragment = $matches[5];

    return ["protocol" => $protocol, "domain" => $domain, "path" => $path, "query" => $query, "fragment" => $fragment];
  }

  /**
   * @param string $message
   * @param int    $level
   */
  private function log($message, $level) {

    // for heroku log
    error_log($message);

  }

  /**
   * @param string $method
   * @param string $url
   * @param array  $headers
   * @param string $body
   * @return SimpleHttpResponse
   * @throws SimpleHttpConnectionException
   */
  private function _sendWithRedirect(string $method, string $url, array $headers, string $body): SimpleHttpResponse {

    $isFirstRequest = true;

    $requestMethod = $method;
    $requestUrl = $url;
    $requestHeaders = $headers;
    $requestBody = $body;

    $originHeaderStr = "";
    $histories = [];

    while (true) {
      $anaUrl = $this->_analyzeUrl($requestUrl);
      $response = $this->_sendRequest($requestMethod, $requestUrl, $requestHeaders, $requestBody);

      $histories[] = $response;
      if ($isFirstRequest) {
        $originHeaderStr = $response->getRequestHeader();
        $isFirstRequest = false;
      }
      if ($this->useCookie) {
        $this->_analyzeSetCookie($requestUrl, $response->getResponseHeader()->getSetCookie());
      }

      if (!in_array($response->getStatusCode(), self::REDIRECT_STATUS)) {
        break;
      }

      if (count($histories) === self::MAX_REDIRECT_DEPTH) {
        break;
      }

      $anaLocation = $this->_analyzeUrl($response->getResponseHeader()->getLocation());
      $protocol = $anaLocation['protocol'] ?: "https://";
      $domain = $anaLocation['domain'] ?: $anaUrl['domain'];
      $fragment = $anaLocation['fragment'] ?: $anaUrl['fragment'];

      $requestMethod = $method;
      $requestUrl = $protocol . $domain . $anaLocation['path'] . $anaLocation['query'] . $fragment;
      $requestHeaders = [];
      $requestBody = "";
    }

    $returnResponse = new SimpleHttpResponse(
      $method,
      $originHeaderStr,
      $body,
      $response->getStatusCode(),
      $response->getResponseHeader(),
      $response->getResponseBody()
    );

    foreach ($histories as $history) {
      $returnResponse->addRedirectHistory($history);
    }

    if ($this->useReferer) {
      $this->referer = $requestUrl;
    }

    return $returnResponse;
  }

  /**
   * @param string $requestUrl
   * @param array  $setCookies
   */
  private function _analyzeSetCookie(string $requestUrl, array $setCookies) {

    $domain = $this->_analyzeUrl($requestUrl)["domain"];
    $path = $this->_analyzeUrl($requestUrl)["path"];

    foreach ($setCookies as $line) {
      $newCookie = null;
      while (true) {
        $exLine = explode(";", $line, 2);
        $attr = trim($exLine[0]);
        $exAttr = explode("=", $attr);

        if ($newCookie) {
          match (strtolower($exAttr[0])) {
            strtolower(SimpleHttpCookie::DOMAIN) => $newCookie->setDomain($exAttr[1]),
            strtolower(SimpleHttpCookie::EXPIRES) => $newCookie->setExpires($exAttr[1]),
            strtolower(SimpleHttpCookie::HTTP_ONLY) => $newCookie->setHttpOnly(),
            strtolower(SimpleHttpCookie::MAX_AGE) => $newCookie->setMaxAge((int) $exAttr[1]),
            strtolower(SimpleHttpCookie::PATH) => $newCookie->setPath($exAttr[1]),
            strtolower(SimpleHttpCookie::SAME_SITE) => $newCookie->setSameSite($exAttr[1]),
            strtolower(SimpleHttpCookie::SECURE) => $newCookie->setSecure(),
            default => null
          };
        } else {
          $newCookie = new SimpleHttpCookie($exAttr[0], $exAttr[1], $domain, $path);
        }

        if (!array_key_exists(1, $exLine) || strlen($exLine[1] === 0)) {
          break;
        }
        $line = $exLine[1];
      }

      if ($newCookie->isDestructioned()) {
        continue;
      }

      foreach ($this->cookie as $key => $cookie) {
        if ($cookie->getDomain() === $newCookie->getDomain()
          && $cookie->getPath() === $newCookie->getPath()
          && $cookie->getKey() === $newCookie->getKey()) {
          unset($this->cookie[$key]);
        }
      }
      $this->cookie[] = $newCookie;
    }
  }

  /**
   * @param string $method
   * @param string $url
   * @param array  $headers
   * @param string $body
   * @return SimpleHttpResponse
   * @throws SimpleHttpConnectionException
   */
  private function _sendRequest(string $method, string $url, array $headers, string $body): SimpleHttpResponse {

    $anaUrl = $this->_analyzeUrl($url);
    $protocol = $anaUrl["protocol"];
    $domain = $anaUrl["domain"];
    $path = $anaUrl["path"];
    $query = $anaUrl["query"];

    $request[] = "$method $path$query HTTP/1.1";
    $request[] = "Host: $domain";
    foreach ($headers as $headerKey => $headerValue) {
      if ($headerKey === "Host"
        || $headerKey === "Referer"
        || $headerKey === "Cookie"
        || $headerKey === "Content-Length") {
        continue;
      }
      $request[] = "$headerKey: $headerValue";
    }
    if ($this->useReferer) {
      $request[] = "Referer: " . $this->referer;
    }
    if ($this->useCookie) {
      $cookieString = $this->_gatherCookie($protocol, $domain, $path);
      if (strlen($cookieString) > 0) {
        $request[] = "Cookie: " . $cookieString;
      }
    }
    $request[] = "Content-Length: " . strlen($body);

    $headerString = implode(Constants::CRLF, $request);
    $request[] = "";
    $request[] = $body;

    $this->log('request to ' . $url, LOG_INFO);

    $context = stream_context_create();
    if ($this->sslVerify) {
      stream_context_set_option($context, 'ssl', 'verify_peer', false);
      stream_context_set_option($context, 'ssl', 'verify_host', false);
    }
    $fp = stream_socket_client('ssl://' . $domain . ':443', $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
    if (!$fp) {
      $this->log('connection Error! ' . $errno . ':' . $errstr, LOG_ERR);
      throw new SimpleHttpConnectionException();
    }
    stream_set_timeout($fp, 10);
    fputs($fp, implode(Constants::CRLF, $request));

    $RESPONSE_HEADER = "";
    $RESPONSE_BODY = "";
    $statusLine = '';
    // HTTPヘッダの取得
    while (!feof($fp)) {
      $temporaryData = fgets($fp, 4096);
      if (preg_match('/^[\r\n]+$/x', $temporaryData)) {
        break;
      }
      if ($statusLine === '') {
        $statusLine = $temporaryData;
      }
      $RESPONSE_HEADER .= $temporaryData;
    }

    // 結果の取得
    $start = intval(microtime(true));
    $len = 0;
    while (!feof($fp)) {
      $RESPONSE_BODY .= fgets($fp, 4096);
      if (intval(microtime(true)) - $start > 10 && strlen($RESPONSE_BODY) === $len) {
        break;
      }
      $len = strlen($RESPONSE_BODY);
    }
    fclose($fp);

    preg_match('!^HTTP/(\d\.\d) (\d{3})(?: (.+))?!', $statusLine, $match);
    $statusCode = (int) $match[2];

    if (str_contains($RESPONSE_HEADER, 'Transfer-Encoding: chunked')) {
      $tmp = $RESPONSE_BODY;
      $eol = "\r\n";
      $add = strlen($eol);
      $str = '';
      do {
        $tmp = ltrim($tmp);
        $pos = strpos($tmp, $eol);
        if ($pos === false) {
          break;
        }
        $len = hexdec(substr($tmp, 0, $pos));
        if (!is_numeric($len) or $len < 0) {
          break;
        }
        $str .= substr($tmp, ($pos + $add), $len);
        $tmp = substr($tmp, ($len + $pos + $add));
        $check = trim($tmp);
      } while (!empty($check));
      $RESPONSE_BODY = $str;
    }

    $this->log(
      'request finish ' . $statusCode . ' header:' . strlen($RESPONSE_HEADER) . ' body:' . strlen($RESPONSE_BODY),
      LOG_INFO
    );

    return
      new SimpleHttpResponse(
        $method,
        $headerString,
        $body,
        $statusCode,
        new SimpleHttpResponseHeader($RESPONSE_HEADER),
        new SimpleHttpResponseBody($RESPONSE_BODY)
      );
  }

  /**
   * @param string $protocol
   * @param string $domain
   * @param string $path
   * @return string
   */
  private function _gatherCookie(string $protocol, string $domain, string $path): string {

    $cookieArr = [];
    $time = time();

    foreach ($this->cookie as $cookie) {
      if ($cookie->sendCheck($protocol, $domain, $path, $time)) {
        $cookieArr[] = $cookie->getKey() . "=" . $cookie->getValue();
      }
    }

    return implode(" ", $cookieArr);
  }
}
