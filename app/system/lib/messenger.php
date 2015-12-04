<?php
class system_messenger {

    /**
     * @brief 队列发短信和邮件
     *
     * @param $sendTo 发送对象
     * @param $tmpl 邮件或短信模板
     * @param $content 发送内容
     *
     * @return
     */
    static public function send($sendTo,$tmpl,$content)
    {
        $objMessenger = kernel::single('system_data_messenger');
        $senders = $objMessenger->getSenders($tmpl);
        $actions = config::get('messenger.actions');
        $sendType = $actions[$tmpl]['sendType'];

        if($actions[$tmpl] && $senders )
        {
            foreach($senders as $sender)
            {
                switch($sender)
                {
                case 'email':
                    $result = self::insertEmailQueue($sendTo['email'],$tmpl,$content);
                    break;
                case 'sms':
                    $result = self::insertSmsQueue($sendTo['sms'],$tmpl,$content);
                    break;
                default:
                    $result = false;
                    break;
                }
            }
            return $result;
        }
        return true;
    }

    /**
     * @brief 直发短信
     *
     * @param $sendTo 手机号
     * @param $tmpl 短信模板
     * @param $content 短信内容
     *
     * @return
     */
    static public function sendSms($sendTo,$tmpl,$content)
    {
        $result = self::sendMessenger('sms',$sendTo,$tmpl,$content);
        return $result;
    }

    /**
     * @brief 直发短信
     *
     * @param $sendTo 手机号
     * @param $tmpl 短信模板
     * @param $content 短信内容
     *
     * @return
     */
    static public function sendEmail($sendTo,$tmpl,$content)
    {
        $result = self::sendMessenger('email',$sendTo,$tmpl,$content);
        return $result;
    }

    /**
     * @brief 发送 email、sms等function
     *
     * @param string $type (email/sms)等
     * @param string $tmpl 模板名称
     * @param array $content=array('sms/email'=>'','content'=>array('模板内的内容'),'config'=>'基础配置')
     * @param $msg
     *
     * @return
     */
    static public function sendMessenger($type,$sendTo,$tmpl,$content)
    {
        $msg = $type." sending failed";
        $objMessenger = kernel::single('system_data_messenger');
        $objMdlsystmpl = app::get('system')->model('messenger_systmpl');

        $mess = config::get('messenger.messenger');
        $method = $mess[$type]['class'];

        $actions = config::get('messenger.actions');

        if($actions[$tmpl]['sendType']) $config['sendType'] = $actions[$tmpl]['sendType'];
        if($actions[$tmpl]['use_reply']) $config['use_reply'] = $actions[$tmpl]['use_reply'];

        if($content['shopname']) $config['shopname'] = $content['shopname'];

        $tmpl_name = 'messenger:'.$method.'/'.$tmpl;

        try{
            $contents = $objMdlsystmpl->fetch($tmpl_name,$content);
            $title = $objMessenger->loadTitle($tmpl,$type,$content);
            $sendResult = kernel::single($method)->send($sendTo,$title,$contents,$config);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return array(
                'rsp' => "fail",
                'err_msg' => $msg,
            );
        }

        return array(
            'rsp' => "succ",
            'err_msg' => "",
        );
    }

    static public function insertEmailQueue($sendTo,$tmpl,$content)
    {
        if(!is_array($sendTo))
        {
            $sendTo = explode(",",$sendTo);
        }
        foreach($sendTo as $value){
            $queue_content = array(
                'email' =>$value,
                'tmpl' => $tmpl,
                'content' => $content,
            );
            $result[$value] = system_queue::instance()->publish('system_tasks_sendemail', 'system_tasks_sendemail', $queue_content);
        }
        $mail = "";
        foreach($result as $key => $val)
        {
            if(!$val) $mail .= $key;
        }
        return array(
            'rsp' => $mail?"fail":"succ",
            'err_msg'=>$val?"加入邮件发送队列失败".$mail:"",
        );
    }

    static public function insertSmsQueue($sendTo,$tmpl,$content)
    {
        $queue_content = array(
            'sms' => $sendTo,
            'tmpl' => $tmpl,
            'content' => $content,
        );
        $result = system_queue::instance()->publish('system_tasks_sendsms', 'system_tasks_sendesms', $queue_content);
        return array(
            'rsp' => $result?"succ":"fail",
            'err_msg'=>$result?"":"加入短信发送队列失败",
        );
    }

    static private function _send($type,$tmpl,$content,$sendType,$isqueue)
    {
        if($isqueue)
        {
            $mess = config::get('messenger.messenger');
            $method = $mess[$type]['class'];
            $tmpl_name = 'messenger:'.$method.'/'.$tmpl;
            $queue_content = array(
                'sendMethod' => $type,
                'tmpl_name' => $tmpl_name,
                'data' => $content,
                'type' => $tmpl,
                'sendType' => $sendType ?  $sendType : 'notice'
            );
            return system_queue::instance()->publish('system_tasks_sendmessenger', 'system_tasks_sendmessenger', $queue_content);
        }
        else
        {
            $result = self::sendMessenger($type,$tmpl,$content);
            if($result['rsp'] == "succ")
            {
                return true;
            }
            elseif($result['rsp'] == "fail")
            {
                return false;
            }
        }
    }
}
