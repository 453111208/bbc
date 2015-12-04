<?php
class sysstat_api_getPageTime{

    public $apiDescription = "获取商家统计的现实时间";
    public function getParams()
    {
        //个字段注意格式
        $return['params'] = array(
            'inforType' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'传入的类型 一共有4种（trade,tradecount,item,itemcount）'],
            'timeType' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'传入的时间类型 一共有6种(yesterday,beforday,week,month,selecttime,select)'],
            'starttime' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'起始时间段。如：2015/05/15-2015/05/15'],
            'endtime' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'结束时间段。如：2015/05/03-2015/05/03'],
        );
        return $return;
    }
    public function getPageTime($params)
    {
        $statLibTrade = kernel::single('sysstat_data_shoptrade');

        $params['timefile'] = array('starttime'=>$params['starttime'],'endtime'=>$params['endtime']);
        $selecttime = $statLibTrade->__checkTime($params['timeType'],$params['timefile']);
        $pagetime = $statLibTrade->getPageTime($params['timeType'],$selecttime);
        return $pagetime;
    }
}
