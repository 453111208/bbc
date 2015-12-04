<?php
class sysitem_api_item_delete{
    public $apiDescription = "商品删除";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required|int','description'=>'商品id，多个id用，隔开','example'=>'2,3,5,6','default'=>''],
            'shop_id' => ['type'=>'int','valid'=>'required|int','description'=>'店铺id','example'=>'','default'=>''],
        );
        return $return;
    }
    public function itemDelete($params)
    {
        try
        {
              $ojbMdlItem = app::get('sysitem')->model('item');
              $result = $ojbMdlItem->doDelete($params);
              if(!$result)
              {
                  throw new Exception('商品删除失败');
              }
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
        return true;
    }
}
