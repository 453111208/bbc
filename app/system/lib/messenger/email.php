<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class system_messenger_email implements system_interface_messenger_send{

    /**
     * send
     * 必有方法,发送时调用
     *
     * config参数为getOptions取得的所有项的配置结果
     *
     * @param mixed $to
     * @param mixed $message
     * @param mixed $config
     * @access public
     * @return void
     */
    public function send($to, $subject, $body, $config)
    {
        if(!$to)
        {
            throw new \LogicException("邮箱地址为空！");
            return false;
        }
        if(!$body)
        {
            throw new \LogicException("邮件内容为空！");
            return false;
        }

        $objDesktopEmail = kernel::single('desktop_email_email');
        $emailconf = $this->_getConfig();
        $config = array_merge($config,$emailconf);
        $config['sendway']=($config['sendway']) ? $config['sendway'] : 'smtp';
        $msg = app::get('system')->_("无法发送邮件，下面是出错信息：");
        if(!$config['usermail'] && !$config['smtpport'] && !$config['smtpserver'])
        {
            throw new \LogicException("邮件发件信息未配置，请查看配置项！");
            return false;
        }
        if($objDesktopEmail->ready($config))
        {
            $result = $objDesktopEmail->send($to,$subject, $body,$config);
            if ($err = $objDesktopEmail->errorinfo)
            {
                $msg .= "<br>".$err['error'];
                throw new \LogicException($msg);
                return false;
            }
        }
        else
        {
            $msg .= "<br>".var_export($objDesktopEmail->smtp->error,true);
            throw new \LogicException($msg);
            return false;
        }
        return true;
    }

    private function _getConfig()
    {
        $objEmailconf = kernel::single('desktop_email_emailconf');
        return $objEmailconf->get_emailConfig();
    }
}


