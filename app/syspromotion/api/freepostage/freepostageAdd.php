<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 添加免邮数据
 */
final class syspromotion_api_freepostage_freepostageAdd {

    public $apiDescription = '添加免邮数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'description'=>'会员ID,user_id和shop_id必填一个', 'default'=>'', 'example'=>''],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个', 'default'=>'', 'example'=>''],
            'freepostage_id' => ['type'=>'int', 'valid'=>'', 'description'=>'免邮id', 'default'=>'', 'example'=>''],
            'freepostage_name' => ['type'=>'string', 'valid'=>'', 'description'=>'免邮名称', 'default'=>'', 'example'=>''],
            'freepostage_status' => ['type'=>'int', 'valid'=>'', 'description'=>'免邮状态', 'default'=>'', 'example'=>''],
            'condition_type' => ['type'=>'string', 'valid'=>'in:money,quantity', 'default'=>'', 'example'=>'money或者quantity', 'description'=>'免邮规则类型'],
            'limit_money' => ['type'=>'string', 'valid'=>'required_if:condition_type,money', 'description'=>'按金额', 'default'=>'', 'example'=>''],
            'limit_quantity' => ['type'=>'string', 'valid'=>'required_if:condition_type,quantity|integer', 'description'=>'按数量', 'default'=>'', 'example'=>''],
            'page_no' => ['type'=>'int', 'valid'=>'', 'description'=>'分页当前页数,默认为1', 'default'=>'', 'example'=>''],
            'page_size' => ['type'=>'int', 'valid'=>'', 'description'=>'每页数据条数,默认10条', 'default'=>'', 'example'=>''],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'description'=>'排序，默认created_time asc', 'default'=>'', 'example'=>''],
        );

        return $return;
    }

    /**
     *  添加免邮数据
     * @param  array $apiData 免邮各种值
     * @return array         返回一条免邮信息
     */
    public function freepostageAdd($apiData)
    {
        return kernel::single('syspromotion_freepostage')->saveFreepostage($apiData);
    }

}

