<?php
class sysuser_api_user_basicsUpdate{

    public $apiDescription = "用户基本信息更新";
    public function getParams()
    {
        $return['params'] = array();
        return $return;
    }
    public function update($params)
    {
        try{
            $data = json_decode($params['data'],true);
            $userId = $params['user_id'];
            $objLibUserUser =  kernel::single('sysuser_data_user_user');
            $saveResult = $objLibUserUser->saveInfoSet($data,$userId);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException($e->getMessage());
        }
        return true;
    }
}
