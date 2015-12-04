<?php
class sysshoppubt_finder_comment{
	//评论明细
    public $column_edit = '编辑';
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=comment&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['comment_id'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('编辑评论').'\', width:500, height:200}';
            $title = app::get('sysshoppubt')->_('查看评论');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }
}