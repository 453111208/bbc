<?php
class sysuser_api_user_verifyEmail{
    public $apiDescription = "验证邮箱";

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID,必填'],
            'email' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要验证的邮箱,必填'],
        );
        return $return;
    }

    public function verifyEmail($params)
    {
        if($params['oauth']['account_id'] && $params['user_id'] == $params['oauth']['account_id'])
        {
            $objMdlAccount = app::get('sysuser')->model('account');
            $objMdlUser = app::get('sysuser')->model('user');

            $user = $objMdlUser->getRow('email_verify',array('user_id'=>$params['user_id']));
            $account = $objMdlAccount->getRow('email,login_account',array('user_id'=>$params['user_id']));
            if(!$user['email_verify'] && !$account['login_account'] && $account['email'] != $params['email'])
            {
                throw new \LogicException('您没有设置用户名，邮箱未绑定时不可更改');
            }

            $db = app::get('sysuser')->database();
            $transaction_status = $db->beginTransaction();

            try
            {
                if($account['email'] == $params['email'])
                {
                    $data = array(
                        'user_id' =>$params['user_id'],
                        'email_verify' =>1,
                    );
                    $result = $objMdlUser->update($data,array('user_id'=>$params['user_id']));
                    if(!$result)
                    {
                        throw new \LogicException('邮箱绑定失败');
                    }
                }
                else
                {
                    $params['user_name'] = $params['email'];
                    unset($params['email']);

                    $result = kernel::single('sysuser_passport')->setAccount($params);
                    if(!$result)
                    {
                        throw new \LogicException('新邮箱地址保存失败');
                    }

                    $data = array(
                        'user_id' =>$params['user_id'],
                        'email_verify' =>1,
                    );
                    $result = $objMdlUser->update($data,array('user_id'=>$params['user_id']));
                    if(!$result)
                    {
                        throw new \LogicException('邮箱绑定失败');
                    }
                }

                $db->commit($transaction_status);
            }
            catch(LogicException $e)
            {
                $db->rollback();
                throw $e;
            }
        }
        else
        {
            throw new \LogicException('请登录，之后验证邮箱');
        }

        return true;
    }
}
