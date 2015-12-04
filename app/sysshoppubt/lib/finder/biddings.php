<?php
class sysshoppubt_finder_biddings{

    //操作按钮
    public $column_editbutton = '操作';
    public $column_editbutton_width=220;
	//子列表商品明细
    public $detail_edit2 = '商品明细';
    public function detail_edit2($row){
        // $oItem = app::get('sysshoppubt')->model('standard_item');
        // $oItem = kernel::single("sysshoppubt_mdl_sprodrelease");
        // $pagedata['standard_item'] = $oItem->getList('*',array('bidding_id' => $row));

        $oItems = app::get('sysshoppubt')->model('biddings');
        $oItem = app::get('sysshoppubt')->model('standard_item');
        $uc = $oItems->getList('uniqid',array('bidding_id' => $row));
        $pagedata['standard_item'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));

        //return 'detail';
        return view::make('sysshoppubt/biddings/itemdetail.html', $pagedata)->render();
    }


    public $detail_bidding = '地址信息';
    public function detail_bidding($row){
        $oItem = app::get('sysshoppubt')->model('deliveryaddr');
        $oItems = app::get('sysshoppubt')->model('biddings');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $uc = $oItems->getList('uniqid',array('bidding_id' => $row));
        $pagedata['deliveryaddr'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));
        //return 'detail';
        return view::make('sysshoppubt/addr/addrdetail.html', $pagedata)->render();
    }

    public $detail_bidding1 = '参与竞价列表';
    public function detail_bidding1($row){
        $oItem = app::get('sysshoppubt')->model('biddingsitems');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $pagedata['biddingsitems'] = $oItem->getList('*',array('bidding_id' => $row));
        //return 'detail';
        return view::make('sysshoppubt/biddings/biddetail.html', $pagedata)->render();
    }


    public $detail_bidding2 = '审核';
    public function detail_bidding2($row){
        $oItem = app::get('sysshoppubt')->model('biddings');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $chech = $oItem->getList('*',array('bidding_id' => $row));
        if($chech[0]['is_through'] == '1'){
            return view::make('sysshoppubt/check/passed.html', $pagedata)->render();
        }
        else{
        $pagedata['sprodrelease_id']= $chech[0]['bidding_id'];
        $pagedata['sprodrelease_uniqid']= $chech[0]['uniqid'];
        $pagedata['id'] = $row;
        //return 'detail';
        return view::make('sysshoppubt/check/biddingcheck.html', $pagedata)->render();
        }
    }


    public function column_editbutton(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=biddings&act=infocontent&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['bidding_id'].'&p[1]='.$row['uniqid'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('通知商家').'\', width:500, height:200}';
            $title = app::get('sysshoppubt')->_('添加通知');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }

}