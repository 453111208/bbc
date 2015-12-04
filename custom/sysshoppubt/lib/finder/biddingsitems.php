<?php
class sysshoppubt_finder_biddingsitems{
	//子列表商品明细
  public $column_edit = '编辑';
    public $column_edit_order = 1;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $html = '  <a href=\'?app=sysshoppubt&ctl=admin_biddingsitems&act=index&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['biddingsitems_id'].'\'" target="dialog::{title:\''.app::get('sysshoppubt')->_('编辑').'\', width:640, height:420}">'.app::get('sysshoppubt')->_('编辑').'</a>';
            $objMdlsupply = app::get('sysshoppubt')->model('biddingsitems');
            $propInfo = $objMdlsupply->getRow("is_def",array('biddingsitems_id'=>$row['biddingsitems_id']));
            $colList[$k] = $html;
        }
    }
}