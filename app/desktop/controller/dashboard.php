<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_ctl_dashboard extends desktop_controller{

    var $workground = 'desktop_ctl_dashboard';

    public function __construct($app)
    {
        parent::__construct($app);
        //$this->member_model = $this->app->model('members');
        header("cache-control: no-store, no-cache, must-revalidate");
    }


    function index(){
        ;
		//如果没有请求到证书，可以重新请求
		if (!base_certificate::certi_id()|| !base_certificate::token()){
			base_certificate::register();
		}

		if (!base_shopnode::node_id()&&base_certificate::certi_id()&&base_certificate::token()){
			$obj_buildin = kernel::single('base_shell_buildin');
			$obj_buildin->command_active_node_id('ceti_node_id');
		}
        $pagedata['tip'] = base_application_tips::tip();

        //设置shell_base_url
        if (app::get('base')->getConf('shell_base_url')!==kernel::base_url(1)) {
            app::get('base')->setConf('shell_base_url', kernel::base_url(1));
        }

        $user = kernel::single('desktop_user');
        $is_super = $user->is_super();

        $group = $user->group();
        $group = (array)$group;

        //桌面挂件排序，用户自定义


		$layout_map = array('t-1'=>'top','l-1'=>'left','l-2'=>'right','b-1'=>'bottom');


        foreach(kernel::servicelist('desktop.widgets') as $key => $obj){
            if($is_super || in_array(get_class($obj),$group)){
                $class_full_name = get_class($obj);
				$key = $obj->get_width();
                $item = array(
                    'title'=>$obj->get_title(),
                    'html'=>$obj->get_html(),
                    'width'=>$obj->get_width(),
                    'className'=>$obj->get_className(),
                    'class_full_name' => $class_full_name,
					'layout'=>$layout
                    );

                $widgets[$key][] = $item;
            }
        }
        foreach((array)$widgets as $key=>$arr){
			$layout = $layout_map[$key];
			if($user->get_conf('arr_dashboard_widgets_'.$layout.'_sort',$sort_conf)&&$sort_conf){
				//echo $sort_conf.'<br/><br/>';
				$sort_conf = explode(',',$sort_conf);
				array_multisort($sort_conf,SORT_STRING,$arr);
			}
				$widgets[$key] = $arr;

        }
        $pagedata['left'] = $widgets['l-1'];
        $pagedata['right'] = $widgets['l-2'];
        $pagedata['top'] = $widgets['t-1'];
        $pagedata['bottom'] = $widgets['b-1'];
        $deploy = kernel::single('base_xml')->xml2array(file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');

        $pagedata['deploy'] = $deploy;

		return $this->page('desktop/dashboard.html', $pagedata);

	}

    /*
     * 桌面排序
     * 桌面挂件排序，用户自定义
     */
    public function dashboard_sort()
    {
        $desktop_user = kernel::single('desktop_user');
        $arr = explode(':',trim($_GET['sort']));

        $desktop_user->set_conf('arr_dashboard_widgets_'.$arr[0].'_sort',$arr[1]);
    }
    #End Func


    function advertisement(){
		$conf = base_setup_config::deploy_info();
		$pagedata['product_key'] = $conf['product_key'];

        $pagedata['cross_call_url'] =base64_encode(url::route('shopadmin', array('ctl' => 'dashboard', 'act' => 'cross_call', 'app' => 'desktop')));

        return view::make('desktop/advertisement.html', $pagedata);
    }

    function cross_call(){
        header('Content-Type: text/html;charset=utf-8');
        echo '<script>'.str_replace('top.', 'parent.parent.', base64_decode($_REQUEST['script'])).'</script>';
    }


    function appmgr() {
        $arr = app::get('base')->model('apps')->getList('*', array('status'=>'active'));
        foreach( $arr as $k => $row ) {
            if( $row['remote_ver'] <= $row['local_ver'] ) unset($arr[$k]);
        }
        $pagedata['apps'] = $arr;

        return view::make('desktop/appmgr/default_msg.html', $pagedata);


    }



    function fetch_tip(){
        return base_application_tips::tip();
    }

    function profile(){

        //获取该项记录集合
        $users = $this->app->model('users');
        $roles=$this->app->model('roles');
        $workgroup=$roles->getList('*');
        $sdf_users = $users->dump($this->user->get_id());

        if($_POST){
            $this->user->set_conf('desktop_theme',$_POST['theme']);
            $this->user->set_conf('timezone',$_POST['timezone']);
             header('Content-Type:application/json; charset=utf-8');
             echo '{success:"'.app::get('desktop')->_("设置成功").'",_:null}';
             exit;
        }

        $themes = array();
        foreach(app::get('base')->model('app_content')
            ->getList('app_id,content_name,content_path'
        ,array('content_type'=>'desktop theme')) as $theme){
            $themes[$theme['app_id'].'/'.$theme['content_name']] = $theme['content_name'];
        }

        //返回无内容信息
        $pagedata['themes'] = $themes;
        $pagedata['current_theme'] = $this->user->get_theme();
        $pagedata['name'] = $sdf_users['name'];
        $pagedata['super'] = $sdf_users['super'];
        return view::make('desktop/users/profile.html', $pagedata);
    }

     function redit(){
        $desktop_user = kernel::single('desktop_user');
        if($desktop_user->is_super()){
            $this->redirect('?ctl=adminpanel');
        }
        else{
            $aData = $desktop_user->get_work_menu();
            $aMenu = $aData['menu'];
            foreach($aMenu as $val){
                foreach($val as $value){
                    foreach($value as $v){
                        if($v['display']){
                            $url = $v['menu_path'];break;
                        }
                    }
                    break;
                }
                break;
            }
            if(!$url) $url = "ctl=adminpanel";
            $this->redirect('?'.$url);
        }
    }

    public function get_license_html()
    {
        return view::make('desktop/license.html');
    }

    public function application(){
        $certificate = kernel::single('base_certificate');
        if($certificate->register()===false)
        {
            header('Content-Type:application/json; charset=utf-8');
            echo '{error:"'.app::get('desktop')->_("申请失败").'",_:null}';
            //$this->end(false,app::get('desktop')->_('申请失败'));
        }
        else
        {
            header('Content-Type:application/json; charset=utf-8');
            echo '{success:"'.app::get('desktop')->_("申请成功").'",_:null}';
            //$this->end(true,app::get('desktop')->_('申请成功'));
        }
    }

}
