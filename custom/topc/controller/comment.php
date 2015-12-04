<?php
class topc_ctl_comment extends topc_controller {

    public function getItemRate()
    {
    	$userid = userAuth::id();
        $itemId = input::get();
        if( empty($itemId) ) return '';
        foreach ($itemId as $key => $value) {
            if($key=='standard_id')$type = 0;
            elseif ($key=='bidding_id') {
                $type = 1;
            }elseif ($key=='tender_id') {
                $type = 2;
            }
        }
        $info = app::get('sysshoppubt')->model('sprodrelease');
        $bid = app::get('sysshoppubt')->model('biddings');
        $tend = app::get('sysshoppubt')->model('tender');
        $comment = app::get('sysshoppubt')->model('comment');
        if($type==0){
        $infos = $info->getRow('*',array('standard_id'=>$itemId['standard_id']));
        $id = $itemId['standard_id'];
        $pagedata['type'] = 0;
        }elseif ($type==1) {
        $infos = $bid->getRow('*',array('bidding_id'=>$itemId['bidding_id']));
        $pagedata['type'] = 1;
        $id = $itemId['bidding_id'];
        }elseif ($type==2) {
        $infos = $tend->getRow('*',array('tender_id'=>$itemId['tender_id']));
        $pagedata['type'] = 2;
        $id = $itemId['tender_id'];
        }
        $pagedata['item_id'] = $id;
        $pagedata['rowinfo'] = $infos;
        $pagedata['user_id'] = $userid;
		$pagedata['user_name'] = userAuth::getLoginName();
        $comm = $comment->getList('*',array('shop_id'=>$infos['shop_id'],'item_id'=>$id,'type'=>$type,'is_lock'=>0));
        $pagedata['comm'] = $comm;
        return view::make('topc/comment/rate.html', $pagedata);
    }
    public function save(){
    	$data = input::get();
        $comment = app::get('sysshoppubt')->model('comment');
        $data['created_time'] = time();
        $comment->save($data);
        return view::make('topc/comment/rate.html', $pagedata);
    }

}