<?php
class sysshoppubt_finder_tender{

     //操作按钮
    public $column_editbutton = '操作';
    public $column_editbutton_width=220;
	//子列表商品明细
    public $detail_edit = '商品明细';
    public function detail_edit($row){
        // $oItem = app::get('sysshoppubt')->model('standard_item');
        // $oItem = kernel::single("sysshoppubt_mdl_sprodrelease");
        // $pagedata['standard_item'] = $oItem->getList('*',array('bidding_id' => $row));

        $oItems = app::get('sysshoppubt')->model('tender');
        $oItem = app::get('sysshoppubt')->model('standard_item');
        $uc = $oItems->getList('uniqid',array('tender_id' => $row));
        $pagedata['standard_item'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));

        //return 'detail';
        return view::make('sysshoppubt/biddings/itemdetail.html', $pagedata)->render();
    }


    public $detail_addr = '地址信息';
    public function detail_addr($row){
        $oItem = app::get('sysshoppubt')->model('deliveryaddr');
        $oItems = app::get('sysshoppubt')->model('tender');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $uc = $oItems->getList('uniqid',array('tender_id' => $row));
        $pagedata['deliveryaddr'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));
        //return 'detail';
        return view::make('sysshoppubt/addr/addrdetail.html', $pagedata)->render();
    }

 


    public $detail_check = '审核';
    public function detail_check($row){
        $oItem = app::get('sysshoppubt')->model('tender');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $chech = $oItem->getList('*',array('tender_id' => $row));
        if($chech[0]['is_through'] == '1'){
            return view::make('sysshoppubt/check/passed.html', $pagedata)->render();
        }
        else{
        $pagedata['sprodrelease_id']= $chech[0]['tender_id'];
        $pagedata['sprodrelease_uniqid']= $chech[0]['uniqid'];
        $pagedata['id'] = $row;
        //return 'detail';
        return view::make('sysshoppubt/check/tendercheck.html', $pagedata)->render();
        }
    }

    public $detail_tenderenter = '投标列表';
    public function detail_tenderenter($row){
        $oItem = app::get('sysshoppubt')->model('tenderenter');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        $chech = $oItem->getList('*',array('tender_id' => $row));
        $pagedata['items']= $chech;
        //return 'detail';
        return view::make('sysshoppubt/tender/tenderenter.html', $pagedata)->render();
    }

    public $detail_sample = '参加看样用户信息';
    public function detail_sample($row){
        $oItem = app::get('sysshoppubt')->model('sample');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        
        $chech = $oItem->getList('*',array('standard_id' => '','standard_id'=> '','tender_id'=>$row));
        $pagedata['items'] = $chech;
        
        //return 'detail';
        return view::make('sysshoppubt/sample/sample.html', $pagedata)->render();
        
    }

    public function column_editbutton(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=tender&act=infocontent&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tender_id'].'&p[1]='.$row['uniqid'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('通知企业').'\', width:500, height:200}';
            $title = app::get('sysshoppubt')->_('添加通知');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }
}