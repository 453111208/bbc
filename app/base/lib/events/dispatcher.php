<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class dispatcher
{

	/**
	 * The registered event listeners.
	 *
	 * @var array
	 */
	protected static $listeners = array();

	/**
	 * The wildcard listeners.
	 *
	 * @var array
	 */
	protected static $wildcards = array();


	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  string|array  $event
	 * @param  mixed   $listener
	 * @param  int     $priority
	 * @return void
	 */
	public static function listen($events, $listener, $priority = 0)
	{
		foreach ((array) $events as $event)
		{
			if (str_contains($event, '*'))
			{
				static::setupWildcardListen($event, $listener);
			}
			else
			{
				static::$listeners[$event][$priority][] = $this->makeListener($listener);
                unset($this->sorted[$event]);
			}
		}
	}

	/**
	 * Setup a wildcard listener callback.
	 *
	 * @param  string  $event
	 * @param  mixed   $listener
	 * @return void
	 */
	protected static function setupWildcardListen($event, $listener)
	{
		static::$wildcards[$event][] = $this->makeListener($listener);
	}

	/**
	 * Sort the listeners for a given event by priority.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected static function sortListeners($eventName)
	{
        static::$sorted[$eventName] = array();

		// If listeners exist for the given event, we will sort them by the priority
		// so that we can call them in the correct order. We will cache off these
		// sorted event listeners so we do not have to re-sort on every events.
		if (isset($this->listeners[$eventName]))
		{
			krsort($this->listeners[$eventName]);

			$this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
		}
	}
    
	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  mixed   $listener
	 * @return mixed
	 */
	public static function makeListener($listener)
	{
		if (is_string($listener))
		{
			$listener = static::createClassListener($listener);
		}

		return $listener;
	}
    
	/**
	 * Create a class based listener using the kernel single
	 *
	 * @param  mixed    $listener
	 * @return \Closure
	 */
	public static function createClassListener($listener)
	{

		return function() use ($listener)
		{
			// If the listener has an @ sign, we will assume it is being used to delimit
			// the class name from the handle method name. This allows for handlers
			// to run multiple handler methods in a single class for convenience.
			$segments = explode('@', $listener);

			$method = count($segments) == 2 ? $segments[1] : 'handle';

			//$callable = array($container->make($segments[0]), $method);
            $callable = array(kernel::single($segments[0]), $method);

			// We will make a callable of the listener instance and a method that should
			// be called on that instance, then we will pass in the arguments that we
			// received in this method into this listener class instance's methods.
			$data = func_get_args();

			return call_user_func_array($callable, $data);
		};
	}

	/**
	 * Get all of the listeners for a given event name.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	public static function getListeners($eventName)
	{
		$wildcards = static::getWildcardListeners($eventName);

		if ( ! isset($this->sorted[$eventName]))
		{
			static::sortListeners($eventName);
		}

		return array_merge(static::$sorted[$eventName], $wildcards);
	}

	/**
	 * Get the wildcard listeners for the event.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected static function getWildcardListeners($eventName)
	{
		$wildcards = array();

		foreach (static::$wildcards as $key => $listeners)
		{
			if (str_is($key, $eventName)) $wildcards = array_merge($wildcards, $listeners);
		}

		return $wildcards;
	}
    
    
	/**
	 * Fire an event and call the listeners.
	 *
	 * @param  string  $event
	 * @param  mixed   $payload
	 * @param  bool    $halt
	 * @return array|null
	 */
	public static function fire($event, $payload = array(), $halt = false)
	{
		$responses = array();

		// If an array is not given to us as the payload, we will turn it into one so
		// we can easily use call_user_func_array on the listeners, passing in the
		// payload to each of them so that they receive each of these arguments.
		if ( ! is_array($payload)) $payload = array($payload);

		static::$firing[] = $event;

		foreach ($this->getListeners($event) as $listener)
		{
			$response = call_user_func_array($listener, $payload);

			// If a response is returned from the listener and event halting is enabled
			// we will just return this response, and not call the rest of the event
			// listeners. Otherwise we will add the response on the response list.
			if ( ! is_null($response) && $halt)
			{
				array_pop(static::$firing);

				return $response;
			}

			// If a boolean false is returned from a listener, we will stop propagating
			// the event to any further listeners down in the chain, else we keep on
			// looping through the listeners and firing every one in our sequence.
			if ($response === false) break;

			$responses[] = $response;
		}

		array_pop($this->firing);

		return $halt ? null : $responses;
	}
    
    
}
