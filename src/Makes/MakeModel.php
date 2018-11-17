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

class MakeModel
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
        $path = $this->getPath($name, 'model');

        $this->createBaseModelIfNotExists();

        if ($this->files->exists($path))
        {
            return $this->scaffoldCommandObj->comment("x $path");
        }

        $this->files->put($path, $this->compileModelStub());

        $this->scaffoldCommandObj->info('+ ' . $path);
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileModelStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/model.stub');

        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);
        $this->buildFillable($stub);

        return $stub;
    }

    /**
     * Build stub replacing the variable template.
     *
     * @return string
     */
    protected function buildFillable(&$stub)
    {
        $schemaArray = [];

        $schema = $this->scaffoldCommandObj->getMeta()['schema'];

        if ($schema)
        {
            $items = (new SchemaParser)->parse($schema);
            foreach($items as $item)
            {
                $schemaArray[] = "'{$item['name']}'";
            }

            $schemaArray = join(", ", $schemaArray);
        }

        $stub = str_replace('{{fillable}}', $schemaArray, $stub);

        return $this;
    }

    protected function createBaseModelIfNotExists()
    {
        $base_model_path = $this->getPath("Model", 'model');
        if (!$this->files->exists($base_model_path))
        {
            $this->makeDirectory($base_model_path);
            $this->files->put($base_model_path, $this->compileBaseModelStub());
            return $this->scaffoldCommandObj->info("+ $base_model_path". ' (Updated)');
        }

        return $this->scaffoldCommandObj->comment("x $base_model_path" . ' (Skipped)');
    }

    protected function compileBaseModelStub()
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/base_model.stub');

        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);
        $this->buildFillable($stub);

        return $stub;
    }
}