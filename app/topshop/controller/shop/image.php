<?php

class topshop_ctl_shop_image extends topshop_controller {

    public $limit = 30;

    public function index()
    {
		$result = $this->__getListData();
        $pagedata['imagedata'] = $result['data']['list'];
        $pagedata['pagers'] = $result['pagers'];
        return $this->page('topshop/shop/image/index.html', $pagedata);
    }

    private function __pager($filter, $count, $isImageModal)
    {
        $params['img_type'] = $filter['img_type'];
        $params['orderBy'] = $filter['orderBy'];
        if( $filter['image_name'] )
		{
			$params['image_name'] = $filter['image_name'];
		}

        if( $isImageModal )
        {
            $params['imageModal'] = true;
        }

        $params['pages'] = time();
        $total = ceil($count/$this->limit);
        $pagers = array(
            'link'=>url::action('topshop_ctl_shop_image@search',$params),
            'current'=>$filter['page_no'],
            'use_app' => 'topshop',
            'total'=>$total,
            'token'=>time(),
        );
        return $pagers;
    }

	private function __getListData($isImageModal)
	{
        if( $isImageModal )
        {
            $this->limit = 12;
        }

        $params['page_no'] = input::get('pages',1);
        $params['page_size'] = $this->limit;
		$params['img_type'] = input::get('img_type','item');
		$params['orderBy'] = input::get('orderBy','last_modified desc');
		if( input::get('image_name') )
		{
			$params['image_name'] = input::get('image_name');
		}
        $result['data'] = app::get('topshop')->rpcCall('image.shop.list', $params, 'seller');

        $result['pagers'] = $this->__pager($params, $result['data']['total'], $isImageModal);

		return $result;
	}

	public function search()
	{
        if( input::get('imageModal',false) )
        {
            $isImageModal = true;
            $pagedata['imageModal'] = true;
        }
		$result = $this->__getListData($isImageModal);
        $pagedata['imagedata'] = $result['data']['list'];
        $pagedata['pagers'] = $result['pagers'];
        $pagedata['image_name'] = input::get('image_name',false);
        return view::make('topshop/shop/image/list.html', $pagedata);
	}

	public function delImgLink()
	{
        $params['image_id'] = implode(',', input::get('img_id') );
		try
		{
        	app::get('topshop')->rpcCall('image.delete.imageLink', $params, 'seller');
            $status = 'success';
            $msg = '删除成功';
		}
		catch( Exception $e)
		{
			$msg = $e->getMessage();
            $status = 'error';
		}

        return $this->splash($status,null,$msg,true);
	}

	public function upImgName()
	{
        $params['url'] = input::get('url');
        $params['image_name'] = input::get('image_name');
        try
        {
            $status = 'success';
            app::get('topshop')->rpcCall('image.shop.upImageName', $params, 'seller');
        }
        catch( LogicExpection $e )
        {
            $mag = $e->getMessage();
            $status = 'error';
        }

        $msg = '更新成功';
        return $this->splash($status,null,$msg,true);
	}

    public function loadImageModal()
    {
        $isImageModal = true;
        $result = $this->__getListData($isImageModal);
        $pagedata['imagedata'] = $result['data']['list'];
        $pagedata['pagers'] = $result['pagers'];
        $pagedata['imageModal'] = true;
        return view::make('topshop/shop/image/upload.html', $pagedata);
    }
}
