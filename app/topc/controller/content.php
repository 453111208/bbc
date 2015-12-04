<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_content extends topc_controller {

    public function index()
    {
        $filter = input::get();
        $pageSize = 20;
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $params = array(
            'node_id'   => $filter['node_id'],
            'page_no'   => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields'    =>'article_id,title,node_id,modified',
            'platform'  =>'pc',
        );
        $contentData = app::get('topc')->rpcCall('syscontent.content.get.list',$params);

        $count = $contentData['articlecount'];
        $contentList = $contentData['articleList'];
        $nodeInfo = $contentData['nodeInfo'];
        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_content@index',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['contentList']= $contentList;
        $pagedata['count'] = $count;
        $pagedata['nodeInfo'] = $nodeInfo;
        //获取文章节点树
        $nodeList = $this->__getCommonInfo();

        $pagedata['nodeList'] = $nodeList;
        //echo '<pre>';print_r($pagedata);exit();
        return $this->page('topc/content/content.html', $pagedata);
    }
    //获取文章详细信息
    public function getContentInfo()
    {
        $post = input::get();
        //获取文章节点树
        $nodeList = $this->__getCommonInfo();
        $pagedata['nodeList'] = $nodeList;

        $params = array(
            'article_id' => $post['article_id'],
            'fields' =>'article_id,title,modified,content',
        );

        $contentInfo = app::get('topc')->rpcCall('syscontent.content.get.info',$params);
        $pagedata['contentInfo'] = $contentInfo;
        return $this->page('topc/content/contentinfo.html', $pagedata);
    }
    //获取文章节点树
    private function __getCommonInfo()
    {
        $params['fields'] = 'node_id,node_name,parent_id,node_depth,node_path';
        $params['parent_id'] = 0 ;
        $params['orderBy'] = 'order_sort ASC';
        $nodeList = app::get('topc')->rpcCall('syscontent.node.get.list',$params);
        return $nodeList;
    }

}