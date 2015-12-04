<?php
class topc_ctl_member_standarditem extends topc_ctl_member {


//保存商品
	public function saveItem(){
		  $item=app::get('sysshoppubt')->model('standard_item');
   		$postData = input::get();
      $uniqid=$postData['uniqid'];
   		$arr=$postData['is_check'];
   		$item_ids=$postData['item_id'];
      $bns=$postData['bn'];
      $titles=$postData['title'];
      $prices=0;
      $nums=$postData['num'];
   		$i=0;
   		foreach ($arr as  $val) {
            $str=array();
   			if($val['is_check']==1){
               $str['uniqid']=$uniqid;
               $str['item_id']=$item_ids[$i];
               $str['bn']=$bns[$i];
               $str['price']=$prices[$i];
               $str['title']=$titles[$i];
               $str['num']=$nums[$i];
               $item->save($str);
   			 }
			$i++;
   		}
        $rows=$item->getList('*',array('uniqid'=>$uniqid));
        foreach ($rows as $key => $value) {
          # code...
          $item_id=$value["item_id"];
          $itemRow=app::get('sysitem')->model('item')->getRow("*",array("item_id"=>$item_id));
          $rows[$key]["image_id"]=$itemRow["image_default_id"];
        }
        $pagedata['rows'] = $rows;
        if($postData['bidding_goods']==1){
              return view::make('topc/member/biddings/goodlist.html',$pagedata);
        }
        return view::make('topc/member/shoppubt/goodlist.html',$pagedata);

	}
}