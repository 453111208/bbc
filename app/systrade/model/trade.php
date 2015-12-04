<?php
class systrade_mdl_trade extends dbeav_model{

    var $defaultOrder = array('created_time','DESC');
    public $has_many = array(
        'order' => 'order:contrast',
    );

    public function _filter($filter,$tableAlias=null,$baseWhere=null)
    {
        if( is_array($filter) &&  $filter['shop_name'] )
        {
            $objMdlShop = app::get('sysshop')->model('shop');
            $adata = $objMdlShop->getList('shop_id',array('shop_name|has'=>$filter['shop_name']));
            if($adata)
            {
                foreach($adata as $key=>$value)
                {
                    $shop[$key] = $value['shop_id'];
                }
                $filter['shop_id'] = $shop;
            }
            else
            {
                $filter['shop_id'] = "-1";
            }
            unset($filter['shop_name']);
        }

        if( is_array($filter) && $filter['login_account'] )
        {
            $aData = app::get('sysuser')->model('account')->getList('user_id',array('login_account'=>$filter['login_account']) );
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
            unset($filter['login_account']);
        }
        $filter = parent::_filter($filter,$tableAlias,$baseWhere);
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

        $columns = array_merge($columns, array(
            'shop_name'=>app::get('systrade')->_('所属商家'),
            'login_account'=>app::get('systrade')->_('用户名'),
        ));

        return $columns;
    }

}
