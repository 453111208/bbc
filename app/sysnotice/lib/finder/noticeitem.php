<?php
class sysnotice_finder_noticeitem{
	public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysnotice&ctl=admin_affiche&act=create&finder_id='.$_GET['_finder']['finder_id'].'&notice_id='.$row['notice_id'];
            $target = 'dialog::  {title:\''.app::get('sysnotice')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysnotice')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }

    public $column_edit1 = "公告类型";
    public $column_edit_order1 = 3;
    public $column_edit_width1 = 50;
    public function column_edit1(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
        	$noticetypeid=$row["type_id"];
        	$typeinfo=app::get("sysnotice")->model("notice_type")->getRow("*",array("type_id" => $noticetypeid)); 
            //$title = app::get('sysnotice')->_('类型');
            $button = '<span>'.$typeinfo["type_name"].'</span>';

            $colList[$k] = $button;
        }
    }

}







