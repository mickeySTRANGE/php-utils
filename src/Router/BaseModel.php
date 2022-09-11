<?php


namespace mickeySTRANGE\phpUtils\Router;


/**
 * Class BaseModel
 * @package mickeySTRANGE\NewBookNotice\FW\model
 */
abstract class BaseModel
{

    /**
     * called by PDO
     *
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $fieldName = $this->toSetterFunctionName($name);
        $this->$fieldName($value);
    }

    /**
     * @param string $str
     * @return string
     */
    private function toSetterFunctionName(string $str): string
    {
        return "set" . strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']);
    }
}
