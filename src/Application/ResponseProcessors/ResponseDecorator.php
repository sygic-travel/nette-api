<?php

namespace Tripomatic\NetteApi\Application\ResponseProcessors;

use Nette\Application\Application;
use Nette\Application\IResponse;
use Tripomatic\NetteApi\Application\Responses\JsonResponse;

class ResponseDecorator
{
	/**
	 * @param Application $application
	 * @param IResponse $response
	 */
	public function process(Application $application, IResponse $response)
	{
		if ($response instanceof JsonResponse) {
			$response->setPostProcessor([$this, 'decorate']);
			$response->setContentType("text/html; charset=utf-8");
		}
	}

	/**
	 * @param $response
	 * @return string
	 */
	public function decorate($response)
	{
		return '<html><head></head><body>'
			. '<div id="json">' . $response . '</div>'
			. '<script>' . file_get_contents(__DIR__ . '/../../../assets/JSONView-standalone.js') . '</script>'
			. '<script>window.onload = function() { jsonView("#json"); }</script>'
			. '</body></html>';
	}
}
