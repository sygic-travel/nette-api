<?php

namespace Tripomatic\NetteApi\Application;

use Nette\Application\BadRequestException;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\InvalidStateException;

abstract class Presenter implements IPresenter
{
	/**
	 * @param Request $request
	 * @return IResponse
	 * @throws BadRequestException
	 */
	public function run(Request $request)
	{
		$method = strtolower($request->getMethod());
		if (!method_exists($this, $method)) {
			throw new BadRequestException("Method '{$request->getMethod()}' not supported.");
		}

		$response = $this->$method($request);
		if (!$response instanceof IResponse) {
			throw new InvalidStateException("Presenter '{$request->getPresenterName()}' did not return any response for method '{$request->getMethod()}'.");
		}
		return $response;
	}
}
