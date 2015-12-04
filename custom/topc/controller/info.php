<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_info extends topc_controller {

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
        $contentData = app::get('topc')->rpcCall('sysinfo.content.get.list',$params);
        $count = $contentData['articlecount'];
        $contentList = $contentData['articleList'];
        $nodeInfo = $contentData['nodeInfo'];
        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_info@index',$filter),
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
        return $this->page('topc/info/content.html', $pagedata);
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
        $contentInfo = app::get('topc')->rpcCall('sysinfo.content.get.info',$params);
        $pagedata['contentInfo'] = $contentInfo;
        return $this->page('topc/info/contentinfo.html', $pagedata);
    }
    //获取文章节点树
    private function __getCommonInfo()
    {
        $params['fields'] = 'node_id,node_name,parent_id,node_depth,node_path';
        $params['parent_id'] = 0 ;
        $params['orderBy'] = 'order_sort ASC';
        $nodeList = app::get('topc')->rpcCall('sysinfo.node.get.list',$params);
        return $nodeList;
    }
    //获取文章详细页  
    public function getInfo()
    {
        $this->setLayoutFlag('articlesdtl');
        $article_id = input::get("articleId");
        $artList = app::get("sysinfo")->model("article")->getList("*",array('article_id'=>$article_id,"towhere"=>"1"));
        if(!$artList[0]){
            $pagedata["isfb"]="0";//不存在文章
            return $this->page('topc/info/articleInfo.html',$pagedata);
        }
        else{
            if($$artList[0]["towhere"]=="0"){
                $pagedata["isfb"]="1";//文章下架
                return $this->page('topc/info/articleInfo.html',$pagedata);
            }
            else{
                 $pagedata["isfb"]="2";//正常显示
            }
        }
        $node_id=$artList[0]['node_id'];
        $clickRate=$artList[0]['click_rate']+1;
        $sql="update sysinfo_article set click_rate = '".$clickRate."' where article_id = '".$article_id."'";
        app::get('sysinfo')->database()->executeUpdate($sql);
        $nodeList = app::get("sysinfo")->model("article_nodes")->getList("*",array('node_id'=>$node_id));
        $pagedata['nodeList'] = $nodeList[0];
        $pagedata['artList'] = $artList[0];
        return $this->page('topc/info/articleInfo.html', $pagedata);
    }
    //获取文章栏目页  
    public function getNode()
    {
        $this->setLayoutFlag('articlesdtl');
        $pageNow=1;         //默认第一页
        $pageSize=10;       //每页长度
        $node_id = input::get("nodeId");
        if(!empty($_GET['pageNow'])){
            $pageNow=$_GET['pageNow'];
            $node_id=$_GET['nodeId'];
        }
        $sql="SELECT * FROM sysinfo_article where node_id='".$node_id."' and status=1 and towhere=1 order by click_rate desc limit ".($pageNow-1)*$pageSize.",".$pageSize."";   //点击分页显示列表
        $List=app::get("base")->database()->executeQuery($sql)->fetchAll();
        foreach($List as $key=>$value){
            $pagedata['List'][$key] = $value;
        }
        $nodeList = app::get("sysinfo")->model("article_nodes")->getList("*",array('node_id'=>$node_id));
        $artList = app::get("sysinfo")->model("article")->getList("*",array('node_id'=>$node_id,'status'=>1,'towhere'=>1));   
        $pagedata['artList'] = $artList;
        $rowConut=  count($artList);
        $pageCount=  ceil($rowConut/$pageSize);     //总页数
        $pagedata['pageCount'] = $pageCount;
        $pagedata['nodeList'] = $nodeList[0];
        $pagedata['pageNow'] = $pageNow;
        $pagedata['nodeId'] = $node_id;
        return $this->page('topc/info/articleNode.html', $pagedata);
    }

}