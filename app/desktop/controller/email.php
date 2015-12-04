<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class desktop_ctl_email extends desktop_controller{
    var $workground = 'desktop_ctl_system';

     public function __construct($app)
    {
        parent::__construct($app);
    }

    function setting(){
        $pagedata['options'] = $this->getOptions();
        $pagedata['messengername'] = "messenger";
        return view::make('desktop/email/config.html', $pagedata);
    }

     function getOptions(){
        return array(
            'sendway'=>array('title'=>app::get('desktop')->_('发送方式'),'type'=>'hidden','options'=>array('smtp'=>app::get('desktop')->_("使用外部SMTP发送")),'value'=>$this->app->getConf('email.config.sendway')?$this->app->getConf('email.config.sendway'):"smtp"),
            'usermail'=>array('label'=>app::get('desktop')->_('发信人邮箱'),'type'=>'input','value'=>$this->app->getConf('email.config.sendway')?$this->app->getConf('email.config.usermail'):'yourname@domain.com'),
            'smtpserver'=>array('label'=>app::get('desktop')->_('smtp服务器地址'),'type'=>'input','value'=>$this->app->getConf('email.config.smtpserver')?$this->app->getConf('email.config.smtpserver'):'mail.domain.com'),
            'smtpport'=>array('label'=>app::get('desktop')->_('smtp服务器端口'),'type'=>'input','value'=>$this->app->getConf('email.config.smtpport')?$this->app->getConf('email.config.smtpport'):'25'),
            'smtpuname'=>array('label'=>app::get('desktop')->_('smtp用户名'),'type'=>'input','value'=>$this->app->getConf('email.config.smtpuname')?$this->app->getConf('email.config.smtpuname'):''),
            'smtppasswd'=>array('label'=>app::get('desktop')->_('smtp密码'),'type'=>'password','value'=>$this->app->getConf('email.config.smtppasswd')?$this->app->getConf('email.config.smtppasswd'):'')
        );
    }

    function saveCfg(){
       $this->begin();
           foreach($_POST['config'] as $key=>$value){
            $this->app->setConf('email.config.'.$key,$value);
        }
        $this->adminlog("编辑邮件配置", 1);
        $this->end(true,app::get('desktop')->_('配置保存成功'));
    }
      function testEmail(){
        $pagedata['options'] = $_GET['config'];
        return view::make('desktop/email/testemail.html', $pagedata);
    }


    function doTestemail(){
        $usermail = $_POST['usermail'];     //发件账户
        $smtpport = $_POST['smtpport'];     //端口号
        $smtpserver = $_POST['smtpserver']; //邮件服务器
        $smtpuname = $_POST['smtpuname'];   //账户名称
        $smtppasswd  = $_POST['smtppasswd'];//账户密码
        $acceptor = $_POST['acceptor'];     //收件人邮箱

        $subject = app::get('desktop')->_("来自[").app::get('site')->getConf('site.name').app::get('desktop')->_("]网店的测试邮件");
        $body = app::get('desktop')->_("这是一封测试邮箱配置的邮件，您的网店能正常发送邮件。");

        $email = kernel::single('desktop_email_email');
        $loginfo = app::get('desktop')->_("无法发送测试邮件，下面是出错信息：");
        if ($email->ready($_POST)){
            $res = $email->send($acceptor,$subject,$body,$_POST);
            if ($res)
                $loginfo = app::get('desktop')->_("已成功发送一封测试邮件，请查看接收邮箱。");
            if ($email->errorinfo){
                $err=$email->errorinfo;
                $loginfo .= "<br>".$err['error'];
            }
        }
        else{
            $loginfo .= "<br>".var_export($email->smtp->error,true);
        }
        echo $loginfo;
    }
}
?>
