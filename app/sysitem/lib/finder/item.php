<?php
class sysitem_finder_item{
    public $column_edit = '商品标题';
    public $column_edit_order = 12;
    public $column_edit_width = 100;


    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = url::action('topc_ctl_item@index',array('item_id'=>$row['item_id']));
            $colList[$k] = "<a href='".$url."' target='_blank' >".$row['title']."</a>";
        }
    }
}
