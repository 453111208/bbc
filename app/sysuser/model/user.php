<?php

class sysuser_mdl_user extends dbeav_model
{

    public function _filter($filter,$tableAlias=null,$baseWhere=null)
    {
        if( is_array($filter) && $filter['login_account'] )
        {
            $tmpfilter['login_account'] = $filter['login_account'];
            unset($filter['login_account']);
        }

        if( is_array($filter) &&  $filter['email'] )
        {
            $tmpfilter['email'] = $filter['email'];
            unset($filter['email']);
        }

        if( is_array($filter) &&  $filter['mobile'] )
        {
            $tmpfilter['mobile'] = $filter['mobile'];
            unset($filter['mobile']);
        }

        if( is_array($filter) &&  $tmpfilter )
        {
            $aData = app::get('sysuser')->model('account')->getList('user_id',$tmpfilter);
            if($aData)
            {
                foreach($aData as $key=>$val)
                {
                    $user[$key] = $val['user_id'];
                }
                $filter['user_id'] = $user;
            }
            else
            {
                $filter['user_id'] = '-1';
            }
        }
        $filter = parent::_filter($filter);
        return $filter;
    }

    /**
     * 重写搜索的下拉选项方法
     * @param null
     * @return null
     */
    public function searchOptions(){
        $columns = array();
        foreach($this->_columns() as $k=>$v)
        {
            if(isset($v['searchtype']) && $v['searchtype'])
            {
                $columns[$k] = $v['label'];
            }
        }

        $columns = array_merge(array(
            'login_account'=>app::get('sysuser')->_('用户名'),
            'email'=>app::get('sysuser')->_('邮箱'),
            'mobile'=>app::get('sysuser')->_('手机'),
        ),$columns);

        return $columns;
    }


    public function doDelete($userIds)
    {
        $objCheck = kernel::single('sysuser_check');
        $objMdlUser = app::get('sysuser')->model('user');
        $objMdlPamUser = app::get('sysuser')->model('account');
        $objMdlTrustInfo = app::get('sysuser')->model('trustinfo');
        try
        {
            $result = $objCheck->checkDelete($userIds);
            $result = $objMdlUser->delete(array('user_id'=>$userIds));
            if(!$result)
            {
                $msg = "删除会员基本信息失败";
                throw new \LogicException($msg);
            }
            $result = $objMdlPamUser->delete(array('user_id'=>$userIds));
            if(!$result)
            {
                $msg = "删除会员登录信息失败";
                throw new \LogicException($msg);
            }
            $trustInfo = $objMdlTrustInfo->getList('user_id',array('user_id'=>$userIds));
            if($trustInfo)
            {
                $result = $objMdlTrustInfo->delete(array('user_id'=>$userIds));
                if(!$result)
                {
                    $msg = "删除会员登录信息失败";
                    throw new \LogicException($msg);
                }
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }
}

