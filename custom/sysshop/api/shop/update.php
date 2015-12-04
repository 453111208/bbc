<?php
class sysshop_api_shop_update{

    public $apiDescription = "更新企业基本信息[暂时只修改企业logo和企业描述]";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'string','valid'=>'required','description'=>'企业id串','default'=>'','example'=>'1,3,5'],
            'shop_logo' => ['type'=>'string','valid'=>'','description'=>'企业logo','default'=>'','example'=>'http://img0.cn/aa.jpg'],
            'shop_descript' => ['type'=>'string','valid'=>'','description'=>'企业基本描述','default'=>'','example'=>"李维斯（Levi's）是著名的牛仔裤品牌"],
        );
        return $return;
    }
    public function update($params)
    {
        $objMdlShop = app::get('sysshop')->model('shop');
        $result = $objMdlShop->save($params);
        return $result;
    }
}
