<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * mdl_user
 *
 * @uses modelFactory
 * @package
 * @version $Id: mdl.user.php 1985 2008-04-28 06:36:02Z flaboy $
 * @copyright 2003-2007 ShopEx
 * @author Likunpeng <leoleegood@zovatech.com>
 * @license Commercial
 */

class desktop_mdl_users extends dbeav_model{

    var $has_parent = array(
        'pam_account' => 'account@desktop'
    );
    var $has_many = array(
        'roles' => 'hasrole:replace',
    );
    var $subSdf = array(
        'default' => array(
            'pam_account:account@desktop' => array('*'),
        ),
        'delete' => array(
            //'pam_account:account@desktop' => array('*'),
            'roles' => array('*'),
        ),
        'test' => array(
            'pam_account' => array('*'),
            'roles' => array('*'),
        )

    );
    function editUser(&$data){
        if($data['userpass'])
        {
            $data[':account@desktop']['login_password'] = pam_encrypt::make(trim($data['userpass']));
        }
        $data['pam_account']['account_type'] = pamAccount::getAuthType($this->app->app_id);
        $data['pam_account']['createtime'] = time();

        //return parent::save($data);
        parent::save($data);
        exit;
    }
    ###

    ##检查用户名
    function check_name($login_name){
        $pam = app::get('desktop')->model('account');
        $account_type = pamAccount::getAuthType($this->app->app_id);
        $aData = $pam->getList('*',array('login_name' => $login_name,'account_type' =>$account_type ));
        $result = $aData[0]['account_id'];
        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    ###更新登录信息

    function update_admin($user_id){

        $aUser = $this->dump($user_id,'*');
        $sdf[':account@desktop']['account_id'] = $user_id;
        $sdf['lastlogin'] = time();
        $sdf['logincount'] = $aUser['logincount']+1;
        $this->save($sdf);
    }

    ##检查
    function validate($aData,&$msg){

        if($aData['pam_account']['login_name']==''||$aData['pam_account']['login_password']==''||$aData['name']==''){
            $msg = app::get('desktop')->_('必填项不能为空');
            return fasle;
        }
        if($aData['pam_account']['login_password']!=$_POST['re_password']){
            $msg = app::get('desktop')->_('两次密码输入不一致');
            return false;
        }
        if(!(strlen($aData['pam_account']['login_password']) >= 6 && preg_match("/\d+/",$aData['pam_account']['login_password']) && preg_match("/[a-zA-Z]+/",$aData['pam_account']['login_password']))){
            $msg = app::get('desktop')->_('密码必须同时包含字母及数字且长度不能小于6!');
            return false;
        }
        if($aData['pam_account']['login_name'] == $aData['pam_account']['login_password']){
            $msg = app::get('desktop')->_('用户名与密码不能相同');
            return false;
        }

        $result = $this->check_name($aData['pam_account']['login_name']);

        if($result){
            $msg = app::get('desktop')->_('该用户名已存在');
            return false;

        }
        return true;
    }

    //获取工作组细分
    function detail_per($check_id,$user_id){
        $roles = $this->app->model('roles');
        $menus =$this->app->model('menus');
        $aPermission =array();
        if(!$check_id) {
            echo '';exit;
        }
        foreach($check_id as $val){
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach((array)$data as $row){
                $aPermission[] = $row;
            }
        }
        $aPermission = array_unique($aPermission);
        if(!$aPermission){
            echo '';exit;
        }
        $addonmethod = array();
        foreach((array)$aPermission as $val){
            $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
            $addon = unserialize($sdf['addon']);
            if($addon['show']&&$addon['save']){  //如果存在控制
                if(!in_array($addon['show'],$addonmethod)){
                    $access = explode(':',$addon['show']);
                    $classname = $access[0];
                    $method = $access[1];
                    $obj = kernel::single($classname);
                    $html.=$obj->$method($user_id);
                }
                $addonmethod[] = $addon['show'];
            }
            else{
                echo '';
            }
        }
        return $html;
    }

    //保存工作组细分
    function save_per($aData){
        $workgrounds = $aData['role'];
        $menus = $this->app->model('menus');
        $roles =  $this->app->model('roles');
        foreach($workgrounds as $val){
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach((array)$data as $row){
                $aPermission[] = $row;
            }
        }
        $aPermission = array_unique($aPermission);
        if($aPermission){
            $addonmethod = array();
            foreach((array)$aPermission as $key=>$val){
                $sdf = $menus->dump(array('menu_type' => 'permission','permission' => $val));
                $addon = unserialize($sdf['addon']);
                if($addon['show']&&$addon['save']){  //如果存在控制
                    if(!in_array($addon['save'],$addonmethod)){
                        $access = explode(':',$addon['save']);
                        $classname = $access[0];
                        $method = $access[1];
                        $obj = kernel::single($classname);
                        $obj->$method($aData['user_id'],$aData);
                    }
                    $addonmethod[] = $addon['save'];
                }
            }
        }
    }
    //超级管理员的判断
    public function delete($filter,$subSdf = 'delete')
    {
        $userInfo = $this->getList('super',array('user_id'=>$filter['user_id']));
        foreach ($userInfo as $key => $value)
        {
            $super[$key]=$value['super'];
        }

        if(in_array(1, $super))
        {
            $msg = "该用户为超级管理员，不可以删除！";
            throw new \logicException($msg);
        }
        return parent::delete($filter);
    }
}
