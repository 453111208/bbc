<?php

class sysshop_mdl_seller extends dbeav_model {

    public function _filter($filter,$tableAlias=null,$baseWhere=null)
    {
        if( $filter['login_account'] )
        {
            $tmpfilter['login_account'] = $filter['login_account'];
            unset($filter['login_account']);
        }

        if( $tmpfilter )
        {
            $aData = app::get('sysshop')->model('account')->getList('seller_id',$tmpfilter);
            if($aData)
            {
                foreach($aData as $key=>$val)
                {
                    $seller[$key] = $val['seller_id'];
                }
                $filter['seller_id'] = $seller;
            }
            else
            {
                $filter['seller_id'] = '-1';
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
        $columns = array(
            'login_account'=>app::get('sysshop')->_('用户名'),
        );

        return $columns;
    }

    public function doDelete($sellerIds)
    {
        $params['filter']['seller_id'] = $sellerIds;
        $objCheck = kernel::single('sysshop_check');
        $objMdlUser = app::get('sysshop')->model('seller');
        $objMdlPamUser = app::get('sysshop')->model('account');
        try
        {
            $result = $objCheck->checkDelete($sellerIds);
            $result = $objMdlUser->delete(array('seller_id'=>$sellerIds));
            if(!$result)
            {
                $msg = "删除商家基本信息失败";
                throw new \logicException($msg);
                return false;
            }
            $result = $objMdlPamUser->delete(array('seller_id'=>$userIds));
            if(!$result)
            {
                $msg = "删除商家登录信息失败";
                throw new \logicException($msg);
                return false;
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            throw new \logicException($msg);
            return false;
        }
        return true;

    }
}

