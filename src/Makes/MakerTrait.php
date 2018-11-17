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
use Illuminate\Container\Container;

trait MakerTrait
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;
    protected $scaffoldCommandM;

    /**
     * @param ScaffoldMakeCommand $scaffoldCommand
     * @param Filesystem $files
     */
    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandM = $scaffoldCommand;

        $this->generateNames($this->scaffoldCommandM);
    }

    protected function getArrayRecursive(array $array, $parent = '')
    {
        $data = [];

        foreach ($array as $key => $value)
        {
            if(gettype($value) == 'array')
            {
                array_merge(
                    $data,
                    $this->getArrayRecursive($value, "$parent")
                );
                continue;
            }

            $data["$parent.$key"] = $value;
        }

        return $data;
    }


    protected function getFilesRecursive($path)
    {
        $files = [];
        $scan = array_diff(scandir($path), ['.', '..']);

        foreach ($scan as $file)
        {
            $file = realpath("$path$file");

            if(is_dir($file))
            {
                $files = array_merge
                (
                    $files,
                    $this->getFilesRecursive($file.DIRECTORY_SEPARATOR)
                );
                continue;
            }

            $files[] = $file;
        }

        return $files;
    }

    /**
     * Get stub path.
     *
     * @param $file_name
     * @param string $path
     * @return string
     */
    protected function getStubPath()
    {
        return substr(__DIR__,0, -5) . 'Stubs' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get fields stubs.
     *
     * @return array fields
     */
    protected function getStubFields($ui, $type)
    {
        $stubsFieldsPath = $this->getStubPath() . join(DIRECTORY_SEPARATOR, ['views', $ui, 'fields', $type, '']);

        if($this->existsDirectory($stubsFieldsPath))
        {
            $this->scaffoldCommandM->error('Stub not found');
            return;
        }

        $stubsFieldsFiles = $this->getFilesRecursive($stubsFieldsPath);

        $stubs = [];

        foreach ($stubsFieldsFiles as $file)
        {
            $stubs[str_replace($stubsFieldsPath, '', $file)] = $this->getFile($file);
        }

        return $stubs;
    }

    /**
     * Get views stubs.
     *
     * @return array views
     */
    protected function getStubViews($ui)
    {
        $viewsPath = $this->getStubPath() . join(DIRECTORY_SEPARATOR, ['views', $ui, 'pages', '']);
        $files = $this->getFilesRecursive($viewsPath);
        $viewFiles = [];

        foreach ($files as $file)
        {
            $viewFiles[str_replace($viewsPath, '', $file)] = $file;
        }

        return $viewFiles;
    }


    protected function getDestinationViews($model)
    {
        return "./resources/views/backend/$model/";
    }

    /**
     * Build file replacing metas in template.
     *
     * @param array $metas
     * @param string &$template
     * @return void
     */
    protected function buildStub(array $metas, &$template)
    {
        foreach($metas as $k => $v)
        {
            $template = str_replace("{{". $k ."}}", $v, $template);
        }

        return $template;
    }

    /**
     * Get the path to where we should store the controller.
     *
     * @param $file_name
     * @param string $path
     * @return string
     */
    protected function getPath($file_name, $path='controller')
    {
        if($path == "controller")
        {
            return './app/Http/Controllers/Administrator/' . $file_name . '.php';
        }
        elseif($path == "request")
        {
            return './app/Http/Requests/Administrator/'.$file_name.'.php';
        }
        elseif($path == "observer")
        {
            return './app/Observers/'.$file_name.'.php';
        }
        elseif($path == "policy")
        {
            return './app/Policies/'.$file_name.'.php';
        }
        elseif($path == "factory")
        {
            return './database/factories/'.$file_name.'Factory.php';
        }
        elseif($path == "model")
        {
            return './app/Models/'.$file_name.'.php';
        }
        elseif($path == "model-trait")
        {
            return './app/Models/Traits/'.$file_name.'Operation.php';
        }
        elseif($path == "seed")
        {
            return './database/seeds/'.$file_name.'.php';
        }
        elseif($path == "view-index")
        {
            return './resources/views/backend/'.$file_name.'/index.blade.php';
        }
        elseif($path == "view-edit")
        {
            return './resources/views/backend/'.$file_name.'/edit.blade.php';
        }
        elseif($path == "view-show")
        {
            return './resources/views/backend/'.$file_name.'/show.blade.php';
        }
        elseif($path == "view-create")
        {
            return './resources/views/backend/'.$file_name.'/create.blade.php';
        }
        elseif($path == "localization"){
            return './resources/lang/'.$file_name.'.php';
        }
        elseif($path == "route"){
            //return './routes/web.php';
            return './routes/administrator.php';
        }
        elseif($path == "route_old"){
            return './app/Http/routes.php';
        }
    }

    protected function getFile($file)
    {
        return $this->files->get($file);
    }

    protected function existsDirectory($path)
    {
        return !$this->files->isDirectory($path);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if ( ! $this->files->isDirectory(dirname($path)))
        {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function compileStub($filename)
    {
        $stub = $this->files->get(substr(__DIR__,0, -5) . 'Stubs/'.$filename.'.stub');

        $this->buildStub($this->scaffoldCommandObj->getMeta(), $stub);
        // $this->replaceValidator($stub);

        return $stub;
    }

    /**
     * Get the application namespace.
     *
     * @return string
     */
    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

}
