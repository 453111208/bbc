<?php
class system_messenger_sms implements system_interface_messenger_send{

     /**
     * send
     * 必有方法,发送时调用
     * config参数为getOptions取得的所有项的配置结果
     * @param string $to
     * @param string $content
     * @param array $config
     * @access public
     * @return void
     */
    public function send($to,$title,$content,$config)
    {
        if(!$to) throw new \LogicException('短信发送失败：手机号为空！');
        if(!$content) throw new \LogicException('短信发送失败：短信内容为空！');
        $setSmsSign = app::get('system')->getConf('setSmsSign');

        if(is_array($setSmsSign))
        {
            $setSmsSign = $setSmsSign['sign'];
        }

        //当在shopex内网开发时，配置测试内容时使用
        if($testConf = config::get('messenger.intranet'))
        {
            $setSmsSign = $testConf['sign'];
            $config = array_merge($config,$testConf);
        }

        if(!$setSmsSign) throw new \LogicException('短信签名不能为空！');
        if(is_array($to))
        {
            $to = implode(',',$to);
        }

        $setSms=$this->_checkReg($setSmsSign);
        $content = $content.'【'. $setSmsSign .'】';
        $contents = array(
            0 => array(
                'phones' => $to,
                'content' => $content,
            ),
        );

        try{
            $result = kernel::single('system_messenger_smschg')->send($contents,$config);
        }catch(Exception $e){
            $msg = $e->getMessage();
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }

    /**
     * @brief 检测短信签名是否合法[全角]
     *
     * @param string $params
     *
     * @return string
     */
    private function _checkReg($params)
    {
        $arr = array(
            '【', '】',
        );
        if ((strstr($params, $arr[0]) && (strstr($params, $arr[1]))) != false)
        {
            throw new \LogicException('签名中含有非法字符');
        }
        return $params;
    }

    /**
     * @brief 发送短信后返回值匹配
     *
     * @param $index
     *
     * @return
     */
    private function _msg($index)
    {
        $aMsg=array(
            '200'=>'true',
            '1'=>'Security check can not pass!',
            '2'=>'Phone number format is not correct.',
            '3'=>'Lack of content or content coding error.',
            '4'=>'Lack of balance.',
            '5'=>'Information packets over limited.',
            '6'=>'You must recharge before write content!',
            '901'=>'Write sms_log error!',
            '902'=>'Write sms_API error!'
            );
        return $aMsg[$index];
    }
}


