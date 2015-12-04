<?php
class sysopen_finder_keys{
    public $column_edit = '操作';
    public $column_edit_keys = 1;
    public $column_edit_width = 60;

    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            if($row['contact_type'] == 'applyforopen')
            {
                $url = '?app=sysopen&ctl=admin_shop&act=doApply&finder_id=' . $_GET['_finder']['finder_id'] . '&shop_id=' . $row['shop_id'];
                $target = 'dialog::{title:\''.app::get('systrade')->_('审核').'\',width:300, height:200}';
                $title = app::get('systrade')->_('平台审核');
                $colList[$k] = '<a href="'.$url.'" target="'.$target.'">' . $title . '</a>';
            }
            elseif($row['contact_type'] == 'notallowopen')
            {
                $url = '?app=sysopen&ctl=admin_shop&act=doDelete&finder_id=' . $_GET['_finder']['finder_id'] . '&shop_id=' . $row['shop_id'];
                $title = app::get('systrade')->_('删除记录');
                $colList[$k] = '<a href="'.$url.'">' . $title . '</a>';
            }
            else
            {
                $url = '?app=sysopen&ctl=admin_shop&act=doSuspend&finder_id=' . $_GET['_finder']['finder_id'] . '&shop_id=' . $row['shop_id'];
                $title = app::get('systrade')->_('关闭商家权限');
                $colList[$k] = '<a href="'.$url.'">' . $title . '</a>';
            }
        }
    }
}

