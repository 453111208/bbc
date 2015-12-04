<?php

class image_api_shop_upImageName {

    /**
     * 接口作用说明
     */
    public $apiDescription = '根据图片URL,修改图片名称';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'url' => ['type'=>'string','valid'=>'required','description'=>'图片URL','example'=>'','default'=>''],
            'image_name' => ['type'=>'string','valid'=>'required','description'=>'图片名称','example'=>'xxx','default'=>''],
        );

        return $return;
    }

	private function __checkAuth($params)
	{
		if($params['oauth']['auth_type'] == "shop")
		{
			$shopId = app::get('image')->rpcCall('shop.get.loginId',array('seller_id'=>$params['oauth']['account_id']),'seller');
		}
		else
		{
			throw new \LogicException('登录已过期，请重新登录');
        }

		return $shopId;
	}

    public function up($params)
    {
        $params = utils::_filter_input($params);
    	$shopId = $this->__checkAuth($params);

		$filter['disabled'] = 0;
		$filter['target_id'] = $shopId;
		$filter['target_type'] = 'shop';
		$filter['url'] = $params['url'];

		$resultData = app::get('image')->model('images')->update(['image_name'=>$params['image_name']], $filter);

		return $resultData;
	}
}
