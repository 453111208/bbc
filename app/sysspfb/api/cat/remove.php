<?php
class syscategory_api_cat_remove{

    public $apiDescription = "类目删除";
    public function getParams()
    {
        $return['params'] = array(
            'cat_id' => ['type'=>'int','valid'=>'required', 'description'=>'类目id'],
        );
        return $return;
    }
    public function toRemove($params)
    {
        $objCat = kernel::single('syscategory_data_cat');
        try
        {
            $result = $objCat->toRemove($params['cat_id']);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException($e->getMessage());
            return false;
        }
        return true;
    }
}
