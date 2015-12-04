<?php
class sysshoppubt_finder_consultation{

    public $detail_basic = '基本信息';
    public function detail_basic($Row)
    {
        $objMdlConsultation = app::get('sysshoppubt')->model('consultation');
        $row = "consultation_id,be_reply_id,item_title,shop_name,author,consultation_type,contack,content,created_time,is_display,shop_id,item_id,author_id,ip";
        $consultation = $objMdlConsultation->getRow($row,array('consultation_id'=>$Row));
        $itemId = $consultation['item_id'];
        $shopId = $consultation['shop_id'];
        /*$item = app::get('sysrate')->rpcCall('item.get',array('item_id'=>$itemId,'fields'=>'title,image_default_id'));*/
        $standardinfo = app::get('sysshoppubt')->model('sprodrelease');
        $item = $standardinfo->getRow('*',array('standard_id'=>$itemId));
        if($item)
        {
            $consultation['item_title'] = $item['trading_title'];
            $consultation['image_default_id'] = $item['image_default_id'];
            $url = url::action('topc_ctl_standard@index',array('item_id'=>$itemId));
            $consultation['item_url'] = $url;
            $url = url::action('topc_ctl_shopcenter@index',array('shop_id'=>$shopId));
            $consultation['shop_url'] = $url;
        }
        $pagedata['comment'] = $consultation;
        $row = "consultation_id,be_reply_id,author,content,created_time,is_display";
        $reply = $objMdlConsultation->getList($row,array('be_reply_id'=>$Row));
        $pagedata['reply'] = $reply;
        return view::make('sysshoppubt/consultation/consultation.html',$pagedata)->render();
    }


}
