<?php
class sysshop_api_enterapply_create{
    public $apiDescription = "商家入驻申请";
    public function getParams()
    {
        $return['params'] = array(
//            'seller_id' => ['type'=>'int','valid'=>'required','description'=>'入驻申请编号','default'=>'','example'=>''],
//            'shop_name' => ['type'=>'int','valid'=>'required','description'=>'申请店铺名称','default'=>'','example'=>''],
//            'shop_type' => ['type'=>'int','valid'=>'required','description'=>'申请店铺类型{flag,brand,cat}','default'=>'','example'=>''],
//            'shopuser_name' => ['type'=>'int','valid'=>'required','description'=>'申请店铺店主名称','default'=>'','example'=>''],
//            'company_name' => ['type'=>'int','valid'=>'required','description'=>'申请店铺公司名称','default'=>'','example'=>''],
//            'shop' => ['type'=>'json','valid'=>'required','description'=>'店铺基本信息集合','default'=>'','example'=>''],
//            'shop_info' => ['type'=>'json','valid'=>'required','description'=>'店铺详细信息集合','default'=>'','example'=>''],
        );
        return $return;
    }
    public function create($params)
    {
        try
        {
            $objShopType = kernel::single('sysshop_data_enterapply');
            $result = $objShopType->savedata($params);
            if(!$result)
            {
                throw new LogicException('入驻申请保存失败');
            }
        }
        catch(\LogicException $e)
        {
            throw new LogicException($e->getMessage());
        }
        return true;
    }
}
