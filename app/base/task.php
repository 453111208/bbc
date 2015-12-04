<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

class base_task{

    function install_options(){
        return array(
            'database.host'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>app::get('base')->_('数据库主机'),'default'=>'127.0.0.1'),
            'database.username'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>app::get('base')->_('数据库用户名'),'default'=>'root'),
            'database.password'=>array('type'=>'password','title'=>app::get('base')->_('数据库密码'),'default'=>''),
            'database.database'=>array('type'=>'select','vtype'=>'required','required'=>true,'title'=>app::get('base')->_('数据库名'),'options_callback'=>array('app'=>'base', 'method'=>'dbnames'),'onfocus'=>'setuptools.getdata(\'base\', \'dbnames\', this);'),
            'app.timezone'=>array('type'=>'select','options'=>base_location::timezone_list()
                                      ,'title'=>app::get('base')->_('默认时区'),'default'=>'8','vtype'=>'required','required'=>true),
            'app.url'=>array('type'=>'text', 'title'=>app::get('base')->_('系统URL地址'),'default'=>url::to('/'),'vtype'=>'required','required'=>true),
            //'ceti_identifier'=>array('type'=>'text','vtype'=>'required','required'=>true,'title'=>app::get('base')->_('电子邮箱或企业帐号'),'default'=>''),
            //'ceti_password'=>array('type'=>'password','vtype'=>'required','required'=>true,'title'=>app::get('base')->_('密码'),'default'=>''),
        );
    }

    function dbnames($options)
    {
        $options = $options['base'];
        $link = @mysql_connect($options['database.host'],$options['database.username'],$options['database.password']);
        if(!$link){
            return array();
        }else{
            if(function_exists('mysql_list_dbs')){
                $db_list = mysql_list_dbs($link);
            }else{
                $db_list = mysql_query('SHOW databases');
            }//todo: 加强兼容性
            $i = 0;
            $cnt = mysql_num_rows($db_list);
            $rows = array();
            while($i < $cnt) {
                $dbname = trim(mysql_db_name($db_list, $i++));
                $rows[$dbname] = $dbname;
            }
            return $rows;
        }
    }//End Function

    function checkenv($options){
        if(!$options['database.host']){
            echo app::get('base')->_("Error: 需要填写数据库主机")."\n";
            return false;
        }
        if(!$options['database.username']){
            echo app::get('base')->_("Error: 需要填写数据库用户名")."\n";
            return false;
        }
        if(!$options['database.database']){
            echo app::get('base')->_("Error: 请选择数据库")."\n";
            return false;
        }

        $link = @mysql_connect($options['database.host'],$options['database.username'],$options['database.password']);
        if(!$link){
            echo app::get('base')->_("Error: 数据库连接错误")."\n";
            return false;
        }

        $mysql_ver = mysql_get_server_info($link);
        if(!version_compare($mysql_ver,'4.1','>=')){
            echo app::get('base')->_("Error: 数据库需高于4.1的版本")."\n";
            return false;
        }

        if(!mysql_select_db($options['database.database'], $link)){
            echo app::get('base')->_("Error: 数据库")."\"" . $options['database.database'] . "\"".app::get('base')->_("不存在")."\n";
            return false;
        }

        if(!kernel::single('base_setup_config')->write($options)){
            echo app::get('base')->_("Error: Config文件写入错误")."\n";
            return false;
        }

        $timezone = config::get('app.timezone', 8);
        date_default_timezone_set('Etc/GMT'.($timezone>=0?($timezone*-1):'+'.($timezone*-1)));

        return true;
    }

    function pre_install($options){
        kernel::set_online(false);
        if(!kernel::single('base_setup_config')->write($options)){
            echo app::get('base')->_("Error: Config文件写入错误")."\n";
            return false;
        }

       // base_certificate::active();
    }

    function post_install(){
        kernel::single('base_application_manage')->sync();
        kernel::set_online(true);
        $rpc_global_server = array(
            'node_id'=> base_mdl_network::MATRIX_ASYNC,
            'node_url'=>config::get('link.matrix_async_url'),
            'node_name'=>'Global Matrix',
            'node_api'=>'',
            'link_status'=>'active',
            );
        app::get('base')->model('network')->replace($rpc_global_server,array('node_id'=> base_mdl_network::MATRIX_ASYNC), true);

		$rpc_realtime_server = array(
                'node_id'=>base_mdl_network::MATRIX_REALTIME,
                'node_url'=>config::get('link.matrix_realtime_url'),
                'node_name'=>'Realtime Matrix',
                'node_api'=>'',
                'link_status'=>'active',
            );

		app::get('base')->model('network')->replace($rpc_realtime_server,array('node_id'=>base_mdl_network::MATRIX_REALTIME), true);

		$rpc_service_server = array(
                'node_id'=>base_mdl_network::MATRIX_SERVICE,
                'node_url'=>config::get('link.matrix_service_url'),
                'node_name'=>'Service Matrix',
                'node_api'=>'',
                'link_status'=>'active',
            );

		app::get('base')->model('network')->replace($rpc_service_server,array('node_id'=>base_mdl_network::MATRIX_SERVICE), true);
    }

    function post_update($dbinfo)
    {
        $dbver = $dbinfo['dbver'];
        if(empty($dbver) || $dbver < 0.31)
        {
            $configWrite = new base_setup_config();
            $configWrite->overwrite = true;

            $configs = [
                'database.host' => config::get('database.host'),
                'database.database' => config::get('database.database'),
                'database.username' => config::get('database.username'),
                'database.password' => config::get('database.password'),
            ];

            $configWrite->write($configs);
        }

        $rpc_global_server = array(
                'node_id'=> base_mdl_network::MATRIX_ASYNC,
                'node_url'=>config::get('link.matrix_async_url'),
                'node_name'=>'Global Matrix',
                'node_api'=>'',
                'link_status'=>'active',
            );
        app::get('base')->model('network')->replace($rpc_global_server,array('node_id'=> base_mdl_network::MATRIX_ASYNC), true);

		$rpc_realtime_server = array(
                'node_id'=>base_mdl_network::MATRIX_REALTIME,
                'node_url'=>config::get('link.matrix_realtime_url'),
                'node_name'=>'Realtime Matrixi',
                'node_api'=>'',
                'link_status'=>'active',
            );

		app::get('base')->model('network')->replace($rpc_realtime_server,array('node_id'=>base_mdl_network::MATRIX_REALTIME), true);

		$rpc_service_server = array(
                'node_id'=>base_mdl_network::MATRIX_SERVICE,
                'node_url'=>config::get('link.matrix_service_url'),
                'node_name'=>'Service Matrix',
                'node_api'=>'',
                'link_status'=>'active',
            );

		app::get('base')->model('network')->replace($rpc_service_server,array('node_id'=>base_mdl_network::MATRIX_SERVICE), true);

    }//End Function


}
