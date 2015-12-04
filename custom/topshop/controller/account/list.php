<?php

class topshop_ctl_account_list extends topshop_controller {

    public function index()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('账号管理');
        $params['shop_id'] = $this->shopId;
        $data = app::get('topshop')->rpcCall('account.shop.user.list',$params);
        $pagedata['data'] = $data;

        $rolesData = app::get('topshop')->rpcCall('account.shop.roles.list',$params);
        foreach( $rolesData as $row )
        {
            $pagedata['rolesName'][$row['role_id']] = $row['role_name'];
        }

        return $this->page('topshop/account/user/list.html', $pagedata);
    }

    public function edit()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('添加子帐号');

        //面包屑
        $this->runtimePath = array(
            ['url'=> url::action('topshop_ctl_index@index'),'title' => app::get('topshop')->_('首页')],
            ['url'=> url::action('topshop_ctl_account_list@index'),'title' => app::get('topshop')->_('账号管理')],
            ['title' => app::get('topshop')->_('添加子帐号')],
        );

        if( input::get('seller_id') )
        {
            $params['shop_id'] = $this->shopId;
            $params['seller_id'] = input::get('seller_id');
            $data = app::get('topshop')->rpcCall('account.shop.user.get',$params);
            if( $data )
            {
                $pagedata = $data;
            }
        }

        $params['shop_id'] = $this->shopId;
        $rolesData = app::get('topshop')->rpcCall('account.shop.roles.list',$params);
        $pagedata['rolesData'] = $rolesData;

        return $this->page('topshop/account/user/edit.html', $pagedata);
    }

    public function save()
    {
        if( !input::get('role_id',false) )
        {
            $msg = '请选择角色';
            return $this->splash('error','',$msg,true);
        }

        try
        {
            if( input::get('seller_id') )
            {
                $params = input::get();
                $params['shop_id'] = $this->shopId;
                app::get('topshop')->rpcCall('account.shop.user.update',$params);
                $msg = '修改子帐号成功';
            }
            else
            {
                $params = input::get();
                $params['shop_id'] = $this->shopId;
                app::get('topshop')->rpcCall('account.shop.user.add',$params);
                $msg = '创建子帐号成功';
            }
        }
        catch( \LogicException $e )
        {
            $msg = $e->getMessage();
            return $this->splash('error','',$msg,true);
        }

        $url = url::action('topshop_ctl_account_list@index');
        return $this->splash('success',$url,$msg,true);
    }

    public function modifyPwd()
    {
        $params['shop_id'] = $this->shopId;
        $params['seller_id'] = input::get('seller_id');
        $data = app::get('topshop')->rpcCall('account.shop.user.get',$params);
        if( !$data || $data['seller_type'] !='1' )
        {
            $msg = '修改失败';
            return $this->splash('error',$url,$msg,true);
        }

        try
        {
            $setPwdData['login_password'] = input::get('login_password');
            $setPwdData['psw_confirm'] = input::get('psw_confirm');
            shopAuth::resetPwd($params['seller_id'], $setPwdData);
        }
        catch( \LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',$url,$msg,true);
        }

        $msg = '修改成功';
        $url = url::action('topshop_ctl_account_list@index');
        return $this->splash('success',$url,$msg,true);
    }

    public function delete()
    {
        $sellerId = input::get('seller_id', false);
        if( !$sellerId )
        {
            $msg = '删除失败';
            return $this->splash('error','',$msg,true);
        }

        try
        {
            $params['seller_id'] = $sellerId;
            $params['shop_id'] = $this->shopId;
            app::get('topshop')->rpcCall('account.shop.user.delete',$params);
        }
        catch( \LogicException $e )
        {
            $msg = $e->getMessage();
            return $this->splash('error','',$msg,true);
        }

        $msg = '删除成功';
        $url = url::action('topshop_ctl_account_list@index');
        return $this->splash('success',$url,$msg,true);
    }
}

