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

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerScaffoldGenerator();
	}

	/**
	 * Register the make:scaffold generator.
	 *
	 * @return void
	 */
	private function registerScaffoldGenerator()
	{
		$this->app->singleton('command.laracms.scaffold', function ($app) {
			return $app['Wanglelecc\Laracms\Generator\Commands\ScaffoldMakeCommand'];
		});

		$this->commands('command.laracms.scaffold');
	}
}