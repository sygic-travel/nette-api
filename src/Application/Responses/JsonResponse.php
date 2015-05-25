<?php

namespace Tripomatic\NetteApi\Application\Responses;

use Nette\Application;
use Nette\Http;
use Nette\Utils\Json;

class JsonResponse implements Application\IResponse
{
	/** @var array|\stdClass */
	private $data;

	/** @var int */
	private $code;

	/** @var string */
	private $contentType = 'application/json; charset=utf-8';

	/** @var callable */
	private $postProcessor;

	/**
	 * @param array|\stdClass $data
	 * @param int $code
	 */
	public function __construct($data, $code = Http\IResponse::S200_OK)
	{
		$this->data = $data;
		$this->code = $code;
	}

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setExpiration(FALSE);
		$httpResponse->setCode($this->code);

		$response = Json::encode($this->data, Json::PRETTY);
		if (is_callable($this->postProcessor)) {
			$response = call_user_func($this->postProcessor, $response);
		}
		echo $response;
	}

	/**
	 * @return array|\stdClass
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param array|\stdClass $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param int $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @param string $contentType
	 */
	public function setContentType($contentType)
	{
		$this->contentType = $contentType;
	}

	/**
	 * @return callable
	 */
	public function getPostProcessor()
	{
		return $this->postProcessor;
	}

	/**
	 * @param callable $postProcessor
	 */
	public function setPostProcessor($postProcessor)
	{
		$this->postProcessor = $postProcessor;
	}
}
