<?php

/**
 * @brief 商品列表
 */
class sysshoppubt_ctl_tenderenter extends desktop_controller{
	
 	public function index()
    {
        return $this->finder('sysshoppubt_mdl_tenderenter',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('招标干预列表'),
            'use_buildin_delete'=>false,
        ));
    }

    public function close(){
        
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        $msg = "关闭成功";
    	$datas = app::get('sysshoppubt')->model('tenderenter')->getRow('*');
    	if(!$datas['openornot']){
    		$sql="update sysshoppubt_tenderenter set openornot=1 where tenderenter_id=".$datas['tenderenter_id'];
          	$db = app::get('sysshoppubt')->database();
          	$db->exec($sql);
    	}
        $this->end(1,$msg);
    }

    public function open(){
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        $msg = "开启成功";
    	$datas = app::get('sysshoppubt')->model('tenderenter')->getRow('*');
    	if($datas['openornot']){
    		$sql="update sysshoppubt_tenderenter set openornot=0 where tenderenter_id=".$datas['tenderenter_id'];
          	$db = app::get('sysshoppubt')->database();
          	$db->exec($sql);
    	}
        $this->end(1,$msg);
    }

    public function other(){

    }
}