<?php namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Database\MigrationRunner;
use CodeIgniter\View\RenderableInterface;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This is used in place of a Dependency Injection container primarily
 * due to its simplicity, which allows a better long-term maintenance
 * of the applications built on top of CodeIgniter. A bonus side-effect
 * is that IDEs are able to determine what class you are calling
 * whereas with DI Containers there usually isn't a way for them to do this.
 *
 * @see http://blog.ircmaxell.com/2015/11/simple-easy-risk-and-change.html
 * @see http://www.infoq.com/presentations/Simple-Made-Easy
 */
class Services
{
	/**
	 * Cache for instance of any services that
	 * have been requested as a "shared" instance.
	 *
	 * @var array
	 */
	static protected $instances = [];

	//--------------------------------------------------------------------

	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 */
	public static function autoloader($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('autoloader');
		}

		return new \CodeIgniter\Autoloader\Autoloader();
	}

	//--------------------------------------------------------------------

	/**
	 * The cache class provides a simple way to store and retrieve
	 * complex data for later.
	 */
	public static function cache(\Config\Cache $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('cache', $config);
		}

		if (! is_object($config))
		{
			$config = new \Config\Cache();
		}

		return \CodeIgniter\Cache\CacheFactory::getHandler($config);
	}

	//--------------------------------------------------------------------

	/**
	 * The CLI Request class provides for ways to interact with
	 * a command line request.
	 */
	public static function clirequest(App $config=null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('clirequest', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\HTTP\CLIRequest(
			$config,
			new \CodeIgniter\HTTP\URI()
		);
	}

	//--------------------------------------------------------------------

	/**
	 * The CURL Request class acts as a simple HTTP client for interacting
	 * with other servers, typically through APIs.
	 */
	public static function curlrequest(array $options = [], $response = null, App $config = null, $getShared = true)
	{
		if ($getShared === true)
		{
			return self::getSharedInstance('curlrequest', $options, $response, $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		if ( ! is_object($response))
		{
			$response = new \CodeIgniter\HTTP\Response($config);
		}

		return new \CodeIgniter\HTTP\CURLRequest(
				$config,
				new \CodeIgniter\HTTP\URI(),
				$response,
				$options
		);
	}

	//--------------------------------------------------------------------

	/**
	 * The Exceptions class holds the methods that handle:
	 *
	 *  - set_exception_handler
	 *  - set_error_handler
	 *  - register_shutdown_function
	 */
	public static function exceptions(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('exceptions', $config);
		}

		if (empty($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\Debug\Exceptions($config);
	}

	//--------------------------------------------------------------------

	/**
	 * Filters allow you to run tasks before and/or after a controller
	 * is executed. During before filters, the request can be modified,
	 * and actions taken based on the request, while after filters can
	 * act on or modify the response itself before it is sent to the client.
	 */
	public static function filters($config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('filters', $config);
		}

		if (empty($config))
		{
			$config = new \Config\Filters();
		}

		return new \CodeIgniter\Filters\Filters($config, self::request(), self::response());
	}

	//--------------------------------------------------------------------

	/**
	 * The Iterator class provides a simple way of looping over a function
	 * and timing the results and memory usage. Used when debugging and
	 * optimizing applications.
	 */
	public static function iterator($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('iterator');
		}

		return new \CodeIgniter\Debug\Iterator();
	}

	//--------------------------------------------------------------------

	/**
	 * Responsible for loading the language string translations.
	 */
	public static function language(string $locale = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('language', $locale);
		}

		$locale = ! empty($locale) ? $locale : self::request()->getLocale();

		return new \CodeIgniter\Language\Language($locale);
	}

	//--------------------------------------------------------------------

	/**
	 * The file locator provides utility methods for looking for non-classes
	 * within namespaced folders, as well as convenience methods for
	 * loading 'helpers', and 'libraries'.
	 */
	public static function locator($getShared = true)
	{
	    if ($getShared)
	    {
		    return self::getSharedInstance('locator');
	    }

		return new \CodeIgniter\Autoloader\FileLocator(new \Config\Autoload());
	}

	//--------------------------------------------------------------------

	/**
	 * The Logger class is a PSR-3 compatible Logging class that supports
	 * multiple handlers that process the actual logging.
	 */
	public static function logger($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('logger');
		}

		return new \CodeIgniter\Log\Logger(new Logger());
	}

	//--------------------------------------------------------------------

	public static function migrations(BaseConfig $config = null, ConnectionInterface $db = null, bool $getShared = true)
	{
	    if ($getShared)
	    {
		    return self::getSharedInstance('migrations', $config, $db);
	    }

		$config = empty($config) ? new \Config\Migrations() : $config;

		return new MigrationRunner($config, $db);
	}

	//--------------------------------------------------------------------


	/**
	 * The Negotiate class provides the content negotiation features for
	 * working the request to determine correct language, encoding, charset,
	 * and more.
	 */
	public static function negotiator(\CodeIgniter\HTTP\RequestInterface $request=null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('negotiator', $request);
		}

		if (is_null($request))
		{
			$request = self::request();
		}

		return new \CodeIgniter\HTTP\Negotiate($request);
	}

	//--------------------------------------------------------------------

	public static function pager($config = null, RenderableInterface $view = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('pager', $config, $view);
		}

		if (empty($config))
		{
			$config = new Pager();
		}

		if (! $view instanceof RenderableInterface)
		{
			$view = self::renderer();
		}

		return new \CodeIgniter\Pager\Pager($config, $view);
	}

	//--------------------------------------------------------------------

	/**
	 * The Renderer class is the class that actually displays a file to the user.
	 * The default View class within CodeIgniter is intentionally simple, but this
	 * service could easily be replaced by a template engine if the user needed to.
	 */
	public static function renderer($viewPath = APPPATH.'Views/', $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('renderer', $viewPath);
		}

		return new \CodeIgniter\View\View($viewPath, self::locator(true), CI_DEBUG, self::logger(true));
	}

	//--------------------------------------------------------------------

	/**
	 * The Request class models an HTTP request.
	 */
	public static function request(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('request', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\HTTP\IncomingRequest(
			$config,
			new \CodeIgniter\HTTP\URI()
		);
	}

	//--------------------------------------------------------------------

	/**
	 * The Response class models an HTTP response.
	 */
	public static function response(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('response', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\HTTP\Response($config);
	}

	//--------------------------------------------------------------------

	/**
	 * The Routes service is a class that allows for easily building
	 * a collection of routes.
	 */
	public static function routes($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('routes');
		}

		return new \CodeIgniter\Router\RouteCollection();
	}

	//--------------------------------------------------------------------

	/**
	 * The Router class uses a RouteCollection's array of routes, and determines
	 * the correct Controller and Method to execute.
	 */
	public static function router(\CodeIgniter\Router\RouteCollectionInterface $routes = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('router', $routes);
		}

		if (empty($routes))
		{
			$routes = self::routes(true);
		}

		return new \CodeIgniter\Router\Router($routes);
	}

	//--------------------------------------------------------------------

	/**
	 * The Security class provides a few handy tools for keeping the site
	 * secure, most notably the CSRF protection tools.
	 */
	public static function security(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('security', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\Security\Security($config);
	}

	//--------------------------------------------------------------------

	/**
	 * @param App|null $config
	 * @param bool     $getShared
	 *
	 * @return \CodeIgniter\Session\Session
	 */
	public static function session(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('session', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		$logger = self::logger(true);

		$driverName = $config->sessionDriver;
		$driver = new $driverName($config);
		$driver->setLogger($logger);

		$session = new \CodeIgniter\Session\Session($driver, $config);
		$session->setLogger($logger);

		return $session;
	}

	//--------------------------------------------------------------------

	/**
	 * The Timer class provides a simple way to Benchmark portions of your
	 * application.
	 */
	public static function timer($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('timer');
		}

		return new \CodeIgniter\Debug\Timer();
	}

	//--------------------------------------------------------------------

	public static function toolbar(App $config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('toolbar', $config);
		}

		if (! is_object($config))
		{
			$config = new App();
		}

		return new \CodeIgniter\Debug\Toolbar($config);
	}

	//--------------------------------------------------------------------

	/**
	 * The URI class provides a way to model and manipulate URIs.
	 */
	public static function uri($uri = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('uri', $uri);
		}

		return new \CodeIgniter\HTTP\URI($uri);
	}

	//--------------------------------------------------------------------

	public static function validation($config = null, $getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('uri', $config);
		}

		if (empty($config))
		{
			$config = new \Config\Validation();
		}

		return new \CodeIgniter\HTTP\URI($config);
	}

	//--------------------------------------------------------------------

	/**
	 * View cells are intended to let you insert HTML into view
	 * that has been generated by any callable in the system.
	 */
	public static function viewcell($getShared = true)
	{
		if ($getShared)
		{
			return self::getSharedInstance('viewcell');
		}

		return new \CodeIgniter\View\Cell(self::cache());
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Utility Methods - DO NOT EDIT
	//--------------------------------------------------------------------

	/**
	 * Returns a shared instance of any of the class' services.
	 *
	 * $key must be a name matching a service.
	 *
	 * @param string $key
	 */
	protected static function getSharedInstance(string $key, ...$params)
	{
		if (! isset(static::$instances[$key]))
		{
			// Make sure $getShared is false
			array_push($params, false);

			static::$instances[$key] = self::$key(...$params);
		}

		return static::$instances[$key];
	}

	//--------------------------------------------------------------------

	/**
	 * Provides the ability to perform case-insensitive calling of service
	 * names.
	 *
	 * @param string $name
	 * @param array  $arguments
	 */
	public static function __callStatic(string $name, array $arguments)
	{
		$name = strtolower($name);

		if (method_exists(__CLASS__, $name))
		{
			return Services::$name(...$arguments);
		}
	}

	//--------------------------------------------------------------------


}
