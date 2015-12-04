<?php
class topm_ctl_category extends topm_controller{


    public function __construct()
    {
        parent::__construct();
        $this->setLayoutFlag('topics');
    }

    //一级类目页
    public function index()
    {
        $catList = app::get('topm')->rpcCall('category.cat.get.list',array('fields'=>'cat_id,cat_name'));
        $pagedata['data'] = $catList;
        $pagedata['title'] = "商品分类";
        return $this->page('topm/category/category.html',$pagedata);
    }

    //二三级类目页
    public function catList()
    {
        $catId = input::get('cat_id');
        $catInfo = app::get('topm')->rpcCall('category.cat.get',array('cat_id'=>$catId,'fields'=>'cat_id,cat_name'));
        $pagedata['data'] = $catInfo[$catId];
        $pagedata['title'] = "商品分类";
        return $this->page('topm/category/catlistinfo.html',$pagedata);
    }
}
