<?php
class syscategory_api_brand_update {

    /**
     * 接口作用说明
     */
    public $apiDescription = '修改商品品牌';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'brand_id' => ['type'=>'int','valid'=>'required', 'description'=>'品牌id','default'=>'','example'=>''],
            'brand_name' => ['type'=>'string','valid'=>'required', 'description'=>'品牌名称','default'=>'','example'=>''],
            'order_sort' => ['type'=>'int','valid'=>'', 'description'=>'排序','default'=>'','example'=>''],
            'brand_alias' => ['type'=>'string','valid'=>'', 'description'=>'品牌别名','default'=>'','example'=>''],
            'brand_logo' => ['type'=>'string','valid'=>'', 'description'=>'品牌图片标识','default'=>'','example'=>''],
        );

        return $return;
    }

    public function updateBrand($apiData)
    {
        return kernel::single('syscategory_data_brand')->update($apiData);
    }
}
