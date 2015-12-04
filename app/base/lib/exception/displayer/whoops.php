<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Exception;
use Whoops\Run;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class base_exception_displayer_whoops  implements base_exception_displayer_interface
{

	/**
	 * The Whoops run instance.
	 *
	 * @var \Whoops\Run
	 */
	protected $whoops;

	/**
	 * Indicates if the application is in a console environment.
	 *
	 * @var bool
	 */
	protected $runningInConsole;

	/**
	 * Create a new Whoops exception displayer.
	 *
	 * @param  \Whoops\Run  $whoops
	 * @param  bool  $runningInConsole
	 * @return void
	 */
	public function __construct(Run $whoops, $runningInConsole)
	{
		$this->whoops = $whoops;
		$this->runningInConsole = $runningInConsole;
	}

	/**
	 * Display the given exception to the user.
	 *
	 * @param  \Exception  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function display(Exception $exception)
	{
		$status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
		$headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();
		return new Response($this->whoops->handleException($exception), $status, $headers);
	}

}

