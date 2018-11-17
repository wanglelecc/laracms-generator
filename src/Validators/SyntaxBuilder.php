<?php
/**
 * LaraCMS - CMS based on laravel
 *
 * @category  LaraCMS
 * @package   Laravel
 * @author    Wanglelecc <wanglelecc@gmail.com>
 * @date      2018/11/17 12:12:00
 * @copyright Copyright 2018 LaraCMS
 * @license   https://opensource.org/licenses/MIT
 * @github    https://github.com/wanglelecc/laracms
 * @link      https://www.laracms.cn
 * @version   Release 1.0
 */
 
namespace Wanglelecc\Laracms\Generator\Validators;

class SyntaxBuilder
{

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  array $schema
     * @param  array $meta
     * @param  string $type
     * @param  bool $illuminate
     * @return string
     * @throws GeneratorException
     * @throws \Exception
     */
    public function create($schema)
    {
        $fieldsc = $this->createSchemaForValidation($schema);
        return $fieldsc;
    }

    private function createSchemaForValidation($schema)
    {
        $validator = '';
        if(is_array($schema)) {
            foreach ($schema as $s) {
                $validator .= "'" . $s['name'] . "' => '";

                #deal with the different format of the console
                if(isset($s['arguments'][0]) && $s['arguments'][0] != null ) {
                    $validator .= str_replace(")", "", str_replace("(", ":", $s['arguments'][0]));
                    $validator .= "',\n\t\t\t";
                }
            }
            return $validator;
        }
    }
}
