<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */

class syscontent_ctl_admin_node extends desktop_controller
{
    var $workground = 'site.wrokground.theme';
    //文章节点页
    public function index()
    {
        $syscontentLibNode = kernel::single('syscontent_article_node');
        $nodeList = $syscontentLibNode->getNodeList();

        $pagedata['list'] = $nodeList;
        $pagedata['tree_number'] = (is_array($pagedata['list'])) ? count($pagedata['list']) : 0;
        return $this->page("syscontent/admin/node/index.html",$pagedata);
    }

    //节点修改
    public function edit()
    {
        $nodeId = input::get('node_id');

        $syscontentLibNode = kernel::single('syscontent_article_node');
        $nodeInfo = $syscontentLibNode->editNode($nodeId);
        $pagedata['node'] = $nodeInfo;

        $nodeList = $syscontentLibNode->getSelectmaps();
        array_unshift($nodeList, array('node_id'=>0, 'step'=>1, 'node_name'=>app::get('syscontent')->_('---无---')));
        $pagedata['selectmaps'] = $nodeList;

        return $this->page("syscontent/admin/node/edit.html",$pagedata);
    }

    //添加节点页
    public function add()
    {
        $nodeId = input::get('node_id');
        $syscontentLibNode = kernel::single('syscontent_article_node');
        if($nodeId!='')
        {
            //$nodeInfo = $syscontentLibNode->editNode($nodeId);
            $pagedata['node'] = array('parent_id'=>$nodeId);
        }

        $nodeList = $syscontentLibNode->getSelectmaps();
        if($nodeList)
        {
            array_unshift($nodeList, array('node_id'=>0, 'step'=>1, 'node_name'=>app::get('syscontent')->_('---无---')));
        }
        else
        {
            $nodeList = array(array('node_id'=>0, 'step'=>1, 'node_name'=>'---无---'));
        }
        $pagedata['selectmaps'] = $nodeList;

        return $this->page("syscontent/admin/node/edit.html",$pagedata);
    }
    //保存节点
    public function save()
    {
        $post = input::get('node');
        $this->begin("?app=syscontent&ctl=admin_node&act=index");
        try
        {
            kernel::single('syscontent_article_node')->savaNode($post);
            $this->adminlog("添加、编辑文章类目{$post['node_name']}", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("添加、编辑文章类目{$post['node_name']}", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    //删除节点
    public function remove()
    {
        $post = input::get();
        $this->begin("?app=syscontent&ctl=admin_node&act=index");
        try
        {
            kernel::single('syscontent_article_node')->deleteNode($post);
            $this->adminlog("删除文章类目 [ID:{$post['node_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("删除文章类目 [ID:{$post['node_id']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    //节点排序保存
    public function update() {
        $post = input::get();

        $this->begin("?app=syscontent&ctl=admin_node&act=index");
        try
        {
            kernel::single('syscontent_article_node')->updateNode($post);
            $this->adminlog("更新文章类目的排序", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("更新文章类目的排序", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }




}//End Class
