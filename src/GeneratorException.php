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
 
namespace Wanglelecc\Laracms\Generator;

class GeneratorException extends \Exception {

    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Could not determine what you are trying to do. Sorry! Check your migration name.';

}