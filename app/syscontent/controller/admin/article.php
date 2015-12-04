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

class syscontent_ctl_admin_article extends desktop_controller
{
    var $workground = 'site.wrokground.theme';
    var $platforms = array('pc'=>'电脑端','wap'=>'移动端');
    public function index() 
    {
        $filter = input::get();
        return $this->finder('syscontent_mdl_article', array(
            'title'=>app::get('syscontent')->_('文章列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'base_filter' =>array('node_id' => $filter['filter']['node_id']),
            'actions'=>array(
                    array(
                        'label'=>app::get('syscontent')->_('添加文章'),
                        'href'=>'?app=syscontent&ctl=admin_article&act=add','target'=>'dialog::{title:\''.app::get('syscontent')->_('添加文章').'\',width:800,height:500}'
                    ),
                )
            ));
    }
    //文章添加
    public function add()
    {
        $nodeId = input::get('node_id');
        if(!empty($nodeId))
        {
            $pagedata['article']['node_id'] = $nodeId;
        }

        $syscontentLibNode = kernel::single('syscontent_article_node');
        $nodeList = $syscontentLibNode->getNodeList();
        foreach ($nodeList as $key => $value)
        {
            $selectmaps[$key]['node_id'] = $value['node_id'];
            $selectmaps[$key]['step'] = $value['node_depth'];
            $selectmaps[$key]['node_name'] = $value['node_name'];
        }

        $pagedata['selectmaps'] = $selectmaps;
        $pagedata['platform_options'] = $this->platforms;
        return $this->page('syscontent/admin/article/editor.html',$pagedata);
    }

    //文章编辑
    public function update()
    {
        $postData = input::get();
        //$syscontentLibArticle = kernel::single('syscontent_article_article');
        $syscontentLibNode = kernel::single('syscontent_article_node');
        $nodeList = $syscontentLibNode->getNodeList();
        foreach ($nodeList as $key => $value)
        {
            $selectmaps[$key]['node_id'] = $value['node_id'];
            $selectmaps[$key]['step'] = $value['node_depth'];
            $selectmaps[$key]['node_name'] = $value['node_name'];
        }
        $pagedata['selectmaps'] = $selectmaps;

        $params['article_id'] = $postData['article_id'];
        $params['fields'] = 'article_id,title,node_id,modified,content,platform,article_logo';
        $articleInfo = app::get('syscontent')->rpcCall('syscontent.content.get.info',$params);;

        $pagedata['platform_options'] = $this->platforms;
        $pagedata['article'] =  $articleInfo;

        return $this->page('syscontent/admin/article/editor.html',$pagedata);
    }


    //文章保存
    public function save()
    {
        $post = input::get('article');

        $this->begin("?app=syscontent&ctl=admin_article&act=index");
        try
        {
            kernel::single('syscontent_article_article')->saveContent($post);
            $this->adminlog("添加文章{$post['title']}", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("添加文章{$post['title']}", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);

    }
}
