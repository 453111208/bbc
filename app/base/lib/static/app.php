<?php
   /**
    * ShopEx licence
    *
    * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
    * @license  http://ecos.shopex.cn/ ShopEx License
    */

class base_static_app
{
    static private $__instance = array();
    static private $__language = null;
    private $__define = null;
    private $__taskrunner = null;
    private $__checkVaryArr = array();
    private $__langPack = array();
    private $__installed = null;
    private $__actived = null;
    private $__setting = null;

    function __construct($app_id){
        $this->app_id = $app_id;
        $this->app_dir = APP_DIR.'/'.$app_id;
        $this->public_app_dir = PUBLIC_DIR.'/app/'.$app_id;

		$this->res_url = kernel::get_app_statics_host_url().'/'.$app_id.'/statics';
		$this->res_full_url = kernel::get_app_statics_host_url().'/'.$app_id.'/statics';
		$this->lang_url = kernel::get_app_statics_host_url().'/'.$app_id.'/lang';
		$this->lang_full_url = kernel::get_app_statics_host_url().'/'.$app_id.'/lang';
		$this->widgets_url = kernel::get_app_statics_host_url().'/'.$app_id.'/widgets';
		$this->widgets_full_url = kernel::get_app_statics_host_url().'/'.$app_id.'/widgets';

        $this->res_dir = PUBLIC_DIR.'/app/'.$app_id.'/statics';
        $this->widgets_dir = PUBLIC_DIR.'/app/'.$app_id.'/widgets';
        $this->lang_dir = PUBLIC_DIR.'/app/'.$app_id.'/lang';
        //$this->lang_resource = lang::get_res($app_id);  //todo: 得到语言包资源文件结构
        $this->_lang_resource = null;
    }

    static function get($app_id){
        if(!isset(self::$__instance[$app_id])){
            self::$__instance[$app_id] = new app($app_id);
        }
        return self::$__instance[$app_id];
    }

    public function lang_resource($lang=null){
        if (!isset($this->_lang_resource)) {
            $this->_lang_resource = lang::get_res($this->app_id);
        }
        return !isset($lang)?$this->_lang_resource:$this->_lang_resource[$lang];
    }

    public function _($key)
    {
        return lang::_($this->lang_dir, $key);
    }//End Function

    public function lang($res=null, $key=null)
    {
        return lang::get_info($this->app_id, $res, $key);     //取得语言包数据
    }//End Function

    public function controller($controller){
        return kernel::single($this->app_id.'_ctl_'.$controller, $this);
    }

    public function model($model){
        return kernel::single($this->app_id.'_mdl_'.$model, $this);
    }

    public function setting() {
        if (!$this->__setting) {
            $this->__setting = new base_setting($this);
        }
        return $this->__setting;
    }

    public function get_parent_model_class(){
        $parent_model_class = $this->define('parent_model_class');
        return $parent_model_class?$parent_model_class:'dbeav_model';
    }

    public function define($path=null){
        if(!$this->__define){
            if(is_dir($this->app_dir) && file_exists($this->app_dir.'/app.xml')){
                $tags = array();
                $file_contents = file_get_contents($this->app_dir.'/app.xml');
                $this->__define = kernel::single('base_xml')->xml2array(
                   $file_contents ,'base_app');
            }else{
                $row = app::get('base')->model('apps')->getList('remote_config',array('app_id'=>$this->app_id));
                $this->__define = $row[0]['remote_config'];
            }
        }
        if($path){
            return eval('return $this->__define['.str_replace('/','][',$path).'];');
        }else{
            return $this->__define;
        }
    }

    public function getConf($key){
        return $this->setting()->get_conf($key);
    }

    public function setConf($key, $value){
        if($this->setting()->set_conf($key, $value)) {
            syscache::instance('setting')->set_last_modify();
            return true;
        } else {
            return false;
        }
    }

    function runtask($method,$option=null){
        if($this->__taskrunner===null){
            $this->__taskrunner = false;
            if(defined('CUSTOM_CORE_DIR') && file_exists(CUSTOM_CORE_DIR.'/'.$this->app_id.'/task.php')){
                $taskDir = CUSTOM_CORE_DIR.'/'.$this->app_id.'/task.php';
            }else{
                $taskDir = $this->app_dir.'/task.php';
            }
            if(file_exists($taskDir)){
                require($taskDir);
                $class_name = $this->app_id.'_task';
                if(class_exists($class_name)){
                    $this->__taskrunner = new $class_name($this);
                }
            }
        }
        if(is_object($this->__taskrunner) && method_exists($this->__taskrunner,$method)){
            return $this->__taskrunner->$method($option);
        }else{
            return true;
        }
    }

    function status(){
        if(kernel::is_online()){
            try
            {
                $row = app::get('base')->database()->executeQuery('select status from base_apps where app_id = ?', [$this->app_id])->fetch();
                return $row['status']?$row['status']:'uninstalled';
            } catch (Exception $e) {
                return 'uninstalled';
            }
        }else{
            return 'uninstalled';
        }
    }

    public function getDatabasePrefix()
    {
        return 'app|'.$this->app_id;
    }

    public function database()
    {
        $connectionKey = $this->getDatabasePrefix();
        return db::connection($connectionKey);
    }

    /***
     *
     * 请求API的统一入口
     * @param string method 方法名,api的key
     * @param array parameters 请求api的参数，每个api请参考api的业务需求
     * @param array identity 用户信息
     *
     * @return array 返回api的信息
     *
     */
    public function rpcCall($method, $parameters = array(),$identity)
    {
        $appName = $this->app_id;
        $appKey = apiUtil::getAppKey($appName);
        return rpc::call($method, $parameters, $appKey,$identity);
    }

    function is_installed()
    {
        if(is_null($this->__installed)){
            $this->__installed = ($this->status()!='uninstalled') ? true : false;
        }
        return $this->__installed;
    }//End Function

    function is_actived()
    {
        if(is_null($this->__actived)){
            $this->__actived = ($this->status()=='active') ? true : false;
        }
        return $this->__actived;
    }//End Function

    function remote($node_id){
        return new base_rpc_caller($this,$node_id);
    }

    function matrix($node_id=1,$version=1){
        return new base_rpc_caller($this,$node_id,$version);
    }

}
