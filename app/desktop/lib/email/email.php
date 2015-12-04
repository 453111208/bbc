<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_email_email{
    var $hasTitle = true; //是否有标题
    var $maxtime = 300; //发送超时时间 ,单位:秒
    var $maxbodylength =300; //最多字符
    var $allowMultiTarget=false; //是否允许多目标
    var $targetSplit = ',';
    var $Sendmail          = "/usr/sbin/sendmail";

     function ready($config){
        $this->smtp = kernel::single('desktop_email_smtp');
        if($config['sendway']=='smtp'){
            if(!$this->SmtpConnect($config)) return false;
        }
        return true;
    }
    /**
     * finish
     * 可选方法，结束发送时触发
     *
     * @param mixed $config
     * @access public
     * @return void
     */
    function finish($config){
        if($config['sendway']=='smtp'){
            $this->SmtpClose();
        }
    }

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
  function send($to, $subject, $body, $config){
        $this->Sender = $config['usermail'];
        $this->Subject = $this->inlineCode($subject);

        $header = array(
            'Return-path'=>'<'.$config['usermail'].'>',
            'Date'=>date('r'),
            'From'=>$this->inlineCode($config['shopname']).'<'.$config['usermail'].'>',
            #'From' =>'sss',
            'MIME-Version'=>'1.0',
            'Subject'=>$this->Subject,
            'To'=>$to,
            'Content-Type'=>'text/html; charset=UTF-8; format=flowed',
            'Content-Transfer-Encoding'=>'base64'
        );
        $config['sendway']=($config['sendway'])?$config['sendway']:'smtp';
        if($config['sendway'] == 'mail'){
            unset($header['Subject']);
            unset($header['To']);
        }
        $body = chunk_split(base64_encode($body));
        $header = $this->buildHeader($header);
        $result = false;
        if($config['sendway'] == 'smtp')
        {
            $result = $this->SmtpSend($to,$header, $body,$config);
        }
        return $result;
    }

    function inlineCode($str){
        $str = trim($str);
        return $str?'=?UTF-8?B?'.base64_encode($str).'?= ':'';
    }

    function buildHeader($headers){
        $ret = '';
        foreach($headers as $k=>$v){
            $ret.=$k.': '.$v."\n";
        }
        return $ret;
    }

    /**
     * Sends mail via SMTP using PhpSMTP (Author:
     * Chris Ryan).  Returns bool.  Returns false if there is a
     * bad MAIL FROM, RCPT, or DATA input.
     * @access private
     * @return bool
     */
    function __maillog(){
        $this->errorinfo = $this->smtp->getError();
        if(MAIL_LOG){
                error_log(var_export($this->smtp->getError(),true)."\n", 3, DATA_DIR."/mail.log");
        }
    }
    function SmtpSend($to,$header, $body,$config) {
        $smtp_from = $this->Sender;
        if(!$this->smtp->Mail($smtp_from))
        {
            $this->__maillog();
            //trigger_error("from_failed");
            $this->smtp->Reset();
            return false;
        }

        if(!$this->smtp->Recipient($to)){
            $this->__maillog();
            //trigger_error("recipients_failed". $to);
            $this->smtp->Reset();
            return false;
        }
        if(!$this->smtp->Data($header ."\n". $body)) {
            $this->__maillog();
            $this->smtp->Reset();
            return false;
        }

        $this->smtp->Reset();
        //$this->SmtpClose();
        return true;
    }

    /**
     * Initiates a connection to an SMTP server.  Returns false if the
     * operation failed.
     * @access private
     * @return bool
     */
    function SmtpConnect($config) {
        $this->smtp->do_debug = $this->debug;
        $index = 0;
        $this->smtp = kernel::single('desktop_email_smtp');
        $connection = ($this->smtp->Connected());

        if($this->smtp->Connect($config['smtpserver'], $config['smtpport'],20))
        {
            $this->smtp->Hello($_SERVER['HTTP_HOST']?$_SERVER['HTTP_HOST']:'localhost.localdomain');

            if($config['smtpuname'] && !$this->smtp->Authenticate($config['smtpuname'],$config['smtppasswd'])){
                //         trigger_error("authenticate");
                $this->smtp->Reset();
                $connection = false;
            }
            $connection = true;
        }
        return $connection;
    }

    /**
     * Closes the active SMTP session if one exists.
     * @return void
     */
    function SmtpClose() {
        if($this->smtp != NULL)
        {
            if($this->smtp->Connected())
            {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }

}
?>
