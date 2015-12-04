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

class sysinfo_ctl_admin_article extends desktop_controller
{
    var $workground = 'sysinfo.wrokground.theme';
    var $platforms = array('pc'=>'电脑端','wap'=>'移动端');
    public function index() 
    {
        $filter = input::get();
        return $this->finder('sysinfo_mdl_article', array(
            'title'=>app::get('sysinfo')->_('资讯列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'base_filter' =>array('node_id' => $filter['filter']['node_id']),
            'actions'=>array(
                    array(
                        'label'=>app::get('sysinfo')->_('添加资讯'),
                        'href'=>'?app=sysinfo&ctl=admin_article&act=add','target'=>'dialog::{title:\''.app::get('sysinfo')->_('添加资讯').'\',width:800,height:500}'
                    ),
                )
            ));
    }
    //资讯添加
    public function add()
    {
        $nodeId = input::get('node_id');
        if(!empty($nodeId))
        {
            $pagedata['article']['node_id'] = $nodeId;
        }
        $sysinfoLibNode = kernel::single('sysinfo_article_node');
        $nodeList = $sysinfoLibNode->getNodeList();
        foreach ($nodeList as $key => $value)
        {
            $selectmaps[$key]['node_id'] = $value['node_id'];
            $selectmaps[$key]['step'] = $value['node_depth'];
            $selectmaps[$key]['node_name'] = $value['node_name'];
        }
        $pagedata['selectmaps'] = $selectmaps;
        $pagedata['platform_options'] = $this->platforms;
        return $this->page('sysinfo/admin/article/editor.html',$pagedata);
    }
    //资讯编辑
    public function update()
    {
        $postData = input::get();
        $rows = "*";
        $params['article_id'] = $postData['article_id'];
        $params['fields'] = 'article_id,title,node_id,modified,content,platform,article_logo';
        $articleInfo = app::get('sysinfo')->rpcCall('sysinfo.content.get.info',$params);
        $pagedata['platform_options'] = $this->platforms;
        $pagedata['article'] =  $articleInfo;
        $node_id=$articleInfo['node_id'];
        $node = app::get('sysinfo')->model('article_nodes');
        $nodeName =$node->getList($rows,array('node_id'=>$node_id));
        $parent_id=$nodeName[0]['parent_id'];
        $parentId=$node->getList($rows,array('node_id'=>$parent_id));
        $pagedata['nodeName'] =$nodeName[0]['node_name'];
        $pagedata['parentName'] =$parentId[0]['node_name'];
        return $this->page('sysinfo/admin/article/infoeditor.html',$pagedata);
    }
    //资讯保存
    public function save()
    {
        $post = input::get('article');
        $post['pubtime']=time();
        $post['modified']=time();
        $post['status']=1;
        $post['source']='平台方';
        $this->begin("?app=sysinfo&ctl=admin_article&act=index");
        $article = app::get('sysinfo')->model('article');
        $article->save($post);
        $this->end(true);
    }
     //资讯审核
    public function infoCheck()
    {
        $post = input::get('article');
        $this->begin("?app=sysinfo&ctl=admin_article&act=index");
        try
        {
            if($post['status']){
                $sql = "UPDATE sysinfo_article set status=1 where article_id=".$post['article_id']."";
                app::get('sysinfo')->database()->executeUpdate($sql);
            }
            if($post['towhere']){
                $sql = "UPDATE sysinfo_article set towhere=1 where article_id=".$post['article_id']."";
                app::get('sysinfo')->database()->executeUpdate($sql);
            }
        }
        catch(Exception $e)
        {
            $this->adminlog("添加资讯{$post['title']}", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }
}
