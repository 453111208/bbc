<?php

class image_api_shop_list {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取当前店铺的图片列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'img_type' => ['type'=>'string','valid'=>'required','description'=>'图片类型','example'=>'item','default'=>''],
            'image_name' => ['type'=>'string','valid'=>'','description'=>'图片名称','example'=>'xxx','default'=>''],

			//分页参数
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],

            'orderBy' => ['type'=>'string','valid'=>'','description'=>'last_modified desc','example'=>'last_modified desc,image_name asc','default'=>'last_modified desc'],
        );

        return $return;
    }

	/*
	* 分页处理
	*
 	*@param $total int 根据条件查询的总数
	*@param $pageNo int 当前分页的页数
	*@param $pageSIze int 当前分页的页码
 	*/
	private function __page($total, $pageNo, $pageSize)
	{
		$pageTotal = ceil($total/$pageSize);
		$pageNo =  $pageNo ? $pageNo : 1;
		$data['limit'] = $pageSize ? $pageSize : 10;
		$currentPage = $pageTotal < $pageNo ? $pageTotal : $pageNo;
		$data['offset'] = ($currentPage-1) * $data['limit'];

		return $data;
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

    public function get($params)
    {
    	$shopId = $this->__checkAuth($params);

		$filter['disabled'] = 0;
		$filter['target_id'] = $shopId;
		$filter['target_type'] = 'shop';

		if( $params['img_type'] != 'all' )
		{
			$filter['img_type'] = $params['img_type'] != 'other' ? $params['img_type'] : '' ;
		}

        if( $params['image_name'] )
		{
			$filter['image_name|has'] = $params['image_name'];
		}

		$total = app::get('image')->model('images')->count($filter);
        $result['total'] = $total;
        if( $total )
        {
            $page = $this->__page($total, $params['page_no'], $params['page_size']);
            $orderBy = $params['orderBy'] ? $params['orderBy'] : 'last_modified desc';

            $result['list'] = app::get('image')->model('images')->getList('*', $filter, $page['offset'], $page['limit'], $orderBy);
            foreach( $result['list']  as $k=>$v )
            {
                $result['list'][$k]['format_size'] = format_filesize($v['size']);
            }
        }
        else
        {
            $result['list'] = [];
        }

		return $result;
	}
}
