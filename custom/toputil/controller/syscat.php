<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class toputil_ctl_syscat extends topshop_controller {

    /**
     * 根据父类id获取子类列表
     * @return json
     */
    public function getChildrenCatList()
    {
        $a=input::get();
        $catId = intval(input::get('cat_id'));
        $shopid= intval(input::get('shopid'));
        if($catId)
        {
            $catPralist=app::get("syscategory")->model('cat')->getRow("*", array("cat_id" => $catId));
            if($catPralist["cat_name"]=="其他"){
                $catList =app::get("syscategory")->model('cat')->getList("*", array("parent_id" => $catId,"belong"=>$shopid));
                foreach($catList as $key=>$value) {
                $newList[$key] = array(
                    'value' => $value['cat_id'],
                    'text' => $value['cat_name'],
                    'hasChild' => ($value['child_count'] >0) ? true : false,
                );
            }
        }
            else{
            $catList = app::get('toputil')->rpcCall('category.cat.get.info',array('parent_id'=>$catId,'fields'=>'cat_id,cat_name,child_count'));
            foreach($catList as $key=>$value) {
                $newList[$key] = array(
                    'value' => $value['cat_id'],
                    'text' => $value['cat_name'],
                    'hasChild' => ($value['child_count'] >0) ? true : false,
                );
            }
        }
            $data['data']['options'] = $newList;
        }
        else
        {
            $data=array();
        }
        return response::json($data);
    }
}
