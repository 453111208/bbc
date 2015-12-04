<?php

class image_api_deleteImage {

    /**
     * 接口作用说明
     */
    public $apiDescription = '数据库中删除图片链接，但是不删除真实图片文件';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'image_id' => ['type'=>'string','valid'=>'required','description'=>'图片ID, 多个图片则用逗号隔开','example'=>'1,2,3','default'=>''],
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

    public function delete($params)
    {
    	$shopId = $this->__checkAuth($params);

		$filter['target_id'] = $shopId;
		$filter['target_type'] = 'shop';
		$filter['id'] = explode(',',$params['image_id']);

		$resultData = app::get('image')->model('images')->update(['disabled'=>1], $filter);

		return $resultData;
	}
}
