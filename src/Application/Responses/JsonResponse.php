<?php

namespace Tripomatic\NetteApi\Application\Responses;

use Nette\Application;
use Nette\Http;
use Nette\Utils\Json;

class JsonResponse implements Application\IResponse
{
	const HTTP_100_CONTINUE = 100;
	const HTTP_101_SWITCHING_PROTOCOLS = 101;
	const HTTP_102_PROCESSING = 102;
	const HTTP_200_OK = 200;
	const HTTP_201_CREATED = 201;
	const HTTP_202_ACCEPTED = 202;
	const HTTP_203_NON_AUTHORITATIVE_INFORMATION = 203;
	const HTTP_204_NO_CONTENT = 204;
	const HTTP_205_RESET_CONTENT = 205;
	const HTTP_206_PARTIAL_CONTENT = 206;
	const HTTP_207_MULTI_STATUS = 207;
	const HTTP_300_MULTIPLE_CHOICES = 300;
	const HTTP_301_MOVED_PERMANENTLY = 301;
	const HTTP_302_FOUND = 302;
	const HTTP_303_SEE_OTHER = 303;
	const HTTP_304_NOT_MODIFIED = 304;
	const HTTP_305_USE_PROXY = 305;
	const HTTP_306_SWITCH_PROXY = 306;
	const HTTP_307_TEMPORARY_REDIRECT = 307;
	const HTTP_400_BAD_REQUEST = 400;
	const HTTP_401_UNAUTHORIZED = 401;
	const HTTP_402_PAYMENT_REQUIRED = 402;
	const HTTP_403_FORBIDDEN = 403;
	const HTTP_404_NOT_FOUND = 404;
	const HTTP_405_METHOD_NOT_ALLOWED = 405;
	const HTTP_406_NOT_ACCEPTABLE = 406;
	const HTTP_407_PROXY_AUTHENTICATION_REQUIRED = 407;
	const HTTP_408_REQUEST_TIMEOUT = 408;
	const HTTP_409_CONFLICT = 409;
	const HTTP_410_GONE = 410;
	const HTTP_411_LENGTH_REQUIRED = 411;
	const HTTP_412_PRECONDITION_FAILED = 412;
	const HTTP_413_REQUEST_ENTITY_TOO_LARGE = 413;
	const HTTP_414_REQUEST_URI_TOO_LONG = 414;
	const HTTP_415_UNSUPPORTED_MEDIA_TYPE = 415;
	const HTTP_416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const HTTP_417_EXPECTATION_FAILED = 417;
	const HTTP_418_IM_A_TEAPOT = 418;
	const HTTP_422_UNPROCESSABLE_ENTITY = 422;
	const HTTP_423_LOCKED = 423;
	const HTTP_424_FAILED_DEPENDENCY = 424;
	const HTTP_425_UNORDERED_COLLECTION = 425;
	const HTTP_426_UPGRADE_REQUIRED = 426;
	const HTTP_449_RETRY_WITH = 449;
	const HTTP_450_BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450;
	const HTTP_500_INTERNAL_SERVER_ERROR = 500;
	const HTTP_501_NOT_IMPLEMENTED = 501;
	const HTTP_502_BAD_GATEWAY = 502;
	const HTTP_503_SERVICE_UNAVAILABLE = 503;
	const HTTP_504_GATEWAY_TIMEOUT = 504;
	const HTTP_505_HTTP_VERSION_NOT_SUPPORTED = 505;
	const HTTP_506_VARIANT_ALSO_NEGOTIATES = 506;
	const HTTP_507_INSUFFICIENT_STORAGE = 507;
	const HTTP_509_BANDWIDTH_LIMIT_EXCEEDED = 509;
	const HTTP_510_NOT_EXTENDED = 510;

	public static $messages = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended'
	];

	/** @var array|\stdClass */
	private $data;

	/** @var int */
	private $code;

	/** @var string */
	private $contentType = 'application/json; charset=utf-8';

	/** @var int */
	private $expiration;

	/** @var callable */
	private $postProcessor;

	/**
	 * @param mixed[]|\stdClass $data
	 * @param int $code
	 * @param int|null $expiration
	 */
	public function __construct($data, $code = Http\IResponse::S200_OK, $expiration = null)
	{
		$this->data = $data;
		$this->code = $code;
		$this->expiration = $expiration;
	}

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setCode($this->code);
		$httpResponse->setExpiration($this->expiration);
		$httpResponse->setHeader('Pragma', $this->expiration ? 'cache': 'no-cache');

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
	 * @param int|null $expiration
	 */
	public function setExpiration($expiration)
	{
		$this->expiration = $expiration;
	}

	/**
	 * @return int|null
	 */
	public function getExpiration()
	{
		return $this->expiration;
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
