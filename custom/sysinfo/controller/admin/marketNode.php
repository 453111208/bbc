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

class sysinfo_ctl_admin_marketNode extends desktop_controller
{
    var $workground = 'sysinfo.wrokground.theme';
    //行情节点页
    public function index()
    {
        $sysinfoLibNode = kernel::single('sysinfo_article_marketNode');
        $nodeList = $sysinfoLibNode->getNodeList();

        $pagedata['list'] = $nodeList;
        $pagedata['tree_number'] = (is_array($pagedata['list'])) ? count($pagedata['list']) : 0;
        return $this->page("sysinfo/admin/marketNode/index.html",$pagedata);
    }

    //节点修改
    public function edit()
    {
        $nodeId = input::get('node_id');

        $sysinfoLibNode = kernel::single('sysinfo_article_marketNode');
        $nodeInfo = $sysinfoLibNode->editNode($nodeId);
        $pagedata['node'] = $nodeInfo;

        $nodeList = $sysinfoLibNode->getSelectmaps();
        array_unshift($nodeList, array('node_id'=>0, 'step'=>1, 'node_name'=>app::get('sysinfo')->_('---无---')));
        $pagedata['selectmaps'] = $nodeList;

        return $this->page("sysinfo/admin/marketNode/edit.html",$pagedata);
    }

    //添加节点页
    public function add()
    {
        $nodeId = input::get('node_id');
        $sysinfoLibNode = kernel::single('sysinfo_article_marketNode');
        if($nodeId!='')
        {
            //$nodeInfo = $sysinfoLibNode->editNode($nodeId);
            $pagedata['node'] = array('parent_id'=>$nodeId);
        }

        $nodeList = $sysinfoLibNode->getSelectmaps();
        if($nodeList)
        {
            array_unshift($nodeList, array('node_id'=>0, 'step'=>1, 'node_name'=>app::get('sysinfo')->_('---无---')));
        }
        else
        {
            $nodeList = array(array('node_id'=>0, 'step'=>1, 'node_name'=>'---无---'));
        }
        $pagedata['selectmaps'] = $nodeList;

        return $this->page("sysinfo/admin/marketNode/edit.html",$pagedata);
    }
    //保存节点
    public function save()
    {
        $post = input::get('node');
        $this->begin("?app=sysinfo&ctl=admin_marketNode&act=index");
        try
        {
            kernel::single('sysinfo_article_marketNode')->savaNode($post);
            $this->adminlog("添加、编辑行情类目{$post['node_name']}", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("添加、编辑行情类目{$post['node_name']}", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    //删除节点
    public function remove()
    {
        $post = input::get();
        $this->begin("?app=sysinfo&ctl=admin_marketNode&act=index");
        try
        {
            kernel::single('sysinfo_article_marketNode')->deleteNode($post);
            $this->adminlog("删除行情类目 [ID:{$post['node_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("删除行情类目 [ID:{$post['node_id']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    //节点排序保存
    public function update() {
        $post = input::get();

        $this->begin("?app=sysinfo&ctl=admin_marketNode&act=index");
        try
        {
            kernel::single('sysinfo_article_marketNode')->updateNode($post);
            $this->adminlog("更新行情类目的排序", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("更新行情类目的排序", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }




}//End Class
