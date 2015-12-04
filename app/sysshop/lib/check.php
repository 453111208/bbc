<?php
class sysshop_check{
    public function checkDelete($sellerIds)
    {
        $filter['seller_id'] = $sellerIds;
        $shopCheck = $this->shopCheck($filter);
        if($shopCheck)
        {
            throw new \LogicException(app::get('sysshop')->_('该商家有存活的店铺'));
            return false;
        }
        return true;
    }

    public function shopCheck($filter)
    {
        $params['rows'] = "shop_id,seller_id,shop_name";
        $params['filter'] = $filter;
        $objShopRel = kernel::single('sysshop_data_seller');
        $shops = $objShopRel->getRelShop($params);
        $result = "";
        if($shops){
            foreach($shops as $value)
            {
                $result[$value['shop_id']]= $value['shop_name'];
            }
        }
        return $result;
    }

}
