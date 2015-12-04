<?php
/**
 * @brief 商城账号
 */

class sysuser_ctl_admin_user extends desktop_controller {


    function __construct($app){
        parent::__construct($app);
        $this->pamUserModel = app::get('sysuser')->model('account');
        $this->sysUserModel = app::get('sysuser')->model('user');
    }
    /**
     * @brief  企业账号列表
     *
     * @return
     */
    public function index()
    {
        return $this->finder('sysuser_mdl_user',array(
            'title' => app::get('sysuser')->_('商城会员列表'),
            'use_buildin_filter' => true,
            'use_buildin_delete' => true,
        ));
    }

    public function license()
    {
        if( $_POST['license'] )
        {
            $this->begin();
            app::get('sysuser')->setConf('sysuser.register.setting_user_license',$_POST['license']);
            $this->end(true, app::get('sysuser')->_('当前配置修改成功！'));
        }
        $pagedata['license'] = app::get('sysuser')->getConf('sysuser.register.setting_user_license');
        return $this->page('sysuser/license.html', $pagedata);
    }


public function premoney(){
        $saveItem = app::get('sysuser')->model('user');
        $savereco = app::get('sysuser')->model('moneyreco');
        $sa=kernel::single('desktop_user');
        $admName = $sa->get_login_name();
        $postData = input::get();

        if($postData['changemoney']==0){
            $msg = '充值金额不能为0';
            return $this->splash('error',null,$msg);
        }
        $paminfo = $saveItem->getRow('*',array('user_id'=>$postData['user_id']));
        $paminfoold = $paminfo;
        $postData['paytime'] = strtotime($postData['paytime']);
        $postData['create_time']=time();
        $postData['name']=$admName;

        if($postData['changemoney']>0)$postData['pay'] = 0;
        else $postData['pay'] = 1;        
        $fileObject=input::file('ensurence');
        if($fileObject){
        $storager = kernel::single('base_storager');
        $result = $storager->upload($fileObject);
        $arr = explode("|",$result);
        $postData['ensurence']=$arr[0];
        }

        try
        {
            $savereco->save($postData);
            $changes = $postData['changemoney'];
            $cut = $paminfo['hjadvance']+$changes;
            $paminfo['hjadvance'] = $cut;
            $saveItem->update($paminfo,$paminfoold);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return view::make('sysuser/admin/user/savesucc.html')->render();
    }
    /**
     * @brief  前台会员信息修改
     *
     * @return
     */
    public function editUserInfo($userId)
    {
        
        $sysInfo = kernel::single('sysuser_passport')->memInfo($userId);

        if($sysInfo['sex']==1)
        {
            $sysInfo['sex']='male';
        }
        else
        {
            $sysInfo['sex']='female';
        }
        $data = array(
            'user_id'=>$sysInfo['userId'],
            'name'=>$sysInfo['name'],
            'sex'=>$sysInfo['sex'],
            'birthday'=>$sysInfo['birthday'],
            'reg_ip'=>$sysInfo['reg_ip'],
            'regtime'=>$sysInfo['regtime'],
            'login_account'=>$sysInfo['login_account'],
            'email'=>$sysInfo['email'],
            'mobile'=>$sysInfo['mobile'],
        );

        $pagedata['data'] = $data;
        return $this->page('sysuser/admin/editinfo.html', $pagedata);
    }

    /**
     * @brief  前台会员信息保存
     *
     * @return
     */
    public function saveUserInfo()
    {
        try
        {
            $data = $_POST;
            kernel::single('sysuser_passport')->saveInfo($data);
            $this->adminlog("修改会员信息[USER_ID:{$data['user']['user_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("修改会员信息[USER_ID:{$data['user']['user_id']}]", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $msg = app::get('sysuser')->_('修改成功');

        return $this->splash('success',null,$msg);
    }

    /**
     * @brief  前台会员密码修改
     *
     * @return
     */
    public function updatePwd()
    {
        try
        {
            $data = $_POST;
            $params = array(
                'type' =>'reset',
                'new_pwd' =>$data['login_password'],
                'confirm_pwd' =>$data['psw_confirm'],
               'user_id' =>$data['user_id'],
            );
            kernel::single('sysuser_passport')->modifyPwd($params);
            $this->adminlog("修改会员密码[USER_ID:{$data['user_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("修改会员密码[USER_ID:{$data['user_id']}]", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $msg = app::get('sysuser')->_('修改成功');

        return $this->splash('success',null,$msg);
    }
}
