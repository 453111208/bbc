<?php
class sysshop_mdl_enterapply extends dbeav_model{
    public function doDelete($filter)
    {
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $params = array(
            'enterapply_id' => $filter,
            'status|in' => array('successful','finish'),
        );
        $enterapply = $objMdlEnterapply->getList("enterapply_id",$params);

        if($enterapply)
        {
            $msg = "入驻申请已经通过审核，不可删除!";
            throw new \logicException($msg);
            return false;
        }

        $result = $objMdlEnterapply->delete(array('enterapply_id'=>$filter));
        if(!$result)
        {
            $msg = "入驻申请信息删除失败!";
            throw new \logicException($msg);
            return false;
        }
        return true;
    }

}
