<?php
class sysinfo_finder_marketArticle{

    public $column_edit = "操作";
    public $column_edit_order = 1;
    public $column_edit_width = 20;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysinfo&ctl=admin_marketArticle&act=update&finder_id='.$_GET['_finder']['finder_id'].'&article_id='.$row['article_id'];
            $target = 'dialog::  {title:\''.app::get('sysinfo')->_('行情审核').'\', width:800, height:500}';
            $title = app::get('sysinfo')->_('审核');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }

//    public $column_look = "预览";
//    public $column_look_order = 2;
//    public $column_look_width = 20;
//    public function column_look(&$colList, $list)
//    {
//        foreach($list as $k=>$row)
//        {
//            if($row['platform']=='pc')
//            {
//                $url = url::action('topc_ctl_info@getContentInfo',array('article_id'=>$row['article_id']));
//            }
//            else
//            {
//                $url = url::action('topm_ctl_content@getContentInfo',array('article_id'=>$row['article_id']));
//            }
//
//            $target = '_blank';
//            $title = app::get('sysinfo')->_('预览');
//            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
//            $colList[$k] = $button;
//        }
//    }

//    public $column_nodename = "节点名称";
//    public $column_nodename_order = 3;
//    public $column_nodename_width = 50;
//    public function column_nodename(&$colList, $list)
//    {
//        foreach($list as $k=>$row)
//        {
//            $nodeId = $row['node_id'];
//            $objMdlNode = app::get('sysinfo')->model('marketNode');
//            $node = $objMdlNode->getRow('node_name,node_id',array('node_id'=>$nodeId));
//            $colList[$k] = $node['node_name'];
//        }
//    }
}
