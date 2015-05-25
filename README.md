# Tripomatic\NetteApi

Build REST APIs with [Nette Framework](http://nette.org).

Tripomatic\NetteApi is a very light base for building REST APIs with Nette Framework. It provides an extremely minimalist [base presenter](src/Application/Presenter.php), an [error presenter](src/Application/Presenters/ErrorPresenter.php) that ensures returning a valid JSON on any error and takes care of logging, and a handy JSON prettifier for testing APIs in browser that does not force you to hide your [Tracy](http://tracy.nette.org) panel.

## Installation

Install Tripomatic\NetteApi using [Composer](https://getcomposer.org):
```Shell
$ composer require tripomatic/nette-api
```

## Quickstart
Add Tripomatic\NetteApi extension in your [NEON config](http://doc.nette.org/en/2.3/configuring):
```YAML
extensions:
  	api: Tripomatic\NetteApi\DI\ApiExtension
```

Create your first RESTful presenter inherited from minimalist [`Tripomatic\NetteApi\Application\Presenter`](src/Application/Presenter.php):
```php
use Nette\Application\Request;
use Tripomatic\NetteApi\Application\Presenter;
use Tripomatic\NetteApi\Application\Responses\JsonResponse;

class WeatherPresenter extends Presenter
{
	public function get(Request $request)
	{
		$city = $request->getParameter('city');

		$data = ...; // get weather data for the city
		return new JsonResponse($data);
	}

	// implement ohter REST methods similarly
}
```

## Configuration
Tripomatic\NetteApi does not require any additional configuration, it sets up two handy defaults:
- An error presenter is set up that ensures the API returns a valid JSON in all error situations.
- API responses are prettified by default when running in debug mode, Tracy panel is also visible.

This behavior can be easily overridden in corresponding extension's section:
```YAML
api:
	prettify: %debugMode% # can be set to TRUE/FALSE
	errorPresenter: NetteApi:Error
	mapping:
		NetteApi: 'Tripomatic\NetteApi\Application\Presenters\*Presenter'
```

## Bootsraping
If you need some help with writing your application bootsrap here is an example that we use in Tripomatic:
```php
require __DIR__ . '/../vendor/autoload.php';

$tomDebug = getenv('TOM_DEBUG') === 'TRUE' && isset($_COOKIE['tom_api_debug']);
$tomLogDir = getenv('TOM_LOG_DIR') ?: '/var/log/tripomatic/some-api';
$tomTmpDir = getenv('TOM_TMP_DIR') ?: '/var/tmp/some-api';
$tomConfigFile = getenv('TOM_CONFIG_FILE') ?: __DIR__ . '/../config/config.local.neon';

$configurator = new Nette\Configurator;
$configurator->setDebugMode($tomDebug);
$configurator->enableDebugger($tomLogDir);

$configurator->setTempDirectory($tomTmpDir);
$configurator->addConfig(__DIR__ . '/../config/config.neon');
if (file_exists($tomConfigFile)) {
	$configurator->addConfig($tomConfigFile);
}

return $configurator->createContainer();
```

Yes, with Nette Framework it can really be that minimalist. Everything else can be achieved with Nette's great [Dependency Injection implementation](http://doc.nette.org/en/2.3/dependency-injection). For more details see [Nette Framework documentation](http://doc.nette.org/en/2.3).

## License
Tripomatic\NetteApi is licensed under [MIT](LICENSE).
