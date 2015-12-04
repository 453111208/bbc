<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_shell_buildin extends base_shell_prototype{

    var $vars;

    var $command_alias = array(
            'ls'=>'list',
            'q'=>'exit',
            'quit'=>'exit',
        );

    function php_call(){
        if($this->vars) extract($this->vars);
        var_dump(func_get_arg(0));
        $this->output(eval(func_get_arg(0)));
        $this->vars = get_defined_vars();
    }

    function command_reset(){
        $this->cmdlibs = null;
        $this->vars = null;
    }

    var $command_help_options = array(
            'verbose'=>array('title'=>'显示详细信息','short'=>'v'),
        );

    function command_help($help_item=null,$shell_command=null){
        if($help_item){
            list($app_id,$package) = explode(':',$help_item);
            $this->app_help($app_id , $package ,$shell_command);
        }else{
            $this->help();
            $this->output_line(app::get('base')->_('应用提供的命令：'));
            if ($handle = opendir(APP_DIR)) {
                while (false !== ($app_id = readdir($handle))) {
                    if($app_id{0}!='.' && is_dir(APP_DIR.'/'.$app_id) && is_dir(APP_DIR.'/'.$app_id.'/lib/command')){
                        $this->app_help($app_id);
                    }
                }
                closedir($handle);
            }
            $this->output_line(app::get('base')->_('原生php命令'));
            $____params=app::get('base')->_('输入命令如果以分号[;]结尾，则被认为是一条php语句.  例如:');
            echo <<<EOF
$____params
  1> \$a = 2;
     int(2)
  2> pow(\$a,8);
     int(256)

EOF;
        }
    }

    function app_help($app_id,$package=null,$command=false){
        if($package){
            $commander = $this->shell->get_commander($app_id,$package);
            $commander->help($command);
        }else{
            if ($handle = opendir(APP_DIR.'/'.$app_id.'/lib/command')) {
                while (false !== ($file = readdir($handle))) {
                    if (substr($file,-4,4)=='.php' && is_file(APP_DIR.'/'.$app_id.'/lib/command/'.$file)) {
                        $commander = $this->shell->get_commander($app_id,substr($file,0,-4));
                        if($commander){
                            $commander->help();
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    function name_prefix(){
        return '';
    }

    var $command_exit = '退出';
    function command_exit(){
        echo 'exit';
        exit;
    }

    var $command_man = '显示帮助';
    function command_man(){
        $args = func_get_args();
        foreach($args as $arg){
            kernel::single('base_misc_man')->show($arg);
        }
    }

    var $command_sh = '执行操作系统命令';
    function command_sh($args){
        eval('system("'.str_replace('"','\\"',implode(' ',$args)).'");');
    }

    var $command_mkconfig = '创建config文件';
    var $command_mkconfig_options = array(
            'dbhost'=>array('title'=>'数据库服务器，默认:localhost','short'=>'h','need_value'=>1),
            'dbpassword'=>array('title'=>'数据库密码','short'=>'p','need_value'=>1),
            'dbuser'=>array('title'=>'数据库用户名','short'=>'u','need_value'=>1),
            'dbname'=>array('title'=>'数据库名','short'=>'n','need_value'=>1),
            'timezone'=>array('title'=>'时区','short'=>'t','need_value'=>1),
        );
    function command_mkconfig(){
            $options = $this->get_options();
            $options = array(
                'db_host'=>$options['dbhost']?$options['dbhost']:'localhost',
                'db_user'=>$options['dbuser']?$options['dbuser']:'root',
                'db_password'=>$options['dbpassword'],
                'db_name'=>$options['dbname'],
                'default_timezone'=>$options['timezone'],
                );
            kernel::single('base_setup_config')->write($options);
    }

    var $command_ls = '列出所有应用';
    function command_ls(){
        $rows = app::get('base')->model('apps')->getlist('*',array('installed'=>true));
        // var_dump($rows);
        foreach($rows as $k=>$v){
            $rows[$k] = array(
                    'app_id'=>$v['app_id'],
                    'name'=>$v['name'],
                    'version'=>$v['version'],
                    'status'=>$v['status']?$v['status']:'uninstalled',
                );
        }
        $this->output_table( $rows );
    }

    var $command_cd = '切换当前应用';
    function command_cd($app=''){

        if($app=='..'){
            $app = '';
        }elseif($app=='-'){
            $app = $this->last_app_id;
        }

        if($app{0}!='.' && is_dir(APP_DIR.'/'.$app)){
            $this->last_app_id = $this->shell->app_id;
            $this->shell->app_id = $app;
        }else{
            throw new \RuntimeException($app.": No such application.\n");
        }
    }

    var $command_install = '安装应用';
    var $command_install_options = array(
               'reset'=>array('title'=>'重新安装','short'=>'r'),
                'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
            );
    function command_install(){
        $args = func_get_args();
        $options = $this->get_options();
        $install_queue = kernel::single('base_application_manage')->install_queue($args,$options['reset']);
        $this->install_app_by_install_queue($install_queue, $options);
    }

	/**
	 * 安装应用过程
	 * @param array intall queue
	 * @param array setup options
	 * @return null
	 */
	private function install_app_by_install_queue($install_queue, $options=array())
	{
		if(!$install_queue){
            return ;
        }

        if(kernel::single('base_application_manage')->has_conflict_apps(array_keys($install_queue), $conflict_info)){
            foreach($conflict_info AS $conflict_app_id=>$conflict_detail){
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= $conflict_app_info['name'] . ' ' . app::get('base')->_('与') . ' ' . $conflict_detail['name'] . ' ' . app::get('base')->_('存在冲突') . "\n";
            }
            logger::info($conflict_message . app::get('base')->_('请手工卸载冲突应用')."\n");
            return false;
        }//todo：安装时判断app冲突，检测包括所有依赖的app和现有安装app之间的冲突

        if($options['options']){
            parse_str($options['options'],$this->shell->input);
        }
        //---start---增加如果是通过web访问，则进行相关处理-------@lujunyi------
        if($_SERVER['HTTP_USER_AGENT'] && !$this->shell->input){
            $a = 'install ' . $args[0] . ' -o "';
            foreach((array)$install_queue as $app_id_=>$app_info_){
                if(!$app_info_){
                    logger::info(app::get('base')->_('无法找到应用').$app_id_."\n");
                    return false;
                }
                $install_options_ = app::get($app_id_)->runtask('install_options');
                if($install_options_){
                    $c = '';
                    foreach($install_options_ as $key=>$item){
                        $b .= $app_id_ . '[' . $key . ']=' . $item['default'] . '&';
                    }
                }
                $c .= $b;
            }
            $d = rtrim($c,'&');
            if(!empty($d)){
                echo "请按以下格式输入数据：\n";
                $e = $a . $d . '"';
                echo $e;
                exit;
            };
        }
        //---end---
        foreach((array)$install_queue as $app_id=>$app_info){
            if(!$app_info){
                logger::info(app::get('base')->_('无法找到应用').$app_id."\n");
                return false;
            }
            if(!kernel::single('base_setup_lock')->lockfile_exists()){
                kernel::single('base_setup_lock')->write_lock_file(false);
            }//todo: 生成setup lock文件
            $install_options = app::get($app_id)->runtask('install_options');

            if(is_array($install_options) && count($install_options)>0 && !$this->shell->input[$app_id]){
                do{
                    $this->shell->input_option($install_options,$app_id);
                }while(app::get($app_id)->runtask('checkenv',$this->shell->input[$app_id])===false);
            }
            kernel::single('base_application_manage')->install($app_id,$this->shell->input[$app_id]);
        }
	}

	/**
	 * 安装整个产品的方法
	 * @param string 产品名称
	 * @return null
	 */
	var $command_install_product = "安装产品";
	var $command_install_product_options = array(
	   'reset'=>array('title'=>'重新安装','short'=>'r'),
		'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
	);
	function command_install_product(){
		$args = func_get_args();
		$options = $this->get_options();
		$config = base_setup_config::deploy_info();
		//if (!$args[0] || $args[0] != $config['product_name']) echo app::get('base')->_('产品名称无法找到！');

		foreach($config['package']['app'] as $k=>$app){
            if ($app['default'] === 'true') {
                $applist[] = $app['id'];
            }
        }

        $applist = kernel::single('base_application_manage')->install_queue($applist);
		$this->install_app_by_install_queue($applist, $options);

		$this->command_install_demodata('demodata');
	}

	var $command_install_demodata = "安装初始化数据";
	var $command_install_demodata_options = array(
	   'reset'=>array('title'=>'重新安装','short'=>'r'),
		'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
	);
	function command_install_demodata($app_id='demodata'){
		/** 所有app安装完成后执行安装demo数据 **/
		$args = func_get_args();
		$options = $this->get_options();
		$config = base_setup_config::deploy_info();

		if($options['options']){
            parse_str($options['options'],$this->shell->input);
        }

		$install_options = array();
		$tmp_arr_options = array();
		foreach ((array)$config['demodatas'] as $key=>$demo_data){
			foreach ((array)$demo_data['options'] as $arr_options){
				$tmp_arr_options[$arr_options['key']] = $arr_options['value'];
			}
			unset($demo_data['options']);
			$demo_data['options'] = $tmp_arr_options;
			$install_options[$key] = $demo_data;
		}

		if(is_array($install_options) && count($install_options)>0 && !$this->shell->input[$app_id]){
			$this->shell->input_option($install_options,$app_id);
		}

		if ($this->shell->input[$app_id][$app_id] == 2){
			logger::info('Import demo data');
			kernel::single('base_demo')->init();
		}
		logger::info('Application demodata installed... ok.');
	}

	var $command_active_cetificate = "激活证书";
	var $command_active_cetificate_options = array(
	   'reset'=>array('title'=>'重新安装','short'=>'r'),
		'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
	);
	function command_active_cetificate($app_id='activeceti'){
		/** 安装完成后获取证书 **/
		$args = func_get_args();
		$options = $this->get_options();
		$config = base_setup_config::deploy_info();

		if($options['options']){
            parse_str($options['options'],$this->shell->input);
        }

		$install_options = array();
		$tmp_arr_options = array();
		foreach ((array)$config['active_ceti']['active_ceti_info'] as $key=>$active_data){
			$install_options[$key] = $active_data;
		}

		if(is_array($install_options) && count($install_options)>0 && !$this->shell->input[$app_id]){
			$this->shell->input_option($install_options,$app_id);
		}

		if ($this->shell->input[$app_id]){
			logger::info('Active cetificate...');
			$api_data = array(
				'certi_app'=>'ent.reg',
				'email'=>$this->shell->input[$app_id]['email'],
				'password'=>$this->shell->input[$app_id]['password'],
				'tel'=>$this->shell->input[$app_id]['tel'],
				'province'=>$this->shell->input[$app_id]['province'],
				'version'=>'1.0',
				'format'=>'json',
			);
			ksort($api_data);
			foreach($api_data as $key => $value){
				$str.=$value;
			}
			base_enterprise::set_token();
			$api_data['certi_ac'] = md5($str.base_enterprise::$token);
			$http = kernel::single('base_httpclient');
			$http->set_timeout(6);
			$result = $http->post(
				config::get('link.shop_user_enterprise_api'),
				$api_data);
			$tmp_res = json_decode($result, 1);
			if ($tmp_res['res'] == 'succ'){
				$arr_enterprise = array(
					'ent_id'=>$tmp_res['msg']['entid'],
					'ent_ac'=>$tmp_res['msg']['password'],
					'ent_email'=>$tmp_res['msg']['email'],
				);
				base_enterprise::set_enterprise_info($arr_enterprise);
				// 申请证书
				if (base_enterprise::ent_id()&&base_enterprise::ent_ac()&&base_enterprise::ent_email()){
					base_certificate::register();
					// 申请应用的节点
					$this->command_active_node_id('ceti_node_id');
				}
				logger::info('Application active cetificate... ok.');
			}else{
				logger::info('Application active cetificate... failed.');
			}
		}
	}

	var $command_active_node_id = "激活node_id";
	var $command_active_node_id_options = array(
	   'reset'=>array('title'=>'重新安装','short'=>'r'),
		'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
	);
	function command_active_node_id($app_id='ceti_node_id'){
		/** 证书获取后激活应用节点 **/
		$config = base_setup_config::deploy_info();
		foreach($config['package']['app'] as $k=>$app){
            $applist[] = $app['id'];
        }

		foreach ($applist as $str_app_id){
			$app_xml = kernel::single('base_xml')->xml2array(file_get_contents(app::get($str_app_id)->app_dir.'/app.xml'),'base_app');
			if (isset($app_xml['node_id'])&&$app_xml['node_id']=="true"&&!base_shopnode::node_id($str_app_id)){
				// 获取节点.
				if(base_shopnode::register($str_app_id)){
					logger::info('Applications info send to center, ok.');
				}
			}
		}
	}

	var $command_inactive_node_id = "取消激活node_id";
	var $command_inactive_node_id_options = array(
		'reset'=>array('title'=>'重新安装','short'=>'r'),
		'options'=>array('title'=>'参数','short'=>'o','need_value'=>1),
	);
	function command_inactive_node_id($app_id='ceti_node_id'){
		/** 证书获取后激活应用节点 **/
		$config = base_setup_config::deploy_info();
		foreach($config['package']['app'] as $k=>$app){
            $applist[] = $app['id'];
        }

		foreach ($applist as $str_app_id){
			$app_xml = kernel::single('base_xml')->xml2array(file_get_contents(app::get($str_app_id)->app_dir.'/app.xml'),'base_app');
			if (isset($app_xml['node_id'])&&$app_xml['node_id']=="true"&&base_shopnode::node_id($str_app_id)){
				// 获取节点.
				base_shopnode::delete_node_id($str_app_id);
			}
		}
	}


    var $command_cleanunicom = '清除与shopex用户中心的关联关系';
    function command_cleanunicom(){
        kernel::single('base_cleandata')->clean();
        echo "Clear shopex_id, certi_id, node_id SUCCESS......";
    }


    var $command_uninstall = '卸载应用';
    var $command_uninstall_options = array(
            'recursive'=>array('title'=>'递归删除依赖之app','short'=>'r'),
        );
    function command_uninstall(){
        $args = func_get_args();
        $uninstall_queue = kernel::single('base_application_manage')->uninstall_queue($args);
        $options = $this->get_options();

        if(!$options['recursive']){
            foreach($uninstall_queue as $app_id=>$type){
                $to_delete[$type[1]][] = $app_id;
            }
            if($to_delete[1]){
                echo 'error in remove app '.implode(' ',$args)."\n";
                echo app::get('base')->_("以下应用依赖欲删除的app: ").implode(' ',$to_delete[1])."\n";
                echo app::get('base')->_("使用 -r 参数按依赖关系全部删除");
                return true;
            }
        }
        foreach($uninstall_queue as $app_id=>$type){
            kernel::single('base_application_manage')->uninstall($app_id);
        }
    }

    var $command_pause = '暂停应用';
    var $command_pause_options = array(
            'recursive'=>array('title'=>'递归删除依赖之app','short'=>'r'),
        );
    function command_pause()
    {
        $args = func_get_args();
        $pause_queue = kernel::single('base_application_manage')->pause_queue($args);
        $options = $this->get_options();

        if(!$options['recursive']){
            foreach($pause_queue as $app_id=>$type){
                $to_pause[$type[1]][] = $app_id;
            }
            if($to_pause[1]){
                echo 'error in pause app '.implode(' ',$args)."\n";
                echo app::get('base')->_("以下应用依赖欲暂停的app: ").implode(' ',$to_pause[1])."\n";
                echo app::get('base')->_("使用 -r 参数按依赖关系全部暂停");
                return true;
            }
        }
        foreach($pause_queue as $app_id=>$type){
            kernel::single('base_application_manage')->pause($app_id);
        }
    }//End Function

    var $command_active = '开启应用';
    var $command_active_options = array(
            'recursive'=>array('title'=>'递归删除依赖之app','short'=>'r'),
        );
    function command_active()
    {
        $args = func_get_args();
        $active_queue = kernel::single('base_application_manage')->active_queue($args);
        $options = $this->get_options();
        if(!$active_queue){
            return ;
        }

        if(kernel::single('base_application_manage')->has_conflict_apps(array_keys($active_queue), $conflict_info)){
            foreach($conflict_info AS $conflict_app_id=>$conflict_detail){
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= $conflict_app_info['name'] . ' ' . app::get('base')->_('与') . ' ' . $conflict_detail['name'] . ' ' . app::get('base')->_('存在冲突') . "\n";
            }
            logger::info($conflict_message . app::get('base')->_('请手工卸载冲突应用')."\n");
            return false;
        }//todo：安装时判断app冲突，检测包括所有依赖的app和现有安装app之间的冲突

        foreach((array)$active_queue as $app_id=>$app_info){
            if(!$app_info){
                logger::info(app::get('base')->_('无法找到应用').$app_id."\n");
                return false;
            }
            kernel::single('base_application_manage')->active($app_id);
        }
    }//End Function

    var $command_update = '升级应用程序';
    var $command_update_options = array(
            'sync'=>array('title'=>'升级应用程序信息库'),
            'sync-only'=>array('title'=>'仅升级应用程序信息库'),
            'force-download'=>array('title'=>'强制下载'),
            'download-only'=>array('title'=>'仅下载应用'),
            'ignore-download'=>array('title'=>'忽略下载'),
            'custom-force-update-db'=>array('title'=>'custom首次安装强制更新数据库'),
            'force-update-db'=>array('title'=>'强制更新数据库'),
            'force-update-app'=>array('title'=>'强制更新应用程序'),
        );
    function command_update(){
        $options = $this->get_options();
        if($options['sync'] || $options['sync-only']){
            kernel::single('base_application_manage')->sync();
        }else{
            kernel::single('base_application_manage')->update_local();
        }
        if($options['sync-only']){
            return true;
        }

        if(!$args){
            $rows = app::get('base')->model('apps')->getList('app_id',array('installed'=>1));
            foreach($rows as $r){
                if($r['app_id'] == 'base')  continue;
                $args[] = $r['app_id'];
            }
        }
        array_unshift($args, 'base');   //todo:总是需要先更新base
        $args = array_unique($args);

        if(!$options['ignore-download']){
            foreach($args as $app_id){
                //kernel::single('base_application_manage')->download($app_id,$options['force-download']);  //todo:临时去掉
            }
        }

        if($options['force-update-db']){
            base_application_dbtable::$force_update = true;
        }

        if($options['custom-force-update-db'])
        {
            if(defined('CUSTOM_CORE_DIR')) {
                foreach(utils::tree(CUSTOM_CORE_DIR) as $k => $v) {
                   if (is_file($v)){
                       touch($v);
                   }
                }
            }
        }

        if(!$options['download-only']){
            foreach($args as $app_id){
                $appinfo = app::get('base')->model('apps')->getList('*', array('app_id'=>$app_id));
                if(version_compare($appinfo[0]['local_ver'], $appinfo[0]['dbver'], '>') || $options['force-update-app']){
                    app::get($app_id)->runtask('pre_update', array('dbver'=>$appinfo[0]['dbver']));
                    kernel::single('base_application_manage')->update_app_content($app_id);
                    app::get($app_id)->runtask('post_update', array('dbver'=>$appinfo[0]['dbver']));
                    app::get('base')->model('apps')->update(array('dbver'=>$appinfo[0]['local_ver']), array('app_id'=>$app_id));
                }else{
                    kernel::single('base_application_manage')->update_app_content($app_id);
                }

				//新更版本等信息
				$app_info = app::get($app_id)->define();
				if (isset($app_info['node_id'])&&$app_info['node_id']=="true" && base_shopnode::node_id($app_id)){
					if(base_shopnode::update($app_id)){
						 logger::info('Applications info send to center, ok.');
					}
				}
            }
            logger::info('Applications database and services is up-to-date, ok.');
        }



        if( (config::get('prism.prismMode') == true) )
        {
            //prism数据更新
            kernel::single('system_prism_init')->update();
        }
    }

    var $command_trace = '打开/关闭性能检测';
    function command_trace($mode=null){
        switch($mode){
        case 'on':
            $this->register_trigger('trace');
            break;

        case 'off':
            $this->unregister_trigger('trace');
            break;
        }
        $this->shell->skip_trigger = true;
        echo 'Trace mode is ' , $this->shell->trigger['trace']?'on':'off';
    }

    var $command_status = '显示系统状态';
    function command_status($part=null){
        $partlen = strlen($part);
        foreach(kernel::servicelist('status') as $srv){
            foreach($srv->get_status() as $k=>$v){
                if(!$partlen || substr($k,0,$partlen+1)==$part.'.'){
                    echo $k,'=>',$v,"\n";
                }
            }
        }
    }

    var $command_search = '在程序库中搜索';
    function command_search()
    {
        $keywords = func_get_args();
        if (empty($keywords))
        {
            echo 'Please search with app id/app name/app description';exit;
        }

        $qb = app::get('base')->database()->createQueryBuilder()
                              ->select('app_id,app_name,description,local_ver,remote_ver')->from('base_apps');
        foreach($keywords as $word)
        {
            $where[] = "app_id like '%{$word}%' or app_name like '%{$word}%' or `description` like '%{$word}%'";
        }
        $rows = $qb->where(call_user_func_array(array($qb->expr(), 'orX'), $where))->execute()->fetchAll();

        $this->output_table($rows);
    }

    function begin_trace(){
        $this->memtrace_begin = memory_get_usage();
        list($usec, $sec) = explode(" ", microtime());
        $this->time_start = ((float)$usec + (float)$sec);
    }

    function end_trace(){
        if(!$this->memtrace_begin)return ;
        list($usec, $sec) = explode(" ", microtime());
        $time_start = ((float)$usec + (float)$sec);
        $mem = memory_get_usage() - $this->memtrace_begin;

        list($usec, $sec) = explode(" ", microtime());
        $timediff = ((float)$usec + (float)$sec) - $this->time_start;
        printf("\n * Command memroy useage = %d, Time left = %f " , $mem , $timediff);
    }

    var $command_kvrecovery = 'kvstore数据恢复';
    function command_kvstorerecovery($instance=null) {
        return $this->command_kvrecovery($instance);
    }

    function command_kvrecovery($instance=null)
    {
        if(!is_null($instance)){
            $instance = trim($instance);
            if(!(strpos($instance, '_') === 0)){
                $instance = 'base_kvstore_' . $instance;
            }
            config::set('kvstore.default', $instance);
        }
        base_kvstore::config_persistent(false);
        $testObj = base_kvstore::instance('test');
        if(get_class($testObj->get_controller()) === 'base_kvstore_mysql'){
            logger::info('The \'base_kvstore_mysql\' is default persistent, Not necessary recovery');
            exit;
        }
        logger::info('KVstore Recovery...');
        $db = app::get('base')->database();
        $count = $db->executeQuery('SELECT count(*) AS count FROM base_kvstore')->fetchColumn();

        if(!$count)
        {
            logger::info('No data recovery');
            exit;
        }

        $pagesize = 100;
        $page = ceil($count / 100);
        for($i=0; $i<$page; $i++){
            $rows = $db->executeQuery('SELECT * FROM base_kvstore limit ? offset ?', [$pagesize, $pagesize*$i])->fetchAll();
            foreach($rows AS $row)
            {
                //logger::info($row['key']);continue;
                $row['value'] = unserialize($row['value']); //todo:合法数据
                if(base_kvstore::instance($row['prefix'])->recovery($row)){
                    logger::info($row['prefix'] .'=>' . $row['key'] . ' ... Recovery Success');
                }else{
                    logger::info($row['prefix'] .'=>' . $row['key'] . ' ... Recovery Failure');
                }
            }
        }
    }//End Function

    var $command_kvdelexpires = 'kvstore清除过期数据，开启持久化功能有效';
    function command_kvdelexpires()
    {
        logger::info('KVstore Delete Expires Data...');
        base_kvstore::delete_expire_data();
    }//End Function

    var $command_cacheclean = '清除缓存';
    function command_cacheclean()
    {
        logger::info('Cache Clear...');
        if(cacheobject::clean($msg)){
            logger::info($msg ? $msg : '...Clear Success');
        }else{
            logger::info($msg ? $msg : '...Clear Failure');
        }
        cacheobject::init(false);
    }//End Function

    var $command_configcompat = 'config兼容配置检测';
    function command_configcompat()
    {
        kernel::single('base_setup_config')->write_compat();
    }//End Function

    var $command_crontab = '运行计划任务';
    function command_crontab ()
    {
        ob_start();
        base_crontab_schedule::trigger_all();
        logger::info('此命令只作为临时处理方案. 如果希望crontab触发, 请用script/crontab/crontab.php');
    }//End Fun

    var $command_crontablist = '列出当前所有crontab';
    function command_crontablist()
    {
        foreach(app::get('base')->model('crontab')->getlist('id, description, schedule') as $row){
            logger::info($row['id'].' : '.$row['description'].' : '.$row['schedule']);
        }
    }

    var $command_crontabexec = '执行指定计划任务，任务名需是crontablist里列出来的';
    function command_crontabexec()
    {
        $crontab = func_get_args();

        if(count($crontab) !== 1 ) {
            logger::info('usage: php cmd crontabexec <crontab_name>');
            exit();
        }

        $cron_id = $crontab[0];
        if (base_crontab_schedule::is_valid_cronentry($cron_id)===false){
            logger::info('cron: '.$cron_id.' not fond');
            exit;
        }

        base_crontab_schedule::run_task($cron_id);
        exit;
    }

}

