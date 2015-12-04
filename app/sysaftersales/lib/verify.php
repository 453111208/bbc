<?php
/**
 * 售后服务的验证类
 */
class sysaftersales_verify {

    public function __construct()
    {
        $this->objMdlAftersales = app::get('sysaftersales')->model('aftersales');
    }


    /**
     * 判断子订单编号是否已申请过售后，或者申请的售后是否已经驳回，需要重新申请
     *
     * @param array $oids 子订单编号
     * @return string APPLY_CLOSED 申请并已驳回|IS_APPLY 售后已申请，正在处理或已处理|NOT_APPLY 未进行过售后申请
     */
    public function isAftersales($oids)
    {
        $aftersalesList = $this->objMdlAftersales->getList('aftersales_bn,status,oid', array('oid'=>$oids));

        $verifyData = array();
        $useOids = array();
        foreach( $aftersalesList as $row )
        {
            $oid = $row['oid'];
            if( $row['status'] == '3' && !in_array($oid, $useOids) )
            {
                $verifyData[$oid] = 'APPLY_CLOSED';
            }
            else
            {
                $verifyData[$oid] = 'IS_APPLY';
            }
        }

        foreach( $oids as $oid )
        {
            if( !isset($verifyData[$oid]) )
            {
                $verifyData[$oid] = 'NOT_APPLY';
            }
        }

        return $verifyData;
    }

    /**
     * 验证操作对象对该售后数据是否有操作权限
     *
     * @param array $aftersalesInfo 需要操作售后的售后详细数据
     * @param string $from  操作用户类型 buyer 消费者，seller 商家
     *
     * @return bool
     */
    public function checkPermission($aftersalesInfo, $from='seller',$id)
    {
        if( !$aftersalesInfo )
        {
            throw new \LogicException(app::get('sysaftersales')->_('审核的售后编号不存在'));
        }

        switch( $from )
        {
            case 'seller':
                if( !$id || $aftersalesInfo['shop_id'] != $id )
                {
                    throw new \LogicException(app::get('sysaftersales')->_('无操作权限,可能已退出登录，请重新登录'));
                }
                break;
            case 'buyer':
                if( !$id || $aftersalesInfo['user_id'] != $id )
                {
                    throw new \LogicException(app::get('sysaftersales')->_('无操作权限,可能已退出登录，请重新登录'));
                }
                break;
        }

        return true;
    }
}
