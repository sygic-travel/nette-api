<?php

namespace Tripomatic\NetteApi\Application\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Tracy\ILogger;
use Tripomatic\NetteApi\Application\Responses\JsonResponse;

class ErrorPresenter implements IPresenter
{
	/** @var ILogger|NULL */
	protected $logger;

	/**
	 * @param ILogger $logger
	 */
	public function __construct(ILogger $logger = NULL)
	{
		$this->logger = $logger;
	}

	/**
	 * @param Request $request
	 * @return IResponse
	 */
	public function run(Request $request)
	{
		$e = $request->getParameter('exception');
		if ($e instanceof BadRequestException) {
			$code = $e->getCode();
		} else {
			$code = 500;
			if ($this->logger) {
				try {
					$this->logger->log($e, ILogger::EXCEPTION);
				} catch (\Exception $e) {
					// logger may fail as well
				}
			}
		}

		if (isset(JsonResponse::$messages[$code])) {
			$message = JsonResponse::$messages[$code];
		} else {
			$message = 'Unknown error';
		}

		return $this->createResponse($message, $code);
	}

	/**
	 * @param string $message
	 * @param int $code
	 * @return IResponse
	 */
	protected function createResponse($message, $code)
	{
		return new JsonResponse(['message' => $message], $code);
	}
}
