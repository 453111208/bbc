<?php

class systrade_mdl_cart extends dbeav_model {

    public $defaultOrder = array('created_time',' DESC');

    public function getList( $cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null )
    {
        $user_id = $filter['user_id'];
        unset($filter['user_id']);
        if( !$filter['user_ident'] ) $filter['user_ident'] = $this->getUserIdentMd5($user_id);

        return parent::getList( $cols, $filter, $offset, $limit, $orderType );
    }

    /**
     * @brief 生成唯一的用户标识
     *
     * @return 返回md5的值
     */
    public function getUserIdentMd5($userId=null)
    {
        pamAccount::setAuthType('sysuser');
        $userId = pamAccount::getAccountId();
        if( $userId )
        {
            return md5($userId);
        }
        else
        {
            return $this->getSessionUserIdent();
        }
    }

    public function getSessionUserIdent()
    {
        $str = kernel::single('base_session')->sess_id();
        return md5($str);
    }

}

