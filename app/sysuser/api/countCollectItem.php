<?php
class sysuser_api_countCollectItem{
    public $apiDescription = "获取会员商品收藏总数";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'cat_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品3级分类','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getCount($params)
    {
        $filter['user_id'] = $params['user_id'];
        if($params['cat_id'])
        {
            $filter['cat_id'] = $params['cat_id'];
        }
        $objMdlFav = app::get('sysuser')->model('user_fav');
        $result = $objMdlFav->count($filter);
        return $result;
    }
}
