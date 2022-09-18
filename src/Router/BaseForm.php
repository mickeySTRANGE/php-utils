<?php

namespace mickeySTRANGE\phpUtils\Router;


abstract class BaseForm
{

    private array $getParam;
    private array $postParam;

    private array $errors = [];

    private bool $overrideWithPostParameter = true;

    /**
     * BaseForm constructor.
     * @param array $getParam
     * @param array $postParam
     */
    public function __construct(array $getParam, array $postParam)
    {
        $this->getParam = $getParam;
        $this->postParam = $postParam;
    }

    /**
     * @param $name
     * @return string|array|null
     */
    protected function getParam($name): array|string|null
    {

        $get = array_key_exists($name, $this->getParam) ? $this->getParam[$name] : null;
        $post = array_key_exists($name, $this->postParam) ? $this->postParam[$name] : null;

        if ($post !== null) {
            return $post;
        } else {
            return $get;
        }
    }

    /**
     * @return bool
     */
    public function isValidParameter(): bool
    {
        return true;
    }

    /**
     * @param string $key
     * @param string $message
     */
    protected function addErrors(string $key, string $message)
    {
        $this->errors[$key][] = $message;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        $return = [];
        foreach ($this->errors as $error) {
            foreach ($error as $message) {
                $return[] = $message;
            }
        }
        return $return;
    }
}
