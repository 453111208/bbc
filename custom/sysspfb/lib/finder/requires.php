<?php
class sysspfb_finder_requires {

    public $column_edit = '编辑';
    public $column_edit_order = 1;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $html = '  <a href=\'?app=sysspfb&ctl=admin_requires&act=addRequire&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['supply_id'].'\'" target="dialog::{title:\''.app::get('sysspfb')->_('编辑').'\', width:640, height:420}">'.app::get('sysspfb')->_('编辑').'</a>';
            $objMdlsupply = app::get('sysspfb')->model('requireInfo');
            $propInfo = $objMdlsupply->getRow("is_def",array('require_id'=>$row['require_id']));
            $colList[$k] = $html;
        }
    }
}