<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Doctrine\DBAL\Logging\SQLLogger;

class base_database_logger implements SQLLogger
{

    static $mysql_query_executions = 0;
    /**
     * {@inheritdoc}
     */
    public function startQuery($sql,array $params = null, array $types = null)
    {
        logger::debug(sprintf("sql:%d %s", ++static::$mysql_query_executions, $sql), ['params' => $params, 'types' => $types]);
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }
}
