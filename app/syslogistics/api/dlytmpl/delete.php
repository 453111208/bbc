<?php
class syslogistics_api_dlytmpl_delete{

    public $apiDescription = "运费模板更新";
    public function getParams()
    {
        $return['params'] = array(
            'template_id' =>['type'=>'string','valid'=>'required', 'description'=>'模板id','default'=>'','example'=>'1'],
            'shop_id' =>['type'=>'string','valid'=>'required', 'description'=>'店铺id','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function delete($params)
    {
        $filter['template_id'] = $params['template_id'];
        $filter['shop_id'] = $params['shop_id'];

        $objDataDlyTmpl = kernel::single('syslogistics_data_dlytmpl');
        $result = $objDataDlyTmpl->remove($filter);
        return $result;

    }
}


