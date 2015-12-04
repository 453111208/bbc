<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use \Doctrine\DBAL\Configuration;
use \Doctrine\DBAL\DriverManager;


class base_database_manager
{

	/**
	 * The Doctrine2 Database configuration instance
	 *
	 * @var \Doctrine\DBAL\Configuration
	 */
    protected $configuration = null;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = array();
    

    public function __construct(Configuration $config = null)
    {
        if (!$config)
        {
            $config = new Configuration;
        }

        $this->configuration = $config;
    }
    
    
	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return Connection
	 */
	public function connection($name = null)
    {
        list($name, $type) = $this->parseConnectionName($name);
        //todo: 在laravel中connections write和read 对应一条记录. 但在doctrine2中是否要对应连个记录

        //todo: 临时处理方式, 
        if (!$this->isExistConfig($name))
        {
            $name = $this->getDefaultConnection();
        }
        
        if ( ! isset($this->connections[$name]))
        {
            $connection = $this->makeConnection($name);

            $this->connections[$name] = $connection;
        }

        // 当指定了type, 同时使用master slave模式的情况下, 设置master或者slave 
        if ( !is_null($type) && $this->connections[$name] instanceof Doctrine\DBAL\Connections\MasterSlaveConnection)
        {
            $this->connections[$name]->connect($type);
        }

        return $this->connections[$name];
    }

	/**
	 * Make the database connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	protected function makeConnection($name)
	{
		$config = $this->getConfig($name);

        $connectionParams = $config;

        # start
        if (isset($config['slave']) || isset($config['master']))
        {
            $connectionParams['wrapperClass'] = 'Doctrine\DBAL\Connections\MasterSlaveConnection';
        }

        $conn = DriverManager::getConnection($connectionParams, $this->configuration);
        # end

        return $conn;
	}
    

	/**
	 * Parse the connection into an array of the name and read / write type.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function parseConnectionName($name)
	{
		$name = $name ?: $this->getDefaultConnection();

		return ends_with($name, ['::master', '::slave'])
                            ? explode('::', $name, 2) : [$name, null];
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection()
    {
        return config::get('database.default');
    }

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name)
    {
        config::set('database.default', $name);
    }

	/**
	 * Get the database Config
	 *
	 * @param  string  $name
	 * @return void
	 */
    public function getConfig($name = null)
    {
        $defaultName = $this->getDefaultConnection();
        
        $name = $name ?: $defaultName;

        $connections = config::get('database.connections');

        // 如果指定的配置为空, 则使用默认的数据库设置
		if (is_null($config = array_get($connections, $name)))
		{
            if (is_null($config = array_get($connections, $defaultName)))
            {
                throw new \InvalidArgumentException("Database [$defaultName] not configured.");
            }
		}

        return $config;
    }

    public function isExistConfig($name = null)
    {
        if ($name === null) return false;

        return !is_null(array_get(config::get('database.connections'), $name, null));
    }
}
