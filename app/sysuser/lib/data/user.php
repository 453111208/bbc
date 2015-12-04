<?php
class sysuser_data_user{
    public function deleteUser($userIds,&$msg="")
    {
        $objCheck = kernel::single('sysuser_check');
        $objMdlUser = app::get('sysuser')->model('user');
        $objMdlPamUser = app::get('sysuser')->model('account');

        try
        {
            $result = $objCheck->checkDelete($userIds);
            $result = $objMdlUser->delete(array('user_id'=>$userIds));
            if(!$result)
            {
                $msg = "删除会员基本信息失败";
                return false;
            }
            $result = $objMdlPamUser->delete(array('user_id'=>$userIds));
            if(!$result)
            {
                $msg = "删除会员登录信息失败";
                return false;
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return false;
        }
        return true;
    }
}
