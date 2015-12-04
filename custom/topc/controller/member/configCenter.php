<?php
class topc_ctl_member_configCenter extends topc_ctl_member{
    public function index()
    {
    	$userInfo = userAuth::getUserInfo();
    	$params["user_id"]=$userInfo["userId"];
        $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        $pagedata["shopInfo"]=$shopInfo;
        $pagedata['action'] = 'topc_ctl_member_configCenter@index';
        $this->action_view = "configCenter/configCenter.html";
        return $this->output($pagedata);
    }
    public function save()
    {
    	$shop_id=$_POST["shop_id"];
        if(!$shop_id){
            return $this->splash('error',null,'请先完成企业入驻信息完善！');
        }
    	$shop_logo=$_POST["shop_logo"][0];
        if(!$shop_logo){
            $shop_logo="";
        }
    	$background_img=$_POST["background_img"][0];
        if(!$background_img){
            $background_img="";
        }
        $map_img=$_POST["map_img"][0];
        if(!$map_img){
            $map_img="";
        }
    	$sql="UPDATE sysshop_shop set shop_logo='".$shop_logo."',background_img='".$background_img."',map_img='".$map_img."' where shop_id=".$shop_id."";
    	try{
    		$result=app::get('sysshop')->database()->executeUpdate($sql);
    	}catch(Exception $e){
    		$msg = $e->getMessage();
            return $this->splash('error',null,$msg);
    	}
    	return $this->splash('success',null,"保存成功！");
    	
    }
}