<?php

namespace Tripomatic\NetteApi\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator;

class ApiExtension extends CompilerExtension
{
	private static $configDefaults = [
		'debugMode' => '%debugMode%',
		'mapping' => ['NetteApi' => 'Tripomatic\NetteApi\Application\Presenters\*Presenter'],
		'errorPresenter' => 'NetteApi:Error',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$configDefaults);

		$builder->getDefinition('application.presenterFactory')
			->addSetup('setMapping', [$config['mapping']]);

		$builder->getDefinition('application.application')
			->addSetup('$errorPresenter', [$config['errorPresenter']]);
	}

	public function afterCompile(PhpGenerator\ClassType $class)
	{
		$config = $this->getConfig(self::$configDefaults);
		$initialize = $class->methods['initialize'];

		// response syntax highliting in debug mode
		if ($config['debugMode'] == TRUE) {
			$initialize->addBody('$this->getService(?)->onResponse[] = [new Tripomatic\NetteApi\Application\ResponseProcessors\ResponseDecorator, "process"];', [
				'application.application',
				$this->prefix('assetManager'),
			]);
		}
	}
}
