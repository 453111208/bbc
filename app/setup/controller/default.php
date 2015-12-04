<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class setup_ctl_default extends setup_controller{

    public function __construct($app)
    {
        kernel::set_online(false);
        if(kernel::single('base_setup_lock')->lockfile_exists()){
            if(!kernel::single('base_setup_lock')->check_lock_code()){
                $this->lock();
            }
        }
        parent::__construct($app);
        config::set('log.default', 'file');
    }

    public function console()
    {

        $shell = new base_shell_webproxy;
        $shell->input = $_POST['options'];
        echo "\n";
        $shell->exec_command($_POST['cmd']);
    }

    private function lock(){
        return response::make('<h3>Setup Application locked by config/install.lock.php</h3><hr />', 401)->send();
    }

    public function index(){
        $pagedata['conf'] = base_setup_config::deploy_info();
        //判断是哪个系统的标志图
        $commerce_class = kernel::single('system_commerce');
        if(!$commerce_class->get_commerce_version()){
            $pagedata['install_bg'] = kernel::base_url(1).'/app/setup/statics/images/setup_product.jpg';
        }else{
            $pagedata['install_bg'] = kernel::base_url(1).'/app/setup/statics/images/setup_cproduct.jpg';
        }
        $pagedata['statics_url'] = $this->app->res_url;

        return view::make('setup/installer-start.html', $pagedata);
    }

    public function process()
    {
        config::set('app.debug', true);
        set_time_limit(0);
        $serverinfo = kernel::single('setup_serverinfo')->run($_POST['installer_check']);

        if($serverinfo['allow_install'] != 1){
            $pagedata['serverinfo'] = $serverinfo;
        }
        $pagedata['conf'] = base_setup_config::deploy_info();
        $install_queue = $this->install_queue($pagedata['conf']);
        $install_options = array();
        if(is_array($install_queue)){
            foreach($install_queue as $app_id=>$app_info){
                $option = app::get($app_id)->runtask('install_options');
                if(is_array($option) && count($option)>=1){
                    $install_options[$app_id] = $option;
                }
            }
        }

        $pagedata['install_options'] = &$install_options;
        $pagedata['install_demodata_options'] = $this->install_demodata_options($pagedata['conf']);

        $pagedata['res_url'] = $this->app->res_url;
        $pagedata['apps'] = &$install_queue;
        if ($pagedata['conf']['demodatas']){
            $pagedata['demodata'] = array(
                'install'=>'true',
                'name'=>'demodata',
                'description'=>'demodata',
            );
        }else{
            $pagedata['demodata'] = 'false';
        }

        if (isset($pagedata['conf']['active_ceti'])&&$pagedata['conf']['active_ceti'])
        {
            $pagedata['success_page'] = $pagedata['conf']['active_ceti']['active_ceti_url'];
        }
        else
        {
            $pagedata['success_page'] = 'success';
        }

        // 授权文件从根目录去取
        if (isset($pagedata['conf']['licence']['file']))
        {
            $pagedata['conf']['licence']['file'] = ROOT_DIR.'/'.$pagedata['conf']['licence']['file'];
        }

        if($_GET['console']){
            $output = view::make('setup/console.html', $pagedata)->render();
        }else{
            $output = view::make('setup/installer.html', $pagedata)->render();
        }
        return str_replace('%BASE_URL%',kernel::base_url(1),$output);
    }

    public function success()
    {
        $pagedata['conf'] = base_setup_config::deploy_info();
        $commerce_class = kernel::single('system_commerce');
        if(!$commerce_class->get_commerce_version()){
            $pagedata['install_bg'] = kernel::base_url(1).'/app/setup/statics/images/setup_product.jpg';
        }else{
            $pagedata['install_bg'] = kernel::base_url(1).'/app/setup/statics/images/setup_cproduct.jpg';
        }
        $output = view::make('setup/installer-success.html', $pagedata)->render();
        return str_replace('%BASE_URL%',kernel::base_url(1),$output);
    }

    public function active(){
        $pagedata['conf'] = base_setup_config::deploy_info();
        $pagedata['callback_ur'] = base64_encode(url::action('setup_ctl_default@success'));
        $pagedata['enterprise_url'] = config::get('link.shop_user_enterprise');
        $output = view::make('setup/installer-active.html', $pagedata)->render();
        return str_replace('%BASE_URL%',kernel::base_url(1),$output);
    }

    private function install_queue($config=null){
        $config = $config?$config:base_setup_config::deploy_info();

        foreach($config['package']['app'] as $k=>$app){
            if ($app['default'] ==='true') {
                $applist[] = $app['id'];
            }
        }

        return kernel::single('base_application_manage')->install_queue($applist);
    }

    /**
     * µÃµ½deploy²¿ÊðµÄdemo dataÑ¡ÔñÏîÄ¿
     * @param null
     * @return array
     */
    private function install_demodata_options($config=null)
    {
        $config = $config?$config:base_setup_config::deploy_info();

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

        return $install_options;
    }

    public function initenv(){
        try {
            kernel::single('base_setup_lock')->write_lock_file();

            header('Content-type: text/plain; charset=UTF-8');

            $install_queue = $this->install_queue();
            foreach($install_queue as $app_id=>$app_info){
                if(false === app::get($app_id)->runtask('checkenv',$_POST['options'][$app_id])){
                    $error = true;
                }
            }
            if($error){
                echo 'check env failed';
            }else{
                echo 'config init ok.';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function install_app(){
        try {
            kernel::set_online(true);
            $app = $_GET['app'];
            //todo: config check
            $shell = new base_shell_webproxy;
            $shell->input = $_POST['options'];
            $shell->exec_command('install -r '.$app);
            exit;

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function install_demodata(){
        kernel::set_online(true);
        //todo: config check
        $shell = new base_shell_webproxy;
        $shell->input = $_POST['options'];
        $shell->exec_command('install_demodata -r demodata');
    }

    public function setuptools()
    {
        $app = addslashes(input::get('app'));
        $method = addslashes(input::get('method'));
        if(empty($app) || empty($method))   die('call error');
        $data = app::get($app)->runtask($method, $_POST['options']);
        return response::json($data);
    }//End Function

}
