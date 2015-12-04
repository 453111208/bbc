<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Monolog\Logger as Monolog;
use base_log_writer as Writer;


class base_facades_log extends base_facades_facade
{

    protected static $__log;

    protected static $__environment = 'production';


    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        if (!static::$__log)
        {
            static::$__log = new Writer(
                new Monolog(static::$__environment)
            );
            static::configureHandlers(static::$__log);
        }
        
        return static::$__log;
    }

    protected static function configureHandlers(Writer $log)
    {
        $method = 'configure'.ucfirst(config::get('log.log')).'Handler';
        static::{$method}($log);
    }

    protected static function configureSingleHandler(Writer $log)
    {
        $log->useFiles(DATA_DIR.'/logs/luckymall.php', config::get('log.record_level'));
    }

    protected static function configureDailyHandler(Writer $log)
    {
        $log->useDailyFiles(DATA_DIR.'/logs/luckymall.php', 30, config::get('log.record_level'));
    }

    protected function configureSyslogHandler(Writer $log)
    {
        $log->useSyslog('luckymall', config::get('log.record_level'));
    }

	protected static function configureErrorlogHandler(Writer $log)
	{
		$log->useErrorLog(config::get('log.record_level'));
	}
    
}

