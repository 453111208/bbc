<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_application_dbtable extends base_application_prototype_filepath
{

    var $path = 'dbschema';
    var $use_db_cache = true;
    static $_define = null;
    static $force_update = false;

    static $__type_define = array();


    function __construct($app=null)
    {
        parent::__construct($app);
    }//End Function

    public function detect($app,$current=null){
        parent::detect($app, $current);
        return $this;
    }

	/**
	 * 根据实际定义的dbschema生成实际创建表的dbal schema
	 *
	 * @return \Doctrine\DBAL\Schema\Schema
	 */
    public function createTableSchema()
    {
        $appId = $this->target_app->app_id;
        $db = app::get($appId)->database();
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $table = $schema->createTable($this->real_table_name());

        $define = $this->load();
        // 建立字段
        foreach($define['columns'] as $columnName => $columnDefine)
        {
            list($type, $options) = $columnDefine['doctrineType'];
            $table->addColumn($columnName, $type, $options);
        }

        // 建立主键
        //if ($define['pkeys']) $table->setPrimaryKey($define['pkeys']);
        if ($define['primary']) $table->setPrimaryKey($define['primary']);
        // 建立索引
        if ($define['index'])
        {
            foreach((array)$define['index'] as $indexName => $indexDefine)
            {
                if (strtolower($indexDefine['prefix'])=='unique')
                {
                    $table->addUniqueIndex($indexDefine['columns'], $indexName);
                }
                else
                {
                    $table->addIndex($indexDefine['columns'], $indexName);
                }
            }
        }
        return $schema;
    }

    public function getCreateTableSql()
    {
        $appId = $this->target_app->app_id;
        $schema = $this->createTableSchema();
        $sql = current($schema->toSql(app::get($appId)->database()->getDatabasePlatform()));
        return $sql;
    }


    public function createDoctrineType($columnDefine)
    {
        $options = [];
        $options['notnull'] = ($columnDefine['required']) ? true : false;
        $convertKeys = ['autoincrement', 'comment', 'default', 'fixed', 'precision', 'scale', 'length', 'unsigned'];
        array_walk($convertKeys, function($key) use ($columnDefine, &$options) {
            if (isset($columnDefine[$key])) $options[$key] = $columnDefine[$key];
        });
        
        $type = $columnDefine['type'];
        switch (true) 
        {
            case is_array($primType =$type):
                $type = 'string';
                $options['length'] = array_reduce(array_keys($primType), function($max, $item) {
                    $itemLenth = strlen($item);
                    return $itemLenth > $max ? $itemLenth : $max;
                });
                break;
            case starts_with($type, 'table:'):
                @list(,$relatedModelString, $relatedColumnName) = explode(':', $type);
                @list($relatedModelName, $relatedModelAppId) = explode('@', $relatedModelString);
                $relatedModelAppId = $relatedModelAppId ?: $this->target_app->app_id;

                $relatedTableDefine = with(new base_application_dbtable)->detect($relatedModelAppId, $relatedModelName)->load();

                if (!$relatedColumnName)
                {
                    // 如果关联表有超过1个主键, 意味着没办法进行关联
                    if (count($relatedTableDefine['primary']) !== 1)
                    {
                        throw new InvalidArgumentException(sprintf('related table: %s %s, not have one primary key! ', $relatedModelAppId, $relatedModelName));
                    }
                    $relatedColumnName = current($relatedTableDefine['primary']);
                }

                @list($type, $relatedOptions) = $relatedTableDefine['columns'][$relatedColumnName]['doctrineType'];
                $relatedOptions = array_intersect_key($relatedOptions, array_flip(['precision', 'scale', 'fixed', 'length', 'unsigned']));
                $options = array_merge($options, $relatedOptions);
                
                break;
            case kernel::single('base_db_datatype_manage')->isExistDefine($type):
                @list($type, $initOptions) = kernel::single('base_db_datatype_manage')->getDefineDoctrineType($type);
                $initOptions = is_array($initOptions) ? $initOptions : [];
                $options = array_merge($options, array_intersect_key($initOptions, array_flip(['precision', 'scale', 'fixed', 'length', 'unsigned'])));
                break;
        }

        return [$type, $options];
        
    }

    function real_table_name()
    {
        return $this->target_app->app_id.'_'.$this->key();
    }

    public function &load($check_lastmodified=true)
    {
        $real_table_name = $this->real_table_name();

        if(!static::$_define[$real_table_name])
        {
            if (defined('CUSTOM_CORE_DIR'))
            {
                $customSchemaPath = CUSTOM_CORE_DIR.'/'.$this->target_app->app_id.'/dbschema/'.$this->key.'.php';
                if (file_exists($customSchemaPath)) $path = $customSchemaPath;
            }
            if (!$path)
            {
                $path = $this->target_app->app_dir.'/dbschema/'.$this->key.'.php';
            }
            
            $lastModified = $check_lastmodified ? filemtime($path) : null;

            // 当未安装时 或者 涉及到base kvstore 或者取不到数据
            if ((!kernel::is_online()) || ($this->target_app->app_id=='base' && $this->key()=='kvstore'))
            {
                $define = $this->loadDefine($path);
            }
            else
            {
                if (!base_kvstore::instance('tbdefine')->fetch($this->target_app->app_id.$this->key, $define, $lastModified))
                {
                    $define = $this->loadDefine($path);
                    base_kvstore::instance('tbdefine')->store($this->target_app->app_id.$this->key, $define);
                }
            }
            static::$_define[$real_table_name] = $define;
        }

        return static::$_define[$real_table_name];
    }

    public function refreshLoad($path, $tableName)
    {
        static::$_define[$tableName] = $this->loadDefine($path);
    }

    public function loadDefine($path)
    {
        $define = require($path);
        
        foreach($define['columns'] as $k=>$v)
        {
            //if($v['pkey']) $define['idColumn'][$k] = $k;
            if($v['is_title']) $define['textColumn'][$k] = $k;
            if($v['in_list'])
            {
                $define['in_list'][] = $k;
                if($v['default_in_list']) $define['default_in_list'][] = $k;
            }
            $define['columns'][$k]['doctrineType'] = $this->createDoctrineType($v);
        }
        // idcColumn 为兼容原有逻辑, 后续会同一用primary进行处理 
        if (isset($define['primary']))
        {
            $define['idColumn'] = $define['primary'] = (array)$define['primary'];
        }

        if(!$define['idColumn'])
        {
            $define['idColumn'] = key($define['columns']);
        }
        elseif(count($define['idColumn'])==1)
        {
            $define['idColumn'] = current($define['idColumn']);
        }

        if(!$define['textColumn'])
        {
            $keys = array_keys($define['columns']);
            $define['textColumn'] = $keys[1];
        }
        elseif(count($define['idColumn'])==1)
        {
            $define['textColumn'] = current($define['textColumn']);
        }

        return $define;
    }

    function current()
    {
        $this->key = substr($this->iterator()->getFilename(),0,-4);
        return $this;
    }

    function filter(){
        return substr($this->iterator()->getFilename(),-4,4)=='.php' && is_file($this->getPathname());
    }

    function install()
    {
        $appId = $this->target_app->app_id;
        $db = app::get($appId)->database();
        
        $schema = $this->createTableSchema();
        $sql = current($schema->toSql(app::get($appId)->database()->getDatabasePlatform()));
        $real_table_name = $this->real_table_name();

        logger::info('Creating table '.$real_table_name);
        if ($db->getSchemaManager()->tablesExist($real_table_name))
        {
            $db->getSchemaManager()->dropTable($real_table_name);
        }
        $db->exec($sql);
    }

    public function update($appId)
    {
        foreach($this->detect($appId) as $item)
        {
            $item->updateTable();
        }
    }

    public function updateTable($schema, $saveMode = true)
    {
        $appId = $this->target_app->app_id;
        $db = app::get($appId)->database();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();

        $real_table_name = $this->real_table_name();
        //echo $real_table_name.PHP_EOL;
        $toSchema = $this->createTableSchema();

        // 如果存在原始表, 则通过原始表建立schema对象
        if ($db->getSchemaManager()->tablesExist($real_table_name))
        {
            $fromSchema = new \Doctrine\DBAL\Schema\Schema([$db->getSchemaManager()->listTableDetails($real_table_name)], [], $db->getSchemaManager()->createSchemaConfig());
        }
        // 否则建立空schema
        else
        {
            $fromSchema = new \Doctrine\DBAL\Schema\Schema();
        }
        
        $comparator = new \Doctrine\DBAL\Schema\Comparator();

        $schemaDiff = $comparator->compare($fromSchema, $toSchema);

        // 非安全模式
        if (!$saveMode)
        {
            $queries = $schemaDiff->toSql($db->getDatabasePlatform());
        }
        // 安全模式, 删除drop columns的相关语句
        else
        {
            $queries = $schemaDiff->toSaveSql ($db->getDatabasePlatform());
            // var_dump($queries);
            if ($queries)
            {
                reset($schemaDiff->changedTables);
                $changeTable = current($schemaDiff->changedTables);
                $changeTable->removedColumns = [];
                $queries = $schemaDiff->toSaveSql($db->getDatabasePlatform());
            }
        }
        
        foreach($queries as $sql)
        {
            logger::info($sql);
            $db->exec($sql);
        }
    }

    public function last_modified($appId)
    {
        if(self::$force_update){
            return time()+999999;
        }else{
            return parent::last_modified($appId);
        }
    }

	/**
	 * 获取指定app
	 *
	 * @return \Doctrine\DBAL\Schema\Schema
	 */
    public function getAppTableNames($appId)
    {
        $db = app::get($appId)->database();
        $prefix = $appId.'_';
        $sm = $db->getSchemaManager();
        $tableNames = $sm->listTableNames();
        $appTableNames = [];

        array_walk($tableNames, function($tableName) use (&$appTableNames, $prefix) {
            if (starts_with($tableName, $prefix))
            {
                $appTableNames[] = $tableName;
            }
        });
        return $appTableNames;
    }

    public function clear_by_app($appId)
    {
        $db = app::get($appId)->database();

        $tableNames = $this->getAppTableNames($appId);

        foreach($tableNames as $tableName)
        {
            $db->getSchemaManager()->dropTable($tableName);
        }

    }

    public function pause_by_app($appId)
    {
        $db = app::get($appId)->database();
        $suffix = '_'.substr(md5('dbtable_'.$appId), 0, 16);

        // 包含临时表和真实表
        $tableNames = $this->getAppTableNames($appId);
        
        foreach($tableNames as $tableName)
        {
            if (ends_with($tableName, $suffix))
            {
                $tmpTableNames[] = $tableName;
            }
            else
            {
                $appTableNames[] = $tableName;
            }
        }
        // 删除临时表
        foreach($tmpTableNames as $tableName)
        {
            $db->getSchemaManager()->dropTable($tableName);
        }
        // 变更真实表为临时表
        foreach($appTableNames as $tableName)
        {
            $newTableName = $tableName.$suffix;
            $db->getSchemaManager()->renameTable($tableName, $newTableName);
            logger::info(sprintf('%s backup to %s', $tableName, $newTableName));
        }
    }//End Function

    public function active_by_app($appId)
    {
        $db = app::get('$appId')->database();
        $suffix = '_'.substr(md5('dbtable_'.$appId), 0, 16);

        $tmpTableNames = $this->getAppTableNames($appId);
        foreach($tmpTableNames as $tmpTableName)
        {
            $tableName = substr($tmpTableName, 0, strlen($suffix));
            if (ends_with($tmpTableName, $suffix))
            {
                $db->getSchemaManager()->renameTable($tmpTableName, $tableName);
                logger::info(sprintf('%s restore', $tableName));
            }
        }
    }//End Function
}
