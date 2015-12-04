<?php
/**
 *
 */
class syslogistics_data_dlycorp {

    public $objMdldlycorp = null;

    public function __construct()
    {
        $this->objMdldlycorp = app::get('syslogistics')->model('dlycorp');
    }


    /**
     * @brief 根据条件查询，获取到物流公司信息
     *
     * @param string $columns 需要获取物流信息字段
     * @param array  $filter  查询条件
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function fetchDlycorp($columns="*", $filter, $offset=0, $limit=-1)
    {
        $data['total_found'] = $this->objMdldlycorp->count($filter);
        $data['data'] = $this->objMdldlycorp->getList($columns,$filter,$offset,$limit);
        return $data;
    }


    public function getDlycorp()
    {
        $corpData = "";
        if (!file_exists(APP_DIR.'/syslogistics/hqbdlycorp.txt')) return $corpData;

        foreach (file(APP_DIR.'/syslogistics/hqbdlycorp.txt') as $row)
        {
            list($key,$value,$name) = explode("\t",trim($row));
            $corpData[$key] = $value." ( ".$name." )";
        }
        return $corpData;
    }

    public function deleteDlycorp($filter,&$msg)
    {
        $objMdlDlytmpl = app::get('syslogistics')->model('dlytmpl');
        $dlytmpl =  $objMdlDlytmpl->getList('template_id,corp_id',array('corp_id'=>$filter));
        if($dlytmpl)
        {
            $msg = app::get('syslogistics')->_('快递公司被快递模板绑定，不可删除');
            return false;
        }
        $result = $this->objMdldlycorp->delete(array('corp_id'=>$filter));
        if(!$result)
        {
            $msg = app::get('syslogistics')->_('快递公司删除失败');
            return false;
        }
        return true;
    }
}



