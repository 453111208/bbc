<?php
class topc_ctl_member_info extends topc_ctl_member {

//我发布的资讯 
    public function publishInfo()
    {
        $userInfo = userAuth::getUserInfo();
        $userId = userAuth::id();
        $rows = "*";
        try
            {
                $userMdlAddr = app::get('sysinfo')->model('article');
                $infoList =$userMdlAddr->getList($rows,array('user_id'=>$userId));
                $count = $userMdlAddr->count(array('user_id'=>$userId));
                $userName = app::get('sysuser')->model('account');
                $infoName =$userName->getList(login_account,array('user_id'=>$userId));
                $params["user_id"]=$userInfo["userId"];
                $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
                $pagedata["shopInfo"]=$shopInfo;
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
        $pagedata['infoName'] = $infoName[0]['login_account'];
        $pagedata['infoList'] = $infoList;
        $pagedata['infoCounts'] = $count;
        $pagedata['action'] = 'topc_ctl_member_info@publishInfo';
        $this->action_view = "info/publishInfo.html";
        return $this->output($pagedata);
    }
        /**
    * 资讯编辑
    **/
    public function editInfo()
    {
        $nodeId = input::get('node_id');
        if(!empty($nodeId))
        {
            $pagedata['article']['node_id'] = $nodeId;
        }
        $sysinfoLibNode = kernel::single('sysinfo_article_node');
        $nodeList = $sysinfoLibNode->getNodeList();
        foreach ($nodeList as $key => $value)
        {
            $selectmaps[$key]['node_id'] = $value['node_id'];
            $selectmaps[$key]['step'] = $value['node_depth'];
            $selectmaps[$key]['node_name'] = $value['node_name'];
        }
        $pagedata['selectmaps'] = $selectmaps;
        $pagedata['action'] = 'topc_ctl_member_info@editInfo';
        $this->action_view = "info/editInfo.html";
        return $this->output($pagedata);
    }
     /**
    * 资讯保存
    **/
        public function saveInfo(){
            $userId = userAuth::id();
            //$postData =utils::_filter_input(input::get());
            $postData = input::get();
            $postData['user_id'] = $userId;
            $postData['modified']=time();
            $postData['pubtime']=time();
            $postData['ifpub'] = 0;
            $postData["platform"] = 'pc';
            $userName = app::get('sysuser')->model('account');
            $infoName =$userName->getList(login_account,array('user_id'=>$userId));
            $postData['source'] = $infoName[0]['login_account'];
            $postData["article_logo"] = $postData["article_logo"][0];
            try
            {
                $userMdlAddr = app::get('sysinfo')->model('article');
                $userMdlAddr->save($postData);
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
            $url = url::action('topc_ctl_member_info@publishInfo');
            $msg = app::get('topc')->_('添加成功');
            return $this->splash('success',$url,$msg);
        }

}

