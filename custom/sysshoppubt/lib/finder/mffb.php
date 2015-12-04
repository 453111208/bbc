<?php
class sysshoppubt_finder_mffb{

     //操作按钮
    public $column_editbutton = '操作';
    public $column_editbutton_width=220;
	//子列表商品明细
     public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=admin_mffb&act=update&finder_id='.$_GET['_finder']['finder_id'].'&mffb_id='.$row['mffb_id'];
            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysshoppubt')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }
    }