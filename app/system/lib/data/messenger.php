<?php
class system_data_messenger{

    public function __construct(&$app)
    {
        $this->app = $app;
    }

    public function loadTitle($action,$type,$data="")
    {
        $tmpArr=$data;
        $title = app::get('system')->getConf('messenger.title.'.$action.'.'.$type);
        if($data != "")
        {
            preg_match_all('/<\{\$(\S+)\}>/iU', $title, $result);
            foreach($result[1] as $k => $v)
            {
                $v=explode('.',$v);
                $data=$tmpArr;
                foreach($v as $key => $val)
                {
                    $data=$data[$val];
                    if(is_array($data))
                    {
                        continue ;
                    }
                    else
                    {
                        $title = str_replace($result[0][$k],$data,$title);
                    }
                }
            }
        }
        return $title;
    }

    function loadTmpl($action,$msg,$lang=''){
        $objMdlsystmpl = app::get('system')->model('messenger_systmpl');
        $msg = $this->_getType($msg);
        return $objMdlsystmpl->get('messenger:'.$msg.'/'.$action);
    }

    public function saveActions($action,&$msg)
    {
        $actions = config::get('messenger.actions');
        foreach($actions as $act=>$info)
        {
            if(!$action[$act]) $action[$act] = array();
        }

        foreach($action as $act=>$call)
        {
            app::get('system')->setConf('messenger.actions.'.$act,implode(',',array_keys($call)));
        }
        return true;
    }

    public function saveContent($action,$messenger,$savedata)
    {
        $objMdlsystmpl = app::get('system')->model('messenger_systmpl');
        $messengers = config::get('messenger.messenger');
        $info = $messengers[$messenger];
        if($info['hasTitle']) app::get('system')->setConf('messenger.title.'.$action.'.'.$messenger,$savedata['title']);
        $msg = $this->_getType($messenger);
        return $objMdlsystmpl->set('messenger:'.$msg.'/'.$action,$savedata['content']);
    }

    public function getSenders($act)
    {
        $ret = app::get('system')->getConf('messenger.actions.'.$act);
        return explode(',',$ret);
    }

    public function setSmsSign($sign)
    {
        $result = $this->checkSign($sign,$msg);
        if(!$result){
            throw new \LogicException($msg);
            return false;
        }
        $signs='【'.$sign.'】';
        $entid = base_enterprise::ent_id();
        $passwd=base_enterprise::ent_ac();
        $params = array(
            'shopexid' => $entid,
            'content' => $signs,
            'passwd' => $passwd,
        );
        $url = config::get('link.sms_api');
        if(config::get('link.sms_debug'))
        {
            $url = config::get('link.sms_sandbox_api');
        }
        $core_http = kernel::single('base_prism');
        $core_http->app_key='xft7toho';
        $core_http->app_secret='zoj66zxqjkq4is3xx762';
        $core_http->base_url=$url;
        //判断是添加还是修改
        $setSmsSign=app::get('system')->getConf('setSmsSign');
        //添加签名
        if(empty($setSmsSign['sign']))
        {
            $result = $core_http->post('/addcontent/new',$params);
        }
        else
        { //修改签名
            $params = array(
                'shopexid' => $entid,
                'passwd' => $passwd,
                'old_content'=>'【'.$setSmsSign['sign'].'】',
                'new_content' => $signs,
            );
            $result = $core_http->post('/addcontent/update',$params);
        }

        $response = json_decode($result,true);

        if($response['res'] == 'succ')
        {
            $array=array(
                'sign'=>trim($sign),
            );
            app::get('system')->setConf('review',$response['data']['review']);
            app::get('system')->setConf('setSmsSign', $array);
            return true;

        }

        //兼容目前出现的“签名不存在”问题
        if($response['code'] == '2010')
        {
            app::get('system')->setConf('setSmsSign', null);
        }
        $msg = $response['data']?$response['data']:"请求设置短信签名出错";
        throw new \LogicException($msg);
        return false;
    }

    public function checkSign($sign,&$msg)
    {
        if(mb_strlen(urldecode(trim($sign)),'utf-8') > 8 || mb_strlen(urldecode(trim($sign)),'utf-8') < 2)
        {
            $msg = app::get('system')->_("签名长度为2到8字");
            return false;
        }

        $arr=array('天猫','tmall','淘宝','taobao','1号店','易迅','京东','亚马逊','test','测试');
        for ($i=0; $i <count($arr) ; $i++)
        {
            if(strstr(strtolower($sign),$arr[$i] ))
            {
                $msg = app::get('system')->_("非法签名");
                return false;
            }
        }
        return true;
    }

    private function _getType($msg)
    {
        $messenger = config::get('messenger.messenger');
        return $messenger[$msg]['class'];
    }


}


