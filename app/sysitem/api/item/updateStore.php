<?php
class sysitem_api_item_updateStore{
    public $apiDescription = "回写库存";
    public function getParams()
    {
        $return['params'] = array(
          //'item_bn' => ['type'=>'string','valid'=>'required','description'=>'商品编号','example'=>'S558FBDE4EE0E9','default'=>''],
          //'sku_bn' => ['type'=>'string','valid'=>'required','description'=>'货品编号','example'=>'2S558FBDE4EE0E9','default'=>''],
          //'quantity' => ['type'=>'int','valid'=>'required','description'=>'库存数量','example'=>'100','default'=>''],
            'list_quantity' => ['type'=>'string','valid'=>'required','description'=>'库存列表的json格式[{"bn"=>,"quantity"=>}](最多50条),bn为sku_bn，不是商品bn','example'=>'[{"bn":"S558FBDE4EE0E901","quantity":100},{"bn":"S558FBDE4EE0E902","quantity":100}]','default'=>''],
        );
        return $return;
    }

    public function updateStore($params, $oauth)
    {
        $shopId = $oauth['shop_id'];
        $listQuantity = json_decode($params['list_quantity'], 1);
        $this->__checkListQuantity($listQuantity);

        $db = app::get('sysuser')->database();
        $db->beginTransaction();

        try
        {
            foreach($listQuantity as $quantity)
            {
                $this->__checkQuantity($quantity);
                $skuBn = $quantity['bn'];
                $store = $quantity['quantity'];
                kernel::single('sysitem_item_store')->updateStoreByBn(null, $skuBn, $shopId, $store);
            }
            $db->commit();
        }
        catch(Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    private function __checkQuantity($quantity)
    {
        $quantityValidate = [
//          'item_bn' => 'required|max:45',
            'bn' => 'required|max:30',
            'quantity' => 'required|numeric',
            ];
        $validator = validator::make($quantity, $quantityValidate);
        if( $validator->fails() )
        {
            $errors = json_decode( $validator->messages(), 1 );
            foreach( $errors as $error )
            {
                throw new LogicException( $error[0] );
            }
        }
    }

    private function __checkListQuantity($listQuantity)
    {
        $countListQuantity = count($listQuantity);
        if($countListQuantity == 0)
        {
            throw new LogicException('list_quantity长度不能为0');
        }
        elseif($countListQuantity > 50 )
        {
            throw new LogicException('批量更新库存的货品数量不能多于50个');
        }
    }
}
