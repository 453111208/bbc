<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syslogistics_mdl_dlycorp extends dbeav_model {

    public $defaultOrder = array('order_sort',' ASC');

    public function doDelete($filter)
    {
        $objMdlDlytmpl = app::get('syslogistics')->model('dlytmpl');
        $objMdldlycorp = app::get('syslogistics')->model('dlycorp');
        $dlytmpl =  $objMdlDlytmpl->getList('template_id,corp_id',array('corp_id'=>$filter));
        if($dlytmpl)
        {
            $msg = app::get('syslogistics')->_('快递公司被快递模板绑定，不可删除');
            throw new \logicException($msg);
            return false;
        }
        $result = $objMdldlycorp->delete(array('corp_id'=>$filter));
        if(!$result)
        {
            $msg = app::get('syslogistics')->_('快递公司删除失败');
            throw new \logicException($msg);
            return false;
        }
        return true;
    }

}
