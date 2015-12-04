<?php
class sysshop_data_shoptype{

    public function __construct()
    {
        $this->objMdlShoptype = app::get('sysshop')->model('shop_type');
    }

    /**
     * @brief 获取所有类型值
     *
     * @return
     */
    public function shopType()
    {
        $rows = "shop_type,name,brief,suffix";
        $lists = $this->objMdlShoptype->getList($rows);
        foreach($lists as $key=>$value)
        {
            $shoptype[$value['shop_type']] = $value;
        }
        return $shoptype;
    }

    /**
     * @brief  获取一个类型的配置信息
     *
     * @return
     */
    public function getShoptype($rows,$filter="")
    {
        $lists = $this->objMdlShoptype->getRow($rows,$filter);
        return $lists;
    }


    public function saveShoptype($postdata)
    {
        $shoptypeMdl = app::get('sysshop')->model('shop_type');
        $postdata['shoptype_id'] = intval($postdata['shoptype_id']);
        if(!$postdata['max_item']){
            throw new \LogicException("默认商品上限必须大于0");
            return false;
        }
        $result = $shoptypeMdl->save($postdata);
        if(!$result)
        {
            throw new \LogicException("店铺类型保存失败");
            return false;
        }
        return true;
    }


}

