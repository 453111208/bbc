<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use \Doctrine\DBAL\Configuration;
use \Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Exception\UniqueConstraintViolationException as UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException as NotNullConstraintViolationException;


class dbal extends PHPUnit_Framework_TestCase
{
    protected $conn = null;

    public function setUp()
    {

        
        $connectionParams = config::get('database.connections.default');
        
        /*
        $connectionParams = array(
            'dbname' => $config['database'],
            'user' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'driver' => 'mysqli',
        );
        */

        $this->conn = DriverManager::getConnection($connectionParams, new Configuration);
    }

    public function testAAA()
    {
        echo 33333;
        var_dump($flag = app::get('sysitem')->database()->executeUpdate('update sysitem_sku_store set store=store-1001 where item_id=34'));
        echo '-------';
        var_dump('+++'.$flag.'-----');
        exit;
    }

	/**
	 * 批量更改数据库名称. 去掉sdb_前缀
	 *
	 * @return void
	 */
    public function testMasterSlave()
    {

        $config = config::get('database');
        $connectionParams = array(
            'master' => array('host' => '127.0.0.1', 'dbname'=>'bbc', 'user'=>'root', 'password'=>''),
            //'slaves' => array(array('host' => '127.0.0.1')),
            'slaves' =>  [array('host' => '127.0.0.1', 'dbname'=>'bbc', 'user'=>'root', 'password'=>'')],
            'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
            //            'host' => $config['host'],
            'charset' => 'utf8',
            'driver' => 'mysqli',
        );
        $conn = DriverManager::getConnection($connectionParams, new Configuration);

        $stmt = $conn->query('select * from sysuser_user');
        while ($row = $stmt->fetch()){
                    var_dump($row);
        }
        exit;
        
    }

	/**
	 * 濞村鐦痯arse comments
	 *
	 * @return void
	 */
    public function testCompileComments()
    {
        return;
        $objMdlItem = app::get('sysitem')->model('item');
        $item = array (
            'sku' => 
            array (
                0 => 
                array (
                    'price' => '12',
                    'store' => 1,
                    'bn' => 'dsdff',
                    'weight' => '1',
                    'title' => 'vvv',
                    'sku_store' => 
                    array (
                        'store' => 1,
                        'freez' => NULL,
                    ),
                ),
            ),
            'spec' => NULL,
            'shop_cids' => 
            array (
                0 => '65',
            ),
            'title' => 'vvv',
            'sub_title' => 's',
            'brand_id' => '1',
            'bn' => 'dsdff',
            'use_platform' => '0',
            'price' => '12',
            'store' => '1',
            'sub_stock' => '0',
            'mkt_price' => '1',
            'cost_price' => '1',
            'weight' => '1',
            'order_sort' => '1',
            'desc' => 
            array (
                'pc_desc' => 'dsfbddf',
                'wap_desc' => '',
            ),
            'wap_desc' => '',
            'shop_id' => 9,
            'cat_id' => '77',
            'approve_status' => 'instock',
            'shop_cat_id' => '65',
            'is_selfshop' => 1,
            'nospec' => '1',
            'image_default_id' => 'a2862735f38bcdc992e6eb156f7144f2',
            'list_image' => 'a2862735f38bcdc992e6eb156f7144f2',
            'list_status' => 
            array (
                'shop_id' => 9,
                'approve_status' => 'instock',
            ),
            'props' => 
            array (
            ),
            'created_time' => 1432267725,
            'modified_time' => 1432267725,
        );
        $objMdlItem->save($item);

        /*
        $stmt = $this->conn->query('select * from sysuser_user');
        while ($row = $stmt->fetch()){
            //            var_dump($row);
        }
     */
        //        $result = $this->compiler->compile('<{* *}>ddd');
        //        $this->assertEquals((array)$result, (array)'ddd', 'xiaotiantian');
    }


	/**
	 * 批量更改数据库名称. 去掉sdb_前缀
	 *
	 * @return void
	 */
    public function testQueryBuilder()
    {

        $db = db::connection();
        echo $db->quoteIdentifier('`as`');
        exit;
        try{
            logger::info('33');
            logger::info('44');
        }
        catch (Exception $e) {
            var_dump($e);
            echo 44;
            
        }
        
        exit;
        $sm = $this->conn->getSchemaManager();
        foreach( $sm->listTableNames() as $tableName )
        {
            list(, $newTableName) = explode('sdb_', $tableName);
            if (!is_null($newTableName))
            {
                $sm->renameTable($tableName, $newTableName);
            }
        }
    }


    public function testLuckymallDB()
    {

        $conn = db::connection();
        $myPlatform = $conn->getDatabasePlatform();
        
        $schema = new \Doctrine\DBAL\Schema\Schema();
        $myTable = $schema->createTable("my_table");
        $myTable->addColumn("id", "integer", array("unsigned" => true));
        $myTable->addColumn("username", "string", array("length" => 32));
        $myTable->addColumn("booltest", "array");
        $myTable->addColumn("xx", "object");
        
        $myTable->setPrimaryKey(array("id"));
        $myTable->addUniqueIndex(array("username"));
        $myTable->addColumn("money", 'decimal', ['precision'=>10, 'scale' =>3]);
        //        $schema->createSequence("my_table_seq");

        $myForeign = $schema->createTable("my_foreign");
        $myForeign->addColumn("id", "integer");
        $myForeign->addColumn("user_id", "integer");
        $myForeign->addColumn("money", 'decimal', ['precision'=>10, 'scale' =>3]);
        $myForeign->addForeignKeyConstraint($myTable, array("user_id"), array("id"), array("onUpdate" => "CASCADE"));

        $queries = $schema->toSql($myPlatform); // get queries to create this schema.
        $dropSchema = $schema->toDropSql($myPlatform);
        //var_dump($queries);
    }

    public $define =  array(
        'columns' => array(
            'cart_id' => array(
                'type' => 'number',
                'autoincrement' => true,
                'pkey' => true,
                'required' => true,
            ),
            /*
            'user_ident' => array(
                'type' => 'varchar(50)',
                'required' => true,
                'comment' => '会员ident,会员信息和session生成的唯一值',
            ),
            'user_id' => array(
                'type' => 'int(8) ',
                'pkey' => true,
                'required' => true,
                'default' => -1,
                'label' => '会员id',
                'editable' => false,
            ),
            'shop_id'=> array(
                'type'=>'number',
                'required' => true,
                'comment' => '店铺ID',
            ),
            'obj_type' => array(
                'type' => 'varchar(20)',
                'required' => true,
                'label' => '购物车对象类型',
            ),
            'item_id' => array(
                'type' => 'number',
                'required' => true,
                'label' => 'item_id',
            ),
            'sku_id' => array(
                'type' => 'number',
                'required' => true,
                'label' => '购物车对象参数',
            ),
            'quantity' => array(
                'type' => 'float unsigned',
                'required' => true,
                'label' => '数量',
            ),
            'is_checked' => array(
                'type' => 'bool',
                'default' => '0',
                'required' => true,
                'label' => '是否购物车选中',
            ),
            'selected_promotion' => array(
                'type' => 'varchar(30)',
                'default' => '',
                'required' => true,
                'label' => '购物车选中的促销ID',
            ),
            'created_time' => array(
                'type' => 'time',
                'label' => '创建时间',
            ),
            'modified_time' => array(
                'type' => 'time',
                'label' => '最后修改时间',
            ),
            */
                
        ),
        /*
        'index' => array(
            'ind_sku_id' => array(
                'columns' => array(
                    0 => 'sku_id',
                    1 => 'user_ident',
                ),
            ),
            'ind_shop_id' => array(
                'columns' => array(
                    0 => 'shop_id',
                ),
            ),
        ),
        */
        'engine' => 'innodb',
        'version' => '$Rev: 40912 $',
        'unbackup' => true,
        'ignore_cache' => true,
        'comment' => '购物车',
    );

    public function testSchema()
    {
        exit;
        $model = app::get('base')->model('setting');
        $filter = ['app|than' => 2,
                   'app|lthan' => 2,
                   'app|nequal' => 2,
                   'app|notin' => array(1,2,3,4),
                   'app|in' => array(1,2,3,4),
                   'app|between' => [1,10],
                   'app|has' => 'asdk',
                   
                   
        ];
        echo ($model->_filter($filter));
        exit;
        
        $filter =array (
            'key' => 'aa4c2040d3220c132cf2fed9e31fb9ce324e137a',
            'app' =>'kkk'
        );

        var_dump(app::get('base')->model('setting')->getList('app, key', $filter));
        exit;
        $db = app::get('system')->database();
        $db->exec('set SESSION autocommit=1;');
        $db->exec('set @msgID = -1;');
        var_dump($db->executeQuery('select @msgID')->fetchColumn());
        exit;
        
        
        var_dump(with(new base_application_dbtable)->getAppTableNames('base'));
        exit;
        var_dump(url::to('/api'));exit;
        //$schema = new base_application_dbtable;
        //$dbinfo = $schema->detect($this->app,$this->table_name())->load();

        if ($db->getSchemaManager()->tablesExist($real_table_name))
        {
            $db->getSchemaManager()->dropTable($real_table_name);
        }
        
        
        //$appId = $this->target_app->app_id;
        $appId = 'syscategory';
        $db = app::get($appId)->database();
        $schema = new \Doctrine\DBAL\Schema\Schema();
        //$table = $schema->createTable($this->real_table_name());
        $table = $schema->createTable('syscategory_brand');

        //$define = $this->load();
        //$define = with(new base_application_dbtable)->detect($appId, 'brand')->load();
        $define = $this->define;

        
        // 建立字段
            
        $options['precision'] = 20;
        $options['scale'] = 3;
        $options['length'] = 0;
        $options['unsigned'] = 10;
        $options['fixed'] = true;
        //            $options['unique'] = true;
            
        //$realType = $columnDefine['realtype'];
        $realType = 'integer';

        $table->addColumn($columnName, $realType, $options);

        // 建立主键
        if ($define['primary']) $table->setPrimaryKey($define['primary']);
        // 建立索引
        if ($define['index'])
        {
            foreach($define['index'] as $indexName => $indexDefine)
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
        $platform = $db->getDatabasePlatform();
        $queries = $schema->toSql($platform);
        var_dump($queries);exit;
    }

    /*
    public function testSave() {
        
        $data = array (
            'name' => 'dddd',
            'status' => '0',
            'account_id' => '76',
            'role' => 
            array (
                1 => '1',
                2 => '2',
            ),
            'pam_account' => 
            array (
                'account_id' => '76',
            ),
            'roles' => 
            array (
                0 => 
                array (
                    'role_id' => '1',
                ),
                1 => 
                array (
                    'role_id' => '2',
                ),
            ),
            'user_id' => '76',
        );
        $data = array (
            'name' => 'dddd',
            'status' => '0',
            'account_id' => '76',
            'role' => 
            array (
                1 => '1',
                2 => '2',
            ),
            'pam_account' => 
            array (
                'account_id' => '76',
            ),
            'roles' => 
            array (
                0 => 
                array (
                    'role_id' => '1',
                    'user_id' => '76',
                ),
                1 => 
                array (
                    'role_id' => '2',
                    'user_id' => '76',
                ),
            ),
            'user_id' => '76',
        );
        $users = app::get('desktop')->model('users');
        var_dump($users->save($data));
        echo 33;

        exit;
    }
    */

    /*
      $analysis_info = app::get('ectools')->model('analysis')->select()->columns('*')->where('service = ?', $params['service'])->instance()->fetch_row();
        if(empty($analysis_info))   return array('categories'=>array(), 'data'=>array());
        $obj = app::get('ectools')->model('analysis_logs')->select()->columns('target, flag, value, time')->where('analysis_id = ?', $analysis_info['id']);
        $obj->where('target = ?', $params['target']);
        $obj->where('time >= ?', strtotime(sprintf('%s 00:00:00', $params['time_from'])));
        $obj->where('time <= ?', strtotime(sprintf('%s 23:59:59', $params['time_to'])));
        if(isset($this->_params['type']))   $obj->where('type = ?', $params['type']);
        $rows = $obj->instance()->fetch_all();      
     */

    /*
        $limit = 2;
        $model = app::get('image')->model('image');
        $db = kernel::database();
        if($params['filter']['image_id']=='_ALL_'||$params['filter']['image_id']=='_ALL_')
        {
            unset($params['filter']['image_id']);
        }
        $where = $model->_filter($params['filter']);
        $where .= ' and last_modified<='.$params['queue_time'];
        $rows = $db->select('select image_id from image_image where '.$where.' order by last_modified desc limit '.$limit);
        $objLibImage = kernel::single('image_data_image');
        foreach($rows as $r)
        {
            $watermark = (bool)$params['watermark'];
            $objLibImage->rebuild($r['image_id'],$params['size'], $watermark);
        }
        return app::get('image')->database()->executeQuery('select count(*) as c from image_image where '.$where)->fetchColumn();
      
     */

    public function _filter()
    {
        return 1;
    }
    
    public function testaInsertSerialize()
    {

        
        $accountShopModel = app::get('sysshop')->model('account');
$data = array(
    'login_account' => 'xinxin',
    'createtime' => '1432187042',
    'modified_time' => '1432187042',
    'login_password' => '$2y$10$kAuKY2zfMiseYU9s6ejS.OSpmcZswlkP0dy6hFE',
);
$sellerId = $accountShopModel->insert($data);
 var_dump($sellerId);

 exit;
        echo 99;
$dbtable = new base_application_dbtable;
        $schema = $dbtable->detect('base', 'apps')->getCreateTableSql();
        var_dump($schema);
        exit;
        /*
        $db = db::connection();
        $db1 = db::connection('test');
        $platform = $db->getDatabasePlatform();
        $platform1 = $db1->getDatabasePlatform();
        $schema = $db->getSchemaManager()->createSchema();
        $schema1 = $db1->getSchemaManager()->createSchema();

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($schema, $schema1);

        var_dump($schemaDiff->toSql($platform));
        exit;
        
        var_dump($schema->toSql($platform));
        exit;
        */        
        
        $dbtable = new base_application_dbtable;
        $schema = $dbtable->detect('base', 'apps')->getCreateTableSql();
        var_dump($schema);exit;
        //        var_dump($dbinfo->toSql(db::connection()->getDatabasePlatform()));
        //----
        $platform = db::connection()->getDatabasePlatform();
        
        $db1 = db::connection('test');
        $platform1 = $db1->getDatabasePlatform();
        $schemaManager = $db1->getSchemaManager();
        //        var_dump($db1->getSchemaManager()->createSchemaConfig());exit;
        $schema1 = new \Doctrine\DBAL\Schema\Schema([$schemaManager->listTableDetails('base_apps')], [], $db1->getSchemaManager()->createSchemaConfig());

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($schema1, $schema);
        var_dump($schemaDiff->toSql($platform));

        exit;
        
        //        var_dump($dbinfo);exit;
        exit;
        $define = ['precision'=>10, 'aa' =>2 , 'fixed' => 'dddd', 'fff'];
        // var_dump(array_merge($a, $b));
        exit;
        
        var_dump(array_intersect_key($a, array_flip(['precision', 'scale', 'fixed'])));
        exit;
        $app_id = 'base';
        $db = kernel::database();
        //$rows = $db->select(sprintf("show tables like '%s'", $app_id.'\_%'));
        $rows = $db->select("show tables like 'base%'", $app_id.'\_%');
        var_dump($rows);
        exit;
        $app = 'base';
        $db = app::get('base')->database();
        $rows = $db->executeQuery('SHOW TABLE STATUS like '. $db->quote($app.'\_%'))->fetchAll();
        var_dump($rows);
        exit;

        
        $app = 'base';
        $db = app::get('base')->database();
        //$tables = $db->executeQuery('SHOW TABLE STATUS like '. $db->quote($app.'%'))->fetchAll();
        $tables = $db->executeQuery('SHOW TABLE STATUS like ?', [$app.'%'], [\PDO::PARAM_STR])->fetchAll();
        var_dump($tables);
        exit;
        $time = time();
        $db = app::get('base')->database();
        $count = $db->executeQuery('SELECT count(*) FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<?',
                            [$db->quote($time, \PDO::PARAM_INT)])->fetchColumn();
        var_dump($count);
        exit;
        $qb = app::get('sysstat')->database()->createQueryBuilder();
        $catId = 'asdf';
        echo $qb->getConnection()->delete('syscategory_cat', ['cat_id' => $catId], [\PDO::PARAM_INT]);
        exit;
        
        $file = 'wapmall/index.html';
        $slots['wapmall/index.html'] = array(120, 121);
        
        $model = app::get('site')->model('widgets_instance');
        //        $rows = $model->database()->executeQuery('select * from site_widgets_instance where widgets_id not in(?) and core_file=?', [$slots[$file], $file], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY, \PDO::PARAM_STR])->fetchAll();
        echo $model->database()->delete('site_widgets_instance', ['core_file' => $file]);
        exit;
        var_dump($rows);
        exit;
        
        $sTheme = 'a';
        $flag = app::get('site')->database()->executeUpdate('delete from site_widgets_instance where core_file like ?', [$sTheme.'%']);
        var_dump($flag);
        exit;

        
        var_dump(app::get('image')->database()->executeUpdate('update image_image SET last_modified = last_modified + 1'));
        exit;
        $tids= [10];
                $status = 'WAIT_SELLER_SEND_GOODS';
        $db = app::get('systrade')->database();
        return $db->executeQuery('SELECT count(*) as ready_send_trade ,O.shop_id as shop_id ,sum(O.payment) as ready_send_fee FROM
            systrade_trade as O  where O.tid in (?) and O.status=? group by shop_id', [$tids, $status], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY])->fetchAll();
        exit;
        $db = app::get('systrade')->database();
        var_dump(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $rows = $db->executeQuery('select * from sysitem_item where item_id in (?)', [[29]], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY])->fetchAll();
        var_dump($rows);
        

        exit;
        
        $qb = app::get('sysstat')->database()->createQueryBuilder();
        
        $model = app::get('sysshop')->model('shop_info');
        $data = [
            'info_id'=>1,
            'company_name' => 'kkajsdf',
            'license_num' => 3334,
            'bank_name' => 'kkajsdf',
        ];

        
        var_dump($model->save($data));

        exit;
        $theme = 'luckymall';
        $qb = app::get('site')->database()->createQueryBuilder();
        $aWidget['widgets'] = $qb->select('*')->from('site_widgets_instance')->where($qb->expr()->like('core_file', $qb->createPositionalParameter($theme.'%')))->execute()->fetchAll();
        var_dump($aWidget);
        exit;
        
        $aWidget['widgets'] = app::get('site')->model('widgets_instance')->select()->where("core_file LIKE '".$theme."/%'")->instance()->fetch_all();

        
        $db = app::get('sysstat')->database();
        $rows = $db->executeQuery('select already_send_fee as order_amount,already_send_trade as order_nums,createtime as mydate from sysstat_trade_statics  where createtime>=? and createtime<? group by createtime', [$to, $from], [\PDO::PARAM_INT, \PDO::PARAM_INT])->fetchAll();
        var_dump($rows);
        exit;
        
        $rows = $db->select('select already_send_fee as order_amount,already_send_trade as order_nums,createtime as mydate from sysstat_trade_statics  where createtime>='.intval($to).' and createtime<'.intval($from).' group by createtime');
        
        
        $data = app::get('systrade')->database()->executeQuery('SELECT count(*) as num,result FROM `sysrate_traderate` WHERE `shop_id`=? and created_time >=? and created_time < ? group by result', [$shopId, $startTime, $endTime])->fetchAll();
        var_dump($data);
        exit;
        $data = app::get('systrade')->database()->executeQuery('SELECT count(*) as num,result FROM `sysrate_traderate` WHERE `shop_id`=? and created_time >=? and created_time < ? group by result', [$shopId, $startTime, $endTime])->fetchAll();
        exit;
        var_dump($itemList);exit;
        app::get('base')->model('apps')->update(['app_name'=>''], ['app_id'=>'toputil']);
        exit;
        $qb = app::get('site')->database()->createQueryBuilder();
        $theme = 'luckymall';
        $selectObj = $qb->select('*')->from('site_widgets_instance')->where('core_file='.$qb->createPositionalParameter($file))->orderBy('widgets_order', 'asc');

        
        $selectObj = app::get('site')->model('widgets_instance')->select()->where('core_file = ?', $file)->order('widgets_order ASC');
        exit;
        $data = $qb->select('*')->from('site_widgets')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        var_dump($data);
        //$data = app::get('site')->model('widgets')->select()->where('theme = ?', $theme)->instance()->fetch_all();
        exit;
        $data = app::get('site')->model('widgets')->select()->where('theme = ?', $theme)->instance()->fetch_all();

        
        $data = $qb->select('*')->from('site_widgets')->where('app!=\'\'')->orWhere('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        var_dump($data);exit;
        
        //$data = app::get('site')->model('widgets')->select()->where('app != ?', '')->or_where('theme = ?', $theme)->instance()->fetch_all();
        
        exit;
        $theme = 'luckymall';
        $db = app::get('site')->database();
        $qb = $db->createQueryBuilder();

        kernel::database()->exec('DELETE FROM base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")');

        exit;
        $cache_keys = $db->executeQuery('SELECT `prefix`, `key` FROM base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")')->fetchAll();
        var_dump($cache_keys);
        exit;
        $rows = $qb->select('*')->from('site_themes_tmpl')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        var_dump($rows);
        exit;
        $qb = app::get('site')->database()->createQueryBuilder();
        $theme ='luckymall';
        echo $qb->select('tmpl_path')->from('site_themes_tmpl')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchColumn();
        exit;
                $themeData = $qb->select('*')->from('site_themes')->where('theme='.$qb->createPositionalParameter($entry))->execute()->fetch();
                var_dump($themeData);
                exit;
        $qb = app::get('site')->database()->createQueryBuilder();
        $themeData = app::get('site')->model('themes')->select()->where('theme = ?', $entry)->instance()->fetch_row();
        exit;
        $rows = $qb->select('*')->from('site_themes')->where('theme='.$qb->createPositionalParameter($theme))
           ->execute()->fetch();

        var_dump($rows);
        exit;
        return app::get('site')->model('themes')->select()->where('theme = ?', $theme)->instance()->fetch_row();
        
        
        exit;
        
        $menu = $qb->select('*')->from('site_menus')->where('id='.$qb->createPositionalParameter($id))->execute()->fetch();
        var_dump($menu);
        exit;
        exit;
        $menu = app::get('site')->model('menus')->select()->where('id = ?', $id)->instance()->fetch_row();

        
        $app_id = 'topcx';
        $content_path = 'view';
        $qb = app::get('site')->database()->createQueryBuilder();
        var_dump($qb->select('app_id')->from('base_apps')->where('app_id like "t%"')->execute()->fetchColumn());
        exit;

        
        var_dump($qb->select('id')->from('site_explorers')->where('app='.$qb->createPositionalParameter($app_id))->andWhere('path='.$qb->createPositionalParameter(str_replace('-', '/', $content_path)))->execute()->fetchColumn());
        exit;
        
        
        return app::get('site')->model('explorers')->select()->columns('id')->where('app = ?', $app_id)->where('path = ?', str_replace('-', '/', $content_path))->instance()->fetch_one() ? true : false;

        exit;
        $db = app::get('base')->database();
        $aStatus = $db->executeQuery('show status')->fetchAll();
        var_dump($aStatus);
        exit;
        $rows = app::get('base')->database()->executeQuery('show tables')->fetchAll();
        var_dump($rows);
        exit;

        $qb = app::get('base')->database()->createQueryBuilder();
        $data = $qb->select('cat_id,count(cat_id) as count')->from('sysitem_item')
                   ->where(app::get('sysitem')->model('item')->_filter($filter))
                   ->groupBy('cat_id')
                   ->execute()
                   ->fetchAll();
        var_dump($qb->getSql());exit;
        var_dump($data);
        exit;
        
        /*
        $sfilter = 'select cat_id,count(cat_id) as count from sysitem_item WHERE ';
        $sfilter .= app::get('sysitem')->model('item')->_filter($filter);
        $sfilter .= ' group by cat_id';
        $data = app::get('sysitem')->model('item')->db->select($sfilter);
        */

        exit;

        $qb = app::get('base')->database()->createQueryBuilder();
        $qb->insert('ectools_analysis')
           ->values(array(
               'service'  => $qb->createPositionalParameter('aaa'),
               '`interval`' => $qb->createPositionalParameter('xxddd')
           ))->execute();
        
        $db = app::get('sysitem')->database();
        $params = ['num'=>2, 'item_id'=>'success'];
        var_dump($db->executeUpdate('UPDATE sysitem_item_count SET sold_quantity = sold_quantity + ?? WHERE item_id = ?', [$params['num'], $params['item_id']]));

        


        
        var_dump(app::get('systrade')->database()->createQueryBuilder()
                 ->select('count(1) as saleTimes,sum(payment) as salePrice ,shop_id as shopname')
                 ->from('systrade_trade')
                 ->where($this->_filter($filter))
                 ->execute()->fetch());
        

        exit;

        
        $whereSql = 1;
        $qb = app::get('sysitem')->database()->createQueryBuilder();
        echo $qb->select('count(*) as _count')
                  ->from('sysitem_item', 'I')
                  ->leftJoin('I', 'sysitem_item_status', 'S', 'I.item_id=S.item_id')
                  ->where($whereSql)->execute()->fetchColumn();
        exit;
        $limit = 2;
        if($params['filter']['image_id']=='_ALL_'||$params['filter']['image_id']=='_ALL_')
        {
            unset($params['filter']['image_id']);
        }
        $qb = app::get('image')->database()->createQueryBuilder();
        $rows = $qb->select('image_id')->from('image_image')
                   ->where(app::get('image')->model('image')->_filter($params['filter']))
                   ->andWhere('last_modified<='.$qb->createPositionalParameter($params['queue_time']))
                   ->setMaxResults($limit)
                   ->orderBy('last_modified', 'desc')
                   ->execute()->fetchAll();

        var_dump($qb->getSql());
        
        
        exit;
        var_dump($r = app::get('image')->database()->executeQuery('select count(*) as c from image_image')->fetchColumn());
        exit;
        var_dump(app::get('ectools')->database()->executeQuery('SELECT DISTINCT bank, account FROM ectools_payments where status="succ"')->fetchAll());
        exit;
        
        $params['service'] ='aaa';
        $params['target'] ='kk';
        $params['time_to'] =3333;
        $params['time_from'] =3333;
        //        $params[''] ='aaa';
        $qb = app::get('ectools')->database()->createQueryBuilder();
        if ($analysis_id = $qb->select('id')->from('ectools_analysis')->where('service='.$qb->createPositionalParameter($params['service']))->execute()->fetchColumn())
        {
            $qb = app::get('ectools')->database()->createQueryBuilder();
            $qb->select('target,flag,value,time')
               ->from('ectools_analysis_logs')
               ->where('analysis_id='.$qb->createPositionalParameter($analysis_id))
               ->andWhere('target ='.$qb->createPositionalParameter($params['target']))
               ->andWhere('time>='.$qb->createPositionalParameter(strtotime(sprintf('%s 00:00:00', $params['time_from']))))
               ->andWhere('time<='.$qb->createPositionalParameter(strtotime(sprintf('%s 23:59:59', $params['time_to']))));
            if(isset($this->_params['type'])) $qb->andWhere('type = '.$qb->createPositionalParameter($params['type']));
            $rows = $qb->execute()->fetchAll();
        }
        else
        {
            return array('categories'=>array(), 'data'=>array());
        }
        var_dump($qb->getSql());
        var_dump($rows);
        exit;

        
        /*
        $qb = app::get('base')->database()->createQueryBuilder();
        $qb->insert('ectools_analysis')
           ->values(array(
               'service'  => $qb->createPositionalParameter('aaa'),
               '`interval`' => $qb->createPositionalParameter('xxddd')
           ))->execute();
        ;
        exit;
        */
        //---
        $this->_service = 'aaa';
        $this->_params = ['type'=>'type', 'target'=>'target'];
        
        $qb = app::get('base')->database()->createQueryBuilder();
        
        if ($analysis_id = $qb->select('id')->from('ectools_analysis')->where('service='.$qb->createPositionalParameter($this->_service))->execute()->fetchColumn())
        {
            $qb = app::get('base')->database()->createQueryBuilder();
            $qb->select('target, sum(value)')
               ->from('ectools_analysis_logs')
               ->where('analysis_id = '.$qb->createPositionalParameter($analysis_id))
               ->andWhere('flag = 0')
               ->groupBy('target');
            if(isset($this->_params['type'])) $qb->andWhere('type = '.$qb->createPositionalParameter($this->_params['type']));
            if(isset($this->_params['target'])) $qb->andWhere('target = '.$qb->createPositionalParameter($this->_params['target']));
            if(isset($this->_params['time_from'])) $qb->andWhere('time_from = '. $qb->createPositionalParameter(strtotime(sprintf('%s 00:00:00', $this->_params['time_from']))));
            if(isset($this->_params['time_to'])) $qb->andWhere('time_to = '. $qb->createPositionalParameter(strtotime(sprintf('%s 23:59:59', $this->_params['time_to']))));
            $rows = $qb->execute()->fetchAll();
        }
        var_dump($rows);
        echo $qb->getSql();
        
        exit;
        
        
        
        /*
        $analysis_id = app::get('ectools')->model('analysis')->select()->columns('id')->where('service = ?', $this->_service)->instance()->fetch_one();
        $obj = app::get('ectools')->model('analysis_logs')->select()->columns('target, sum(value) AS value')->where('analysis_id = ?', $analysis_id);
        if(isset($this->_params['type']))   $obj->where('type = ?', $this->_params['type']);
        if(isset($this->_params['target']))   $obj->where('target = ?', $this->_params['target']);
        if(isset($this->_params['time_from']))   $obj->where('time >= ?', strtotime(sprintf('%s 00:00:00', $this->_params['time_from'])));
        if(isset($this->_params['time_to']))   $obj->where('time <= ?', strtotime(sprintf('%s 23:59:59', $this->_params['time_to'])));
        $rows = $obj->where('flag = ?', 0)->group(array('target'))->instance()->fetch_all();
        foreach($rows AS $row){
            $tmp[$row['target']] = $row['value'];
        }
        foreach($this->logs_options AS $target=>$option){
            $detail[$option['name']]['value'] = ($tmp[$target]) ? $tmp[$target] : 0;
            $detail[$option['name']]['memo'] = $this->logs_options[$target]['memo'];
            $detail[$option['name']]['icon'] = $this->logs_options[$target]['icon'];
        }
        */
            
        
        $this->_service = 'aa';
        $qb = app::get('base')->database()->createQueryBuilder();
        if ($analysis_id = $qb->select('id')->from('ectools_analysis')->where($qb->expr()->eq('service', $qb->createPositionalParameter($this->_service)))->execute()->fetchColumn())
        {
            
        }
        var_dump($qb->getSql());
        
        var_dump($analysis_id);exit;
        
        //$analysis_id = app::get('base')->model('apps')->select()->columns(', app_name')->where('app_id = ?', 'base')->instance()->fetch_one();
        $model = app::get('ectools')->model('analysis');
        $model->select('id')->where($model->expr()->eq('service', $model->quote()));
        
        $qb = app::get('ectools')->database();
        $qb->select('id')->from('ectools_analysis')->where($qb->expr);
        
        //        $analysis_id = $model->select('id')->where();
        exit;
        
        /*
        $sTheme = 'luckymall';
        $db = app::get($app)->database();
        $data = $db->executeQuery('select count("widgets_id") as num from site_widgets_instance where core_file like ?', [$sTheme.'%'])->fetchColumn();
        var_dump($data);
        exit;
        */
        $app = 'base';
        $db = app::get($app)->database();
        $sm = $db->getSchemaManager();
        var_dump($sm->listTableNames());
        exit;
        $rows = $db->executeQuery('SHOW TABLE STATUS like '. $db->quote($app.'%'))->fetchAll();
        var_dump($rows);exit;
        $db = kernel::database();
        $rows = $db->select('SHOW TABLE STATUS like "'.$app.'%"');

        
        var_dump(app::get('base')->database()->getDatabasePlatform()->getName());
        exit;
        var_dump(app::get('base')->database()->getDriver()->getName());
        exit;
        var_dump($sm->listTableDetails('user'));
        exit;
        
        var_dump($rows);
        exit;
        
        $rows = kernel::database()->select('select app_id from base_apps where status != "uninstalled"');
        
        $db = app::get('base')->database();
        $rows = app::get('base')->database()->executeQuery('select content_name,content_path from base_app_content where content_type=? and disabled!=?', ['service', 1])->fetchAll();

        var_dump($rows);exit;
        
        
        $sql = 'select content_name,content_path from base_app_content where content_type="service" and disabled!=1';
        if($filter){
            $sql.=' and content_name like '.$db->quote($filter);
        }
        /*
        $db = kernel::database();
        $sql = 'select content_name,content_path from base_app_content where content_type="service" and disabled!=1';
        if($filter){
            $sql.=' and content_name like '.$db->quote($filter);
        }
        */

        if($count){
            if($joinTable && $obj_id){
                if(!$where){
                    $sql = "select t.tag_id,t.tag_name,t.tag_type,count(o.{$obj_id}) as rel_count,$obj_id as ss,t.is_system
                     FROM base_tag t
                     LEFT JOIN base_tag_rel r ON r.tag_id=t.tag_id
                     LEFT JOIN {$joinTable} o ON r.rel_id=o.{$obj_id} and o.disabled!=1
                     where tag_type='$type' group by t.tag_id";
                }else{
                    $sql = "select $obj_id as trel_id
                     FROM base_tag_rel r
                     LEFT JOIN {$joinTable} o ON r.rel_id=o.{$obj_id} and o.disabled!=1
                     where r.tag_id = {$where}";
                }
            }else{
                $sql = "select t.tag_id,t.tag_name,t.tag_type,count(r.rel_id) as rel_count,t.is_system FROM base_tag t LEFT JOIN base_tag_rel r ON r.tag_id=t.tag_id where tag_type='$type' group by t.tag_id";
            }
        }else{
            $sql = "select * FROM base_tag where tag_type='$type'";
        }
 
        /*
        if($count){
            if($joinTable && $obj_id){
                if(!$where){
                    $sql = "select t.tag_id,t.tag_name,t.tag_type,count(o.{$obj_id}) as rel_count,$obj_id as ss,t.is_system
                     FROM base_tag t
                     LEFT JOIN base_tag_rel r ON r.tag_id=t.tag_id
                     LEFT JOIN {$joinTable} o ON r.rel_id=o.{$obj_id} and o.disabled!=1
                     where tag_type='$type' group by t.tag_id";
                }else{
                    $sql = "select $obj_id as trel_id
                     FROM base_tag_rel r
                     LEFT JOIN {$joinTable} o ON r.rel_id=o.{$obj_id} and o.disabled!=1
                     where r.tag_id = {$where}";
                }
            }else{
                $sql = "select t.tag_id,t.tag_name,t.tag_type,count(r.rel_id) as rel_count,t.is_system FROM base_tag t LEFT JOIN base_tag_rel r ON r.tag_id=t.tag_id where tag_type='$type' group by t.tag_id";
            }
        }else{
            $sql = "select * FROM base_tag where tag_type='$type'";
        }
        */

        exit;
        
        $rows = $qb->select('action_id')->from('base_lnk_acts')->where($qb->expr()->in('tag_id', $tag))->execute()->fetchAll();

        
        $rows = $this->db->select('select action_id from base_lnk_acts where role_id in ('.implode(',',$role_id).')');
        
        exit;
        
        $tag = [1000, 2000];
        $qb = app::get('desktop')->database()->createQueryBuilder();
        $rows = $qb->select('rel_id')->from('desktop_tag_rel')->where($qb->expr()->in('tag_id', $tag))->execute()->fetchAll();
        var_dump($rows);exit;

        echo $qb->getSql();
        exit;
        var_dump($rows);
        
        exit;
             
        
        $len = 10;
        $startid = 0;
        $app='base';
        $model = 'kvstore';
        $tname = "{$app}_{$model}";
        
        
        $qb = app::get($app)->database()->createQueryBuilder()->select('*')->from($tname);
        if( strtolower($app)=='base' && strtolower($model)=='kvstore' )
        {
            $qb->where($qb->expr()->notLike('prefix', $qb->getConnection()->quote('cache/%')));
        }
        $qb->setFirstResult($startid)->setMaxResults($len);
        $aData = $qb->execute()->fetchAll();

        var_dump($aData);
        exit;
        
        /*
        $limit = sprintf( 'LIMIT %s,%s', $startid, $len );
        if( strtolower($app)=='base' && strtolower($model)=='kvstore' ) 
            $where = ' WHERE prefix NOT LIKE "cache/%"';
            
        $sql = "SELECT * FROM $tname $where $limit";
        $aData = $this->_db->select( $sql );
        */
        

            
        
        $tables = app::get('base')->database()->executeQuery('SELECT app_id FROM base_apps WHERE status=?', ['active'])->fetchAll();
        var_dump($tables);
        
        exit;
        $pri_settings = app::get('base')->database()->executeQuery('select app, `key`, value from base_setting')->fetchAll();
        var_dump($pri_settings);
        exit;
        /*
        $db->select($s='select image_id,url,s_url,m_url,l_url,last_modified,width,height from image_image where image_id in(\''.
                    implode("','",array_keys($img)).'\')'); 

        exit;
        */
        $img = array('ff6485392d25f0499eb08941d22ceeab'=>2, 'asdfsadf'=>4);
        $qb = app::get('image')->database()->createQueryBuilder();
        $rows = $qb->select('image_id,url,s_url,m_url,l_url,last_modified,width,height')->from('image_image')
           ->where($qb->expr()->in('image_id', array_map(function($image_id) use ($qb){
               return $qb->getConnection()->quote($image_id);
           }, array_keys($img))))->execute()->fetchAll();
        var_dump($rows);

        var_dump($qb->getSql());
        exit;
        
        
        /*
        foreach($db->select($s='select image_id,url,s_url,m_url,l_url,last_modified,width,height from image_image where image_id in(\''.
                    implode("','",array_keys($img)).'\')') as $r){
                $imglib[$r['image_id']] = $r;
        }
        */
        exit;

        
        $keywords = array('base', 'ectools');
        $qb = app::get('base')->database()->createQueryBuilder()
                              ->select('app_id,app_name,description,local_ver,remote_ver')->from('base_apps');
        foreach($keywords as $word)
        {
            $where[] = "app_id like '%{$word}%' or app_name like '%{$word}%' or `description` like '%{$word}%'";
        }
        $rows = $qb->where(call_user_func_array(array($qb->expr(), 'orX'), $where))->execute()->fetchAll();
        var_dump($rows);exit;
        /*
        foreach($keywords as $word){
            $where[] = "app_id like '%{$word}%' or app_name like '%{$word}%' or `description` like '%{$word}%'";
        }
        $sql = 'select app_id,app_name,description,local_ver,remote_ver from base_apps where 1 and '.implode(' and ',$where);
        $rows = kernel::database()->select($sql);
        var_dump($rows);exit;
        */
        exit;
        
        
        $prefix = 'tbdefine';
        $key = 'basesyscache_resources';
        $rows = app::get('base')->database()->executeQuery('SELECT * FROM `base_kvstore` WHERE `prefix` = ? AND `key` = ?', [$prefix, $key])->fetchAll();
        var_dump($rows);
        exit;
        $rows = kernel::database()->select(
            sprintf("SELECT * FROM `base_kvstore` WHERE `prefix` = %s AND `key` = %s", kernel::database()->quote($this->prefix), kernel::database()->quote($key)),
            true
        );        
        
        $db = app::get('base')->database();
        //$rows = $db->executeQuery();
        $app_id ='\'\';exit;;;;a/dsajfk""';
        $count = app::get('base')->database()->executeQuery('select count(*) from base_apps where app_id = ? AND status = "active"', [$app_id])->fetchColumn();
        echo $count;exit;
        $rows = app::get('base')->database()->executeQuery('select app_id,app_name from base_apps where status <> "uninstalled"')->fetchAll;

        var_dump($rows);exit;
        

        
        //$count = $db->count('SELECT count(*) AS count FROM base_kvstore', true);
        $count = $db->executeQuery('SELECT count(*) AS count FROM base_kvstore')->fetchColumn();
        echo  $count;
        exit;
        $data = array (
            'fullminus_id' => '16',
            'fullminus_name' => '99-9',
            'canjoin_repeat' => 0,
            'join_limit' => 3,
            'used_platform' => '0',
            'free_postage' => 1,
            'condition_value' => '99|9',
            'shop_id' => 2,
            'start_time' => 1429632000,
            'end_time' => 1430150400,
            'valid_grade' => '1,2,3',
            'fullminus_rel_itemids' => '44,46,123',
            'fullminus_desc' => '',
            'test1' => 0,
            'promotion_tag' => '满减',
        );

        
        $model = app::get('syspromotion')->model('fullminus');
        $model->save($data);

        exit;

        
        $data = $db->executeQuery('select count("widgets_id") as num from site_widgets_instance where core_file like ?', [$sTheme.'%'])->fetchColumn();

        $db = app::get('sysitem')->database();
        var_dump($db->quote(3333, \PDO::PARAM_BOOL));
                 exit;
        $qb = $db->createQueryBuilder();
        $qb->where($qb->expr()->andX($qb->expr()->eq('a', 'fff'), 'a like '.$db->quote('kkk%'), ''));
        echo $qb->getSql();
        exit;
        $params = ['num'=>2, 'item_id'=>'success'];
        var_dump($db->executeUpdate('UPDATE sysitem_item_count SET sold_quantity = sold_quantity + ? WHERE item_id = ?', [$params['num'], $params['item_id']]));
        exit;
        
        

        
        $sql = "UPDATE sysitem_item_count SET sold_quantity = sold_quantity + ".intval($params['num'])." WHERE item_id = ".intval($params['item_id']);
        

        
        $db = app::get('systrade')->database();
        $qb = $db->createQueryBuilder();
        $subQb = $db->createQueryBuilder();

        $subQb->select('I.item_id')
              ->from('systrade_order', 'O')
              ->leftJoin('O', ' sysitem_item', 'I', 'O.item_id=I.item_id')
              ->where($qb->expr()->andX(
                  $qb->expr()->neq('O.status', $db->quote('WAIT_BUYER_PAY'))
              ))
              ->groupBy('I.item_id');
        $qb->select('count(*) as _count')
           ->from('('.$subQb->getSql().')', 'dd');

        
        echo $qb->getSql();exit;
        
        echo $subQb->getSql();exit;
        //$stmt = $subQb->execute();
        
        //        var_dump($subQb->)
        

        
        $conn = db::connection();
        $qb = $conn->createQueryBuilder()
              ->select('u.id')
              ->addSelect('p.id')
              ->from('users', 'u')
              ->leftJoin('u', 'phonenumbers', 'u.id = p.user_id');
        var_dump($qb->getSql());
        
        /*
        $sql = 'SELECT count(*) as _count FROM (SELECT login_account FROM pam_user as M 
		where '.$this->_filter($filter).' Group By M.user_id) as tb';
        */

        $sql = 'SELECT count(*) as _count FROM pam_user 	where '.$this->_filter($filter).' Group By M.user_id) as tb';

        
        
        
        $db = app::get('site')->database();
        //        var_dump($db->quote('luckymall%'));exit;
        $sTheme = 'luckymall';
        
        $data = $db->executeQuery('select count("widgets_id") as num from site_widgets_instance where core_file like ?', [$sTheme.'%'])->fetchColumn();
        var_dump($data);
        exit;

        

        
        //        $rows = kernel::database()->selectlimit('SELECT `prefix`, `key` FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<'.$time, $pagesize, $i*$pagesize);

        
        //        $rows = kernel::database()->selectlimit('SELECT `prefix`, `key` FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<'.$time, $pagesize, $i*$pagesize);

        
        $i = 0;
        $pagesize = 2;
        $rows = app::get('base')->database()->executeQuery('SELECT `prefix`, `key` FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<? limit ? offset ?', [time(), $pagesize, $pagesize*$i])->fetchAll();

        var_dump($rows);

        
        exit;
        exit;
        $conn = db::connection();

        
        $qb = $conn->createQueryBuilder();
        $qb->insert('desktop_menus');
        $qb->setValue('display', 'true');
        $qb->execute();exit;
        

        
        $theme = app::get('site')->model('themes');
        $data = [
            'theme' => 'test',
            'config' => array(1,2,3,4,5,6,7),
        ];
        var_dump($theme->insert($data));
        exit;


        
        app::get('base')->database()->rollback();
        exit;
        
        $time = time();
        $count = kernel::database()->count('SELECT count(*) FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<'.$time);
        $pagesize = 100;
        $page = ceil($count / 100);
        $rows = kernel::database()->selectlimit('SELECT `prefix`, `key` FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<'.$time, $pagesize, $i*$pagesize);


        var_dump(app::get('base')->database()->executeQuery('SELECT count(*) FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<?', [222])->fetchColumn());
        exit;
        
        exit;
        var_dump($rows);
        $app_id = 'topc';
        //        $row = kernel::database()->selectrow('select status from base_apps where app_id = "'.$app_id.'" AND status IN ("uninstalled", "paused") ');
        
        /*
        $conn = db::connection();
        $qb = $conn->createQueryBuilder();
        $row = $qb->select('status')
                  ->from('base_apps')
                  ->where($qb->expr()->andX(
                      $qb->expr()->eq('app_id', $qb->createPositionalParameter($app_id)),
                      $qb->expr()->in('status', array($conn->quote('uninstalled'), $conn->quote('paused')))
                  ))->execute()->fetch();
        

        //$sql = 'select status from base_apps where app_id = ? AND status IN ("uninstalled", "paused")';
        //        $sql = sprintf('select status from base_apps where app_id = ? AND status IN ("uninstalled", "paused")');

        $row = app::get('base')->model('apps')->getRow('status',
                                                       ['app_id' => $app_id, array('uninstalled', 'paused')]);
        //        var_dump($row);exit;

        echo '---'.PHP_EOL;
        $stmt = $conn->prepare('select status from base_apps where app_id = ? AND status IN ("uninstalled", "paused")');
        $stmt->bindValue(1, 'dev');
        $stmt->execute();
        
        var_dump($stmt->fetchColumn());
        */
        
        $app_id = 'topc';

        $data = ['queue_name' => 'asasss',
                 'worker' => 'xxxx',
                 'params' => 'paaa',
                 'create_time' => time()];
        
        app::get('system')->model('queue_mysql')->insert($data);

        $a = app::get('system')->database()->executeUpdate('UPDATE system_queue_mysql force index(PRIMARY) SET owner_thread_id=GREATEST(CONNECTION_ID() ,(@msgID:=id)*0),last_cosume_time=? WHERE queue_name=? and owner_thread_id=-1 order by id LIMIT 1;', [time(), 'asasss']);

        var_dump($a);
        if ($a) {
            var_dump(app::get('system')->database()->executeQuery('select id, worker, params from system_queue_mysql where id=@msgID')->fetch());
            
        }
        

        exit;
        echo '==';
        //        $row = app::get('image')->database()->executeQuery('select count(*) as c from image_image where storage="filesystem" ')->fetch();
        $row = app::get('system')->database()->executeQuery('select CONNECTION_ID()')->fetch();
        var_dump($row);exit;
        //        var_dump($row);exit;


        var_dump($row);
        
        
        var_dump(app::get('topc')->status());exit;
        var_dump($row);
        /*
        var_dump($conn->executeQuery('select * from base_apps where app_id = ? AND status IN ("uninstalled", "paused")',
                                     ['dev'])->fetch());
        */
        exit;
        
        
        /*
        $sql = 'select status from base_apps where app_id = ? AND status IN ("uninstalled", "paused")';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, 'dev');
        $stmt->execute();
        */
        echo 333;
        var_dump($stmt->fetchColumn());exit;
        
        
                                         
        var_dump($row);exit;
        
        $conn = db::connection();
        var_dump($conn->executeUpdate('set @msgId=-1'));
        $stmt = $conn->executeQuery('select @msgID');
        var_dump($stmt->fetchAll());
        exit;

        $quoted = $conn->quoteIdentifier('id');
        var_dump($quoted);
        $qb = $conn->createQueryBuilder();
        $qb->select('show tables like \'%a%\'');
        $stmt = $qb->execute();
        var_dump($stmt->fetchAll());
        exit;
        
        $theme = app::get('site')->model('themes');
        $data = [
            'theme' => 'test',
            'config' => array(1,2,3,4,5,6,7),
        ];
        //$theme->database()->beginTransaction();
        db::connection()->beginTransaction();
        var_dump($theme->insert($data));
        
               $theme->database()->commit();
        echo '---'.PHP_EOL;
        //        $theme->database()->rollback();

        echo 10;

        exit;

        


        $data = [
            'theme' => 'test',
            'config' => array(1,2,3)
        ];
        var_dump($theme->replace($data, ['theme' => 'test']));
        
    }
    
    public function testRealQueryBuilder()
    {
        /*
        $conn = db::connection();
        $qb = $conn->createQueryBuilder();

        try {
            $qb->update('base_network')
               ->set('`node_url`', $qb->createPositionalParameter('dddd'))
               ->where('node_id=78 or node_id=77');
            //            var_dump($qb->getSql());exit;
            var_dump($qb->execute());
            exit;
            
            $qb->insert('base_network')
               ->values(
                   array(
                       '`node_name`' => $qb->createPositionalParameter($conn->quote('dd'), \PDO::PARAM_STR),
                   )
               );
            
            var_dump($qb->getSql());exit;
            $stmt = $qb->execute();
            var_dump($conn->lastInsertId());
            exit;
            $query = $conn->executeUpdate('insert into base_network (node_name, node_url) values (?, ?)', ['adsfdsaf', 'xxx']);
            var_dump($conn->lastInsertId());
            exit;
                                            
            //            var_dump($stmt->fetch());exit;
            var_dump($stmt);exit;
        }
        catch (NotNullConstraintViolationException $e) 
        {
            echo 33;exit;
            echo get_class($e);
        }
        exit;
        
        
        
        
        $qb->select('count(*) as count')
           ->from('sysitem_item');
        $stmt = $qb->execute();
        $a = $stmt->fetch();
        var_dump($a);exit;
        
        $qb->select('*')
           ->from('sysitem_item')
           ->setFirstResult(0)
           ->setMaxResults(1)
           ->where($qb->expr()->andX(
               $qb->expr()->eq('item_id', $qb->createPositionalParameter(12))
           ));
        

        var_dump($qb->getSql());exit;
        //        exit;
        $stmt = $qb->execute();
        var_dump($stmt->fetchAll());
        
        */
                     
    }
    
    
}
