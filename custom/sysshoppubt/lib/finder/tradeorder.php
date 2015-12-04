<?php
class sysshoppubt_finder_tradeorder{
  public $column_edit = '编辑';
 //    public $column_edit_order = 1;
 //    public function column_edit(&$colList, $list){
 //        foreach($list as $k=>$row)
 //        {
 //            $html = '<a href=\'?app=sysshoppubt&ctl=admin_tradeorder&act=create&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tradeorder_id'].'\'" target="dialog::{title:\''.app::get('sysshoppubt')->_('编辑').'\', width:640, height:420}">'.app::get('sysshoppubt')->_('编辑').'</a>';
 //            $colList[$k] = $html;
 //        }
 //    }

       public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {


            $url = '?app=sysshoppubt&ctl=tradeorder&act=create&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tradeorder_id'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysshoppubt')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }

}