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
        $tenderenterid=$_GET["tenderenter_id"];
    	$datas = app::get('sysshoppubt')->model('tenderenter')->getRow('*',array("tenderenter_id"=>$tenderenterid));
    	if($datas['openornot']){
    		$sql="update sysshoppubt_tenderenter set openornot=0 where tenderenter_id=".$tenderenterid;
          	$db = app::get('sysshoppubt')->database();
          	$db->exec($sql);
    	}
        $this->end(1,$msg);
    }

    public function other(){
        $tenderenterid = input::get('tenderenter_id');
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $datas = $tenderenter->getRow('*',array('tenderenter_id'=>$tenderenterid));
        $pagedata['worncontent'] = $datas['worncontent'];
        $pagedata['tenderenter_id'] = $tenderenterid;
        return view::make('sysshoppubt/worning/worning.html',$pagedata);
    }
    public function worning(){
        $data = input::get();
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $datas = $tenderenter->getRow('*',array('tenderenter_id'=>$data['tenderenter_id']));
        $olddatas = $datas;
        $datas['is_worning'] = 0;
        $datas['worncontent'] = $data['worncontent'];
        try {
        $tenderenter->update($datas,$olddatas);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        return $this->splash('success',null,"发布成功");
    }
    public function state(){
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        $data = input::get();
        $tenderenter = app::get('sysshoppubt')->model('tenderenter');
        $datas = $tenderenter->getRow('*',array('tenderenter_id'=>$data['tenderenter_id']));
        $olddatas = $datas;
        if($data['is_worning']==1){
            $datas['is_worning'] = 0;
        }elseif($data['is_worning']==0){
            $datas['is_worning'] = 1;
        }
        try {
            $tenderenter->update($datas,$olddatas);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->end(0,$msg); 
        }
        $this->end(1,"修改成功");
    }
}