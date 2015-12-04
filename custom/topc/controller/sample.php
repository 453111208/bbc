
<?php
class topc_ctl_sample extends topc_controller{
//看样


	//看样 dialog
	public function sample_dialog()
    {
        return view::make('topc/sample/index.html',$pagedata);
    }

    public function save_sample(){
		$userId = userAuth::id();
    $params["user_id"]=$userId;
    $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        if(!$userId)
        {
            $url = url::action('topc_ctl_passport@signin');
            return $this->splash('error',$url);
        }
        $data=$_POST;
        /*if(strtotime($data['ceshi'])<strtotime($data['about_time'])){
          return $this->splash('error',null, app::get('topc')->_('看样时间不可以大于截止时间'));
          }*/
        $sysshoppubt_sample_model=app::get('sysshoppubt')->model('sample');
        $stan = $sysshoppubt_sample_model->getList('seegoods_id',array('shop_id'=>$data['shop_id'],'user_id'=>$shopInfoGet['shop_id'],'standard_id'=>$data['standard_id']));
        $bid = $sysshoppubt_sample_model->getList('seegoods_id',array('shop_id'=>$data['shop_id'],'user_id'=>$shopInfoGet['shop_id'],'bidding_id'=>$data['bidding_id']));
        $tend = $sysshoppubt_sample_model->getList('seegoods_id',array('shop_id'=>$data['shop_id'],'user_id'=>$shopInfoGet['shop_id'],'tender_id'=>$data['tender_id']));
        if(!$stan&&!$bid&&!$tend){
        /*$user = app::get('sysuser')->model('user');
        $username = $user->getRow('user_name',array('user_id'=>$userId));*/
        $data['user_name'] = $shopInfoGet['shop_name'];
        $data['user_id']=$shopInfoGet['shop_id'];
        
        $data['create_time']=time();
        $data['about_time']=strtotime($data['about_time']);
       	$sysshoppubt_sample_model->save($data);

          if ($data['type']==0) {
          $total=$sysshoppubt_sample_model->count(array('standard_id'=>$data['standard_id']));
          $sql="update sysshoppubt_sprodrelease set attendcount='".$total."' where standard_id=".$data['standard_id'];
          $db = app::get('sysshoppubt')->database();
          $db->exec($sql);
          return $this->splash('success',null, app::get('topc')->_('参加成功'));

          } elseif ($data['type']==2) {
            $total=$sysshoppubt_sample_model->count(array('tender_id'=>$data['tender_id']));
          $sql="update sysshoppubt_tender set attendcount='".$total."' where tender_id=".$data['tender_id'];
          $db = app::get('sysshoppubt')->database();
          $db->exec($sql);
          return $this->splash('success',null, app::get('topc')->_('参加成功'));
          } elseif ($data['type']==1) {
            $total=$sysshoppubt_sample_model->count(array('bidding_id'=>$data['bidding_id']));
          $sql="update sysshoppubt_biddings set attendcount='".$total."' where bidding_id=".$data['bidding_id'];
          $db = app::get('sysshoppubt')->database();
          $db->exec($sql);
          return $this->splash('success',null, app::get('topc')->_('参加成功'));
          }
          }else{
          return $this->splash('error',null, app::get('topc')->_('您已提交申请，请等待！'));
          }
	}
}