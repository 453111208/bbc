<?php
class syscategory_finder_item_prop {

    public $column_edit = '编辑';
    public $column_edit_order = 1;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $html = '  <a href=\'?app=syscategory&ctl=admin_itemprop&act=create&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['item_prop_id'].'\'" target="dialog::{title:\''.app::get('syscategory')->_('编辑').'\', width:640, height:420}">'.app::get('syscategory')->_('编辑').'</a>';
            $objMdlProps = app::get('syscategory')->model('item_prop');
            $propInfo = $objMdlProps->getRow("*",array('item_prop_id'=>$row['item_prop_id']));
           

            $colList[$k] = $html;
        }
    }
}

