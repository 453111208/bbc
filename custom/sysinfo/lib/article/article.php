<?php


/*
 * @package content
 * @subpackage article
 * @copyright Copyright (c) 2015, shopex. inc
 * @author gongjiapeng@shopex.cn
 * @license 
 */
class sysinfo_article_article
{
    //保存资讯
    public function saveContent($data)
    {
        $nodeMdl = app::get('sysinfo')->model('article_nodes');
        $articleMdl = app::get('sysinfo')->model('article');
        $contentData = $this->__checkdata($data);
        $db = app::get('sysinfo')->database();
        $db->beginTransaction();
        try
        {
            $articleMdl->save($contentData['article']);
            $nodeMdl->save($contentData['node']);
            $db->commit();
        }
        catch(LogicException $e)
        {
            $db->rollback();
            throw $e;
        }
    }
    private function __checkdata($contentData)
    {
        $nodeMdl = app::get('sysinfo')->model('article_nodes');
        $validator = validator::make(
            ['title' => $contentData['title'] , 'node_id' => $contentData['node_id']],
            ['title' => 'required'            , 'node_id' => 'required'],
            ['title' => '资讯标题不能为空'      , 'node_id' => '资讯节点id不能为空']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                throw new LogicException( $error[0] );
            }
        }

        $nodeInfo = $nodeMdl->getRow('parent_id',array('node_id'=>$contentData['node_id']));
        if($nodeInfo['parent_id']==0)
        {
            throw new \LogicException('请选择二级分类!');
        }
        $data['node']['node_id'] = $contentData['node_id'];
        $data['article']['content'] = $contentData['content'];
        $data['article']['node_id'] = $contentData['node_id'];
        $data['article']['title'] = $contentData['title'];
        $data['article']['platform'] = $contentData['platform'];
        $data['article']['article_logo'] = $contentData['article_logo'];
        $data['article']['modified'] = time();
        $data['article']['platform'] = $contentData['platform'];
        if($contentData['article_id'])
        {
            $data['article']['article_id'] = $contentData['article_id'];
        }
        return $data;
    }

    //根据资讯id获取信息
    public function getArticleInfo($params)
    {
        $articleMdl = app::get('sysinfo')->model('article');
        if($params['article_id']=='')
        {
            throw new \LogicException('资讯id不能为空!');
        }
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $articleInfo = $articleMdl->getRow($params['fields'],array('article_id'=>$params['article_id']));
        return $articleInfo;
    }

    public function getArticleList($params)
    {
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        if(!$params['platform'])
        {
            $params['platform'] = 'pc';
        }
      /*  if($params['node_id']=='')
        {
            throw new \LogicException('节点id不能为空!');
        }*/
        $params['page_no'] = $params['page_no'] ? $params['page_no']:'0';
        $params['page_size'] = $params['page_size'] ? $params['page_size']:'-1';
        $articleMdl = app::get('sysinfo')->model('article');
        $nodeMdl = app::get('sysinfo')->model('article_nodes');
        $filter = array('node_id'=>$params['node_id'],'platform'=>$params['platform']);
        $nodeInfo = $nodeMdl->getRow('node_id,node_name',$filter);

        $orderBy    = $params['orderBy'] ? $params['orderBy'] : 'modified DESC';
        $aData = $articleMdl->getList($params['fields'], $filter,$params['page_no'],$params['page_size'], $orderBy);
        $articleCount = $articleMdl->count($filter);
        $articleData = array(
                'articleList'  => $aData,
                'articlecount' => $articleCount,
                'nodeInfo'     =>$nodeInfo,
            );

        return $articleData;
    }
}