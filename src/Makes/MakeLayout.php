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

class MakeLayout
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
     * Start make layout(view).
     *
     * @return void
     */
    protected function start()
    {
        $ui = $this->scaffoldCommandObj->getMeta()['ui'];
        $this->putViewLayout("Stubs/views/$ui/layout.blade.php.stub", 'layouts/app.blade.php');
        $this->putViewLayout("Stubs/views/$ui/error.blade.php.stub", 'common/error.blade.php');
    }


    /**
     * Write layout in path
     *
     * @param $path_resource
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function putViewLayout($stub, $file)
    {
        $path_file = $this->getPathResource().$file;
        $path_stub = substr(__DIR__,0, -5) .$stub;

        $this->makeDirectory($path_file);

        if ($this->files->exists($path_file))
        {
            return $this->scaffoldCommandObj->comment("x $path_file");
        }

        $html = $this->files->get($path_stub);
        $html = $this->buildStub($this->scaffoldCommandObj->getMeta(), $html);
        $this->files->put($path_file, $html);
        $this->scaffoldCommandObj->info("+ $path_file");
    }

    /**
     * Get the path to where we should store the view.
     *
     * @return string
     */
    protected function getPathResource()
    {
        return './resources/views/';
    }
}