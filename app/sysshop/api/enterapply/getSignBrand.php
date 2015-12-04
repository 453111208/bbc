<?php
class sysshop_api_enterapply_getSignBrand{
    public $apiDescription = "获取该品牌签约的店铺";
    public function getParams()
    {
        $return['params'] = array(
            //'shop_name' => ['type'=>'int','valid'=>'required','description'=>'申请店铺名称','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getSignBrand($params)
    {
        try{
            $objSysshopEnterapply = kernel::single('sysshop_data_enterapply');
            $objSysshopEnterapply->checkBrand($params);
        }
        catch(\LogicException $e)
        {
            throw new LogicException($e->getMessage());
        }
        return true;
    }
}
