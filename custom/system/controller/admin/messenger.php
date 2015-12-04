<?php
class system_ctl_admin_messenger extends desktop_controller{

    public function __construct($app)
    {
        parent::__construct($app);
        header("cache-control: no-store, no-cache, must-revalidate");
    }

    public function index()
    {
        $this->path[] = array('text'=>app::get('system')->_('邮件短信配置'));
        $objMessenger = kernel::single('system_data_messenger');
        $objSms = kernel::single('system_messenger_smschg');
        $actions = config::get('messenger.actions');
        foreach($actions as $act=>$info){
            $list = $objMessenger->getSenders($act);
            foreach($list as $msg){
                $pagedata['call'][$act][$msg] = true;
            }
        }
        $setSmssign = app::get('system')->getConf('setSmsSign');
        $pagedata['smsSign'] = $setSmssign;
        $pagedata['sms_url'] = $objSms->getSmsBuyUrl();
        $pagedata['actions'] = $actions;
        $pagedata['messenger'] = config::get('messenger.messenger');
        return $this->page('system/admin/messenger/index.html', $pagedata);
    }


    public function edtmpl($action,$msg)
    {
        $objMessenger = kernel::single('system_data_messenger');
        $messenger = config::get('messenger.messenger');
        $actions = config::get('messenger.actions');

        $info = $messenger[$msg];
        if($pagedata['hasTitle'] = $info['hasTitle'])
        {
            $pagedata['title'] = $objMessenger->loadTitle($action,$msg);
        }
        $pagedata['body'] = $objMessenger->loadTmpl($action,$msg);
        $pagedata['type'] = $info['isHtml']?'html':'textarea';
        $pagedata['messenger'] = $msg;
        $pagedata['action'] = $action;
        $pagedata['varmap'] = $actions[$action]['varmap'];
        $pagedata['action_desc'] = $actions[$action]['label'];
        $pagedata['msg_desc'] = $info['label'];
        return $this->singlepage('system/admin/messenger/edtmpl.html', $pagedata);

    }

    public function viewtmpl($action,$msg)
    {
        $objMessenger = kernel::single('system_data_messenger');
        $pagedata['body'] = $objMessenger->loadTmpl($action,$msg);
        $setSmsSign = app::get('system')->getConf('setSmsSign');
        $pagedata['smssign'] = is_array($setSmsSign) ? $setSmsSign['sign'] : '';
        return $this->page('system/admin/messenger/viewtmpl.html', $pagedata);

    }

    public function save()
    {
        $this->begin();
        $objMessenger = kernel::single('system_data_messenger');
        $postdata = input::get('actdo');
        $result = $objMessenger->saveActions($postdata,$msg);
        $this->end($result);
    }

    public function setSmsSign()
    {
        $setSmsSign = app::get('system')->getConf('setSmsSign');
        $pagedata['sign'] = is_array($setSmsSign) ? $setSmsSign['sign'] : '';
        return $this->page('system/admin/messenger/setsms.html', $pagedata);
    }

    public function saveSmsSign()
    {
        $sign = input::get('sign');
        $objMessenger = kernel::single('system_data_messenger');
        try
        {
            $result = $objMessenger->setSmsSign($sign,$msg);
            $this->adminlog("短信签名设置", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("短信签名设置", 0);
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,"短信签名配置保存成功");
    }

    public function saveTmpl()
    {
        $this->begin();
        $objMessenger = kernel::single('system_data_messenger');
        $postdata = input::get('data');
        $content=$this->checkReg($postdata['content']);
        if($postdata['messenger']=='sms')
        {
            if($content=='false')
            {
                $this->end(false,app::get('system')->_('含有非法字符'));
            }
        }
        $savedata = array(
            'content'=>htmlspecialchars_decode($content),
            'title'=>$postdata['title'],
        );

        $ret = $objMessenger->saveContent($postdata['actdo'],$postdata['messenger'],$savedata);
        if($ret){
            $this->adminlog("邮件短信模板编辑[{$postdata['messenger']}]", 1);
            $this->end(true,app::get('system')->_('操作成功'));
        }else{
            $this->adminlog("邮件短信模板编辑[{$postdata['messenger']}]", 0);
            $this->end(false,app::get('system')->_('操作失败'));
        }
    }

    public function checkReg($content)
    {
        $arr = array(
            '【', '】',
        );
        if ((strstr($content, $arr[0]) && (strstr($content, $arr[1]))) != false)
        {
            return 'false';
        }
        return $content;
    }

}


