<?php
class sysshoppubt_finder_sprodrelease{

//操作按钮
    public $column_editbutton = '操作';/*
    public $column_Tissuesamplen = '组织看样';*/
    public $column_editbutton_width=220;

//子列表地址信息功能
    public $detail_edit1 = '地址信息';
    public function detail_edit1($row){

         $oItem = app::get('sysshoppubt')->model('deliveryaddr');
        $oItems = app::get('sysshoppubt')->model('sprodrelease');
        $uc = $oItems->getList('uniqid',array('standard_id' => $row));
        $pagedata['deliveryaddr'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));
        //return 'detail';
        return view::make('sysshoppubt/addr/addrdetail.html', $pagedata)->render();
    }
//子列表商品明细
    public $detail_edit2 = '商品明细';
    public function detail_edit2($row){

        $oItems = app::get('sysshoppubt')->model('sprodrelease');
        $oItem = app::get('sysshoppubt')->model('standard_item');
        $uc = $oItems->getList('uniqid',array('standard_id' => $row));
        $pagedata['standard_item'] = $oItem->getList('*',array('uniqid' => $uc[0]['uniqid']));
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        // $pagedata['standard_item'] = $oItem->getList('*',array('standard_id' => $row));
        //return 'detail';
        return view::make('sysshoppubt/sprodrelease/standarditem.html', $pagedata)->render();
    }
//审核功能按钮
    public $detail_edit3 = '审核';
    public function detail_edit3($row){
        $oItem = app::get('sysshoppubt')->model('sprodrelease');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        
        $chech = $oItem->getList('*',array('standard_id' => $row));
        if($chech[0]['is_through'] == '1'){
            return view::make('sysshoppubt/check/passed.html', $pagedata)->render();
        }
        else{
        $pagedata['sprodrelease_id']= $chech[0]['standard_id'];
        $pagedata['sprodrelease_uniqid']= $chech[0]['uniqid'];
        $pagedata['id'] = $row;
        //return 'detail';
        return view::make('sysshoppubt/check/standardcheck.html', $pagedata)->render();
        }
    }

    public $detail_edit4 = '参加看样用户信息';
    public function detail_edit4($row){
        $oItem = app::get('sysshoppubt')->model('sample');
        /*$oItem = kernel::single("sysshoppubt_mdl_sprodrelease");*/
        
        $chech = $oItem->getList('*',array('standard_id' => $row));
        $pagedata['items'] = $chech;
        
        //return 'detail';
        return view::make('sysshoppubt/sample/sample.html', $pagedata)->render();
        
    }

    /**
     * @brief 操作列内容的显示(one)
     *
     * @param $row
     *
     * @return
     */

    public function column_editbutton(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {


            $url = '?app=sysshoppubt&ctl=sprodrelease&act=infocontent&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['standard_id'].'&p[1]='.$row['uniqid'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('通知商家').'\', width:800, height:500}';
            $title = app::get('sysshoppubt')->_('添加通知');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }
    /*public function column_Tissuesamplen(&$colList, $list)
        {
            foreach($list as $k=>$row)
            {
                $url = '?app=sysshoppubt&ctl=sample&act=tissue&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['standard_id'].'&p[1]='.$row['uniqid'];
                $title = app::get('sysshoppubt')->_('组织看样');
                $button = '<a href="' . $url . '">' . $title . '</a>';
                $colList[$k] = $button;
            }
        }*/
}