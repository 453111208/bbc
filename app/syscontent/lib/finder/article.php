<?php
class syscontent_finder_article{

    public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=syscontent&ctl=admin_article&act=update&finder_id='.$_GET['_finder']['finder_id'].'&article_id='.$row['article_id'];
            $target = 'dialog::  {title:\''.app::get('syscontent')->_('文章编辑').'\', width:800, height:500}';
            $title = app::get('syscontent')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }

    public $column_look = "预览";
    public $column_look_order = 2;
    public $column_look_width = 50;
    public function column_look(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            if($row['platform']=='pc')
            {
                $url = url::action('topc_ctl_content@getContentInfo',array('article_id'=>$row['article_id']));
            }
            else
            {
                $url = url::action('topm_ctl_content@getContentInfo',array('article_id'=>$row['article_id']));
            }

            $target = '_blank';
            $title = app::get('syscontent')->_('预览');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }

    public $column_nodename = "节点名称";
    public $column_nodename_order = 3;
    public $column_nodename_width = 120;
    public function column_nodename(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $nodeId = $row['node_id'];
            $objMdlNode = app::get('syscontent')->model('article_nodes');
            $node = $objMdlNode->getRow('node_name,node_id',array('node_id'=>$nodeId));
            $colList[$k] = $node['node_name'];
        }
    }
}
