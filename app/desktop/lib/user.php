<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_user{

    private $mark_modified = false;

    function __construct(){
        pamAccount::setAuthType('desktop');
        if( pamAccount::check())
        {
            $this->user_id =  pamAccount::getAccountId();
            if(empty($_SESSION['account']['user_data']) )
            {
                $_SESSION['account']['user_data'] = array();
                $account = app::get('desktop')->model('account');
                if($row = $account->getList('account_id',array('account_id' => $this->user_id)))
                {
                    $model = app::get('desktop')->model('users');
                    $data = $model->getList('user_id',array('user_id' => $this->user_id));
                    if(!$data)
                    {
                        unset($_SESSION['account'][$this->account_type]);
                    } else {
                        $_SESSION['account']['user_data'] = app::get('desktop')->model('users')->dump($this->user_id,'*',array( ':account@desktop'=>array('*') ));
                    }
                }
                else
                {
                    unset($_SESSION['account'][$this->account_type]);
                }
            }
            $this->user_data = $_SESSION['account']['user_data'];
        }else{
            $this->user_data = array();
        }
    }

    function get_name(){
        return $this->user_data['name'];
    }

    function get_login_name(){
        return $this->user_data['account']['login_name'];
    }
    function get_id(){
        return $this->user_id;
    }

    function is_super(){
        return $this->user_data['super'];
    }

    function get_status(){
        return $this->user_data['status'];
    }

    function logout(){

    }

    function valid(){
    }

    function valid_permission(){
    }

    function get_conf($key,&$return){
        if(!isset($this->config)){
            $info = app::get('desktop')->model('users')->dump($this->user_id,'config');
            $this->config = $info['config'];
        }
        if(array_key_exists($key,(array)$this->config)){
            $return = $this->config[$key];
            return true;
        }else{
            return false;
        }
    }

    function set_conf($key,$value){
        $this->config[$key] = $value;
        if(!$this->mark_modified){
            $this->mark_modified = true;
            register_shutdown_function(array(&$this,'save_conf'));
        }
        return true;
    }

    public function save_conf(){
        $info = app::get('desktop')->model('users')->dump($this->user_id,'config');
        $this->config = array_merge((array)$info['config'],(array)$this->config);
        app::get('desktop')->model('users')->update(
                            array('config'=>$this->config),
                            array('user_id'=>$this->user_id));
    }

    function get_theme(){
        if($this->get_conf('desktop_theme',$current_theme)){
            return $current_theme;
        }else{
            return 'desktop/default';
        }
    }

    function has_roles(){
        return array(0);
    }
     #获取用户 permission ID
    function group(){

        $hasrole = app::get('desktop')->model('hasrole');
        $roles = app::get('desktop')->model('roles');
        $menus = app::get('desktop')->model('menus');
        $sdf = $hasrole->getList('role_id',array('user_id'=>$this->user_id));
        $pass = array();
        foreach($sdf as $val){
        $pass[] = $roles->dump($val,'*');
        }
        $group = array();

       foreach($pass as $key){
       $work = unserialize($key['workground']);
       if(!$work){echo app::get('desktop')->_("无任何权限");exit;}
       foreach($work as $val){
                    $group[] = $val;
       }
          }
    return $group;
    }

    #检查工作组权限

    function chkground($workground){
        if($workground == 'desktop_ctl_dashboard') { return true;}
        if($workground == ''){return true;}
        if($_GET['ctl'] == 'adminpanel') return true;
        $menus = app::get('desktop')->model('menus');
        /*
         $act = app::get('desktop')->model('menus')->getList(
            'menu_id,app_id,menu_title,menu_path',
            array('menu_type'=>'workground','disabled'=>0)
        );
          $actions = array();
         $group = $this->group();
         foreach($act as $val){

         if(in_array($val['menu_id'],$group)){
         $actions[]=$val['menu_name'];
         }
     }
   */
   $group = $this->group();
   $permission_id = $menus->permissionId($_GET);
   if($permission_id == '0'){return true;}
         if(in_array($permission_id,$group)){
         return true;
     }
     else{
         return false;
     }

    }

     ###更新登录信息

   function login(){
     $users = app::get('desktop')->model('users') ;
     $aUser = $users->dump($this->user_id,'*');
     $sdf['lastlogin'] = $_SESSION['login_time']?$_SESSION['login_time']:time();
     unset($_SESSION['login_time']);
     $sdf['logincount'] = $aUser['logincount']+1;
     if($this->user_id){$users->update($sdf,array('user_id'=>$this->user_id));}
   }


   //todo根据管理员ID获得工作组菜单和相应的子菜单

   function get_work_menu(){
        $aWorkground = app::get('desktop')->model('menus')->getList(
            'menu_id,app_id,menu_title,menu_path,menu_type,workground,menu_group,target',
            array('menu_type'=>'workground','disabled'=>0,'display' => 1)
        );
        $aMenu = app::get('desktop')->model('menus')->getList(
            'menu_id,app_id,menu_title,menu_path,menu_type,workground,menu_group,addon,target',
            array('menu_type'=>'menu','disabled'=>0,'display' => 1)
        );

        if($this->is_super()){
            foreach($aWorkground as $value){
                $tmp[$value['workground']] = $value;
            }
            $aData['workground'] = $tmp;
            //
            $allkey_workground = array_keys($aData['workground']);
            //
            unset($tmp);
            foreach($aMenu as $value){
                if($value['menu_title']) $value['menu_title'] = app::get('desktop')->_($value['menu_title']);
                if($value['menu_group']) $value['menu_group'] = app::get('desktop')->_($value['menu_group']);
                $group= $value['menu_group']?$value['menu_group']:'nogroup';
                $tmp[$value['workground']][$group][] = $value;
            }
            $aData['menu'] = $tmp;
        }
        else{
            $group = $this->group();
            $meuns = app::get('desktop')->model('menus');
            $data = array();
            $data_menus = array();
            foreach($group as $key=>$val){
                $aTmp = $meuns->workgroup($val);
                foreach($meuns->get_menu($val) as $v){
                    $group= $v['menu_group']?$v['menu_group']:'nogroup';
                    if(!@in_array($v,$data_menus[$aTmp[0]['workground']][$group])) $data_menus[$aTmp[0]['workground']][$group][] = $v;
                }
                foreach($aTmp as $val ){
                    $data[$val['workground']] =$val;
                }
            }
            $aData['workground'] = $data;
              //
            $allkey_workground = array_keys($aData['workground']);
            //
            $aData['menu'] = $data_menus;
        }
        foreach((array)$aData['menu'] as $k1=>$group){
            if(!in_array($k1,(array)$allkey_workground)) break;
            $menu_default = current(current($aData['menu'][$k1]));
            $__query = '';
            if($menu_default['addon']){
                $__params =  unserialize($menu_default['addon']);
                if(is_array($__params['url_params'])) $__query = '&'.http_build_query($__params['url_params']);
            }
            if($__query) $menu_default['menu_path'] = $menu_default['menu_path'].$__query;
            $aData['workground'][$k1]['menu_path'] = $aData['workground'][$k1]['menu_path']?$aData['workground'][$k1]['menu_path']:$menu_default['menu_path'];
            $aData['workground'][$k1]['target'] = $aData['workground'][$k1]['target']?$aData['workground'][$k1]['target']:$menu_default['target'];
            //ksort($aData['menu'][$k1]);
            foreach($group as $k2=>$menus){
                if(!$menus){unset($aData['menu'][$k1][$k2]);continue;}
                foreach($menus as $k3=>$menu){
                    $query = '';
                    if($menu['addon']){
                        $params =  unserialize($menu['addon']);
                        if(is_array($params['url_params'])) $query = '&'.http_build_query($params['url_params']);
                    }
                    $menu['menu_path'] = $menu['menu_path'].$query;
                    $aData['menu'][$k1][$k2][$k3] = $menu;
                }
            }
        }
        return $aData;
   }

   function has_permission($permission_id)
   {
       if($this->is_super()) return true;
       $group = $this->group();
       if(in_array($permission_id,$group))
           return true;
       else
           return false;
   }
}
