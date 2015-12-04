<?php
class topc_ctl_member_notice extends topc_ctl_member
{
    
    public function index() {
        $userId = userAuth::id();
        $params["user_id"] = $userId;
        $shopInfoGet = app::get('topc')->rpcCall('shop.get.shopInfo', $params, 'buyer');
        $shopnotice=app::get("sysshop")->model("shop_notice")->getList("*",array("shop_id"=>$shopInfoGet["shop_id"]),0,-1,"is_read  asc");
        $pagedata['shopnotice'] = $shopnotice;
        $pagedata['action'] = 'topc_ctl_member_notice@index';
        $this->action_view = "notice/index.html";
        return $this->output($pagedata);

    }
    public function update()
    {
        $noticeid=$_POST["notice_id"];
        $noticemdl=app::get("sysshop")->model("shop_notice");
        $notice=$noticemdl->getRow("*",array("notice_id"=>$noticeid));
        $notice["is_read"]=true;
        try {
             $noticemdl->save($notice);
             $ajaxdata["result"]="success";
             return response::json($ajaxdata);
        } catch (Exception $e) {
            $ajaxdata["result"]="error";
            return response::json($ajaxdata);
        }
       

    }
}
