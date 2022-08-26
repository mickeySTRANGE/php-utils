<?php


namespace mickeySTRANGE\phpUtils\SimpleHttp\Entity;

use JetBrains\PhpStorm\Pure;

/**
 * Class SimpleHttpCookie
 * @package mickeySTRANGE\phpUtils\SimpleHttp\Entity
 */
class SimpleHttpCookie
{

  private string $key;
  private string $value;
  private int $expires = 0;
  private string $domain;
  private string $path;
  private bool $secure = false;
  private bool $httpOnly = false;
  private string $sameSite = "Lax";

  private bool $isExistDomainAttribute = false;
  private bool $isDestructed = false;

  const EXPIRES = "Expires";
  const MAX_AGE = "Max-Age";
  const DOMAIN = "Domain";
  const PATH = "Path";
  const SECURE = "Secure";
  const HTTP_ONLY = "HttpOnly";
  const SAME_SITE = "SameSite";

  /**
   * SimpleHttpCookie constructor.
   * @param string $key
   * @param string $value
   * @param string $domain
   * @param string $path
   */
  public function __construct(string $key, string $value, string $domain, string $path)
  {
    $this->key = $key;
    $this->value = $value;
    $this->domain = $domain;
    $this->path = $path;
  }

  /**
   * @param string $protocol
   * @param string $domain
   * @param string $path
   * @param int $time
   * @return bool
   */
  #[Pure] public function sendCheck(string $protocol, string $domain, string $path, int $time): bool
  {

    $isTimeOk = $this->expires === 0 || $time < $this->expires;
    $isDomainOk = $this->isExistDomainAttribute
      ? str_ends_with($domain, $this->domain) && str_ends_with($domain, "." . $this->domain)
      : $domain === $this->domain;
    $isPathOk = $path === $this->path
      || (str_starts_with($path, $this->path) && str_ends_with($this->path, "/"))
      || str_starts_with($path, $this->path . "/");
    $isSecureOk = !$this->secure || $protocol === "https://";

    return $isTimeOk && $isDomainOk && $isPathOk && $isSecureOk;
  }

  /**
   * @param string $domain
   */
  public function setDomain(string $domain): void
  {

    if (strrpos($domain, ".") === strlen($domain) - 1) {
      $this->isDestructed = true;
      return;
    }

    $domain = trim($domain, ".");
    if (str_ends_with($this->domain, $domain) && str_ends_with($this->domain, "." . $domain)) {
      $this->domain = $domain;
      $this->isExistDomainAttribute = true;
    } else {
      $this->isDestructed = true;
    }
  }

  /**
   * @param string $expires
   */
  public function setExpires(string $expires): void
  {

    if ($this->expires !== "") {
      return;
    }

    $time = strtotime(trim(substr($expires, 5)));
    if ($time === false) {
      return;
    }

    $this->expires = $time;
  }

  /**
   * @param int $maxAge
   */
  public function setMaxAge(int $maxAge): void
  {

    $this->expires = time() + $maxAge;
  }

  /**
   * @param string $path
   */
  public function setPath(string $path): void
  {
    $this->path = $path;
  }

  /**
   *
   */
  public function setSecure(): void
  {
    $this->secure = true;
  }

  /**
   *
   */
  public function setHttpOnly(): void
  {
    $this->httpOnly = true;
  }

  /**
   * @param string $sameSite
   */
  public function setSameSite(string $sameSite): void
  {
    $this->sameSite = $sameSite;
  }

  /**
   * @return string
   */
  public function getKey(): string
  {
    return $this->key;
  }

  /**
   * @return string
   */
  public function getValue(): string
  {
    return $this->value;
  }

  /**
   * @return int
   */
  public function getExpires(): int
  {
    return $this->expires;
  }

  /**
   * @return string
   */
  public function getDomain(): string
  {
    return $this->domain;
  }

  /**
   * @return string
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * @return bool
   */
  public function isSecure(): bool
  {
    return $this->secure;
  }

  /**
   * @return bool
   */
  public function isHttpOnly(): bool
  {
    return $this->httpOnly;
  }

  /**
   * @return string
   */
  public function getSameSite(): string
  {
    return $this->sameSite;
  }

  /**
   * @return bool
   */
  public function isDestructed(): bool
  {
    return $this->isDestructed;
  }

  /**
   * @return bool
   */
  public function isExistDomainAttribute(): bool
  {
    return $this->isExistDomainAttribute;
  }
}
