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

namespace Wanglelecc\Laracms\Generator\Makes;

use Illuminate\Filesystem\Filesystem;
use Wanglelecc\Laracms\Generator\Commands\ScaffoldMakeCommand;
use Wanglelecc\Laracms\Generator\Migrations\SchemaParser;
use Wanglelecc\Laracms\Generator\Migrations\SyntaxBuilder;

class MakeRoute
{
    use MakerTrait;

    /**
     * Create a new instance.
     *
     * @param ScaffoldMakeCommand $scaffoldCommand
     * @param Filesystem $files
     * @return void
     */
    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;

        $this->start();
    }

    /**
     * Start make controller.
     *
     * @return void
     */
    private function start()
    {
        $name = $this->scaffoldCommandObj->getObjName('Name');
        $route_name = floatval(app()::VERSION) < 5.3 ? 'route_old' : 'route';
        $path = $this->getPath($name, $route_name);
        $stub = $this->compileRouteStub();

        $content = $this->files->get($path);
        
        if (strpos($content, $stub) === false) {

            if (strpos($content, '#Append Route') === false) { 
               $this->files->append($path, $stub);   
            }else{
               $content = str_replace("#Append Route", $stub, $content);
               $this->files->put($path, $content);  
            }

            return $this->scaffoldCommandObj->info('+ ' . $path . ' (Updated)');
        }
        
        return $this->scaffoldCommandObj->comment("x $path" . ' (Skipped)');
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileRouteStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/route.stub');

        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);

        return $stub;
    }
}