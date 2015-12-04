<?php
class topc_ctl_member_enquireinfo extends topc_ctl_member {
//询价信息
    public function enquireinfo()
    {
        $userId = userAuth::id();
        try
        {
            $postdata = $this->__checkdata(input::get());
            $postdata['user_id'] =$userId;
            $userMdlAddr = app::get('sysspfb')->model('enquireinfo');
            $userMdlAddr->save($postdata);
        }
        catch (Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        $msg = app::get('topc')->_('提交成功');
        return $this->splash('success',null,$msg);
    }
    
    private function __checkdata($data)
    {
        if($data['name']=='')
        {
             throw new \LogicException('您的姓名不能为空！');
        }
        if($data['phone']=='')
        {
             throw new \LogicException('联系方式不能为空！');
        }
        if($data['email']=='')
        {
             throw new \LogicException('email不能为空！');
        }
        if(!preg_match('/^(?:[a-z\d]+[_\-\+\.]?)*[a-z\d]+@(?:([a-z\d]+\-?)*[a-z\d]+\.)+([a-z]{2,})+$/i',trim($data['email'])) )
        {
            throw new \LogicException('邮件格式不正确');
        }
        return $data;
    }
    //询价信息列表页
    public function enquire()
    {
        $userInfo = userAuth::getUserInfo();
        $userId = userAuth::id();
        $rows = "*";
        try
            {
                $userMdlAddr = app::get('sysspfb')->model('enquireinfo');
                $enquireList =$userMdlAddr->getList($rows,array('user_id'=>$userId));
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $userName = app::get('sysuser')->model('account');
                $infoName =$userName->getList(login_account,array('user_id'=>$userId));
            }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
        $pagedata['enquireList'] = $enquireList;
        $pagedata['enquireCounts'] = $count;
        $pagedata['infoName'] = $infoName[0]['login_account'];
        $pagedata['action'] = 'topc_ctl_member_enquireinfo@enquire';
        $this->action_view = "enquire/enquireInfo.html";
        return $this->output($pagedata);
    }

    //我参与的供求
    public function inreqsupp(){
        $userInfo = userAuth::getUserInfo();
        $userId = userAuth::id();
        try{
            $sql = "select seo.user_id,seo.reqsupp_id,seo.ifrequire,ssf.cat_id,ssf.cat_name,
                    ssf.variety_name,ssf.name,ssf.tel,ssf.email,ssf.effective_time,'供应' listtype 
                    from sysspfb_enquireinfo seo join sysspfb_supplyInfo ssf on seo.reqsupp_id=ssf.supply_id and ifrequire=1 
                    where seo.user_id=".$userId." 
                    union 
                    select seo.user_id,seo.reqsupp_id,seo.ifrequire,srf.cat_id,srf.cat_name,
                    srf.variety_name,srf.name,srf.tel,srf.email,srf.effective_time,'求购' listtype 
                    from sysspfb_enquireinfo seo join sysspfb_requireInfo srf on seo.reqsupp_id=srf.require_id and ifrequire=2 
                    where seo.user_id=".$userId;
            $reqsupplist =app::get("base")->database()->executeQuery($sql)->fetchAll();

            
        }catch(Exception $e){
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
        }catch(\LogicException $e){
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
        }
        $pagedata['reqsupplist'] = $reqsupplist;
        // $pagedata['enquireCounts'] = $count;
        // $pagedata['infoName'] = $infoName[0]['login_account'];
        // $pagedata['action'] = 'topc_ctl_member_enquireinfo@enquire';
        $this->action_view = "enquire/inreqsuppinfo.html";
        return $this->output($pagedata);
    }

    //询价信息查看页
    public function enquireEdit()
    {
        $userInfo = userAuth::getUserInfo();
        $enquire_id = input::get("enquireId");
        $rows = "*";
        try
            {
                $userMdlAddr = app::get('sysspfb')->model('enquireinfo');
                $enquireList =$userMdlAddr->getList($rows,array('enquire_id'=>$enquire_id));
            }
         catch(Exception $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
            catch(\LogicException $e)
            {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg);
            }
        $pagedata['enquireList'] = $enquireList[0];
        $pagedata['action'] = 'topc_ctl_member_enquireinfo@enquireEdit';
        $this->action_view = "enquire/enquireEdit.html";
        return $this->output($pagedata);
    }
}