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
                    array(
                'label'=>app::get('sysinfo')->_('批量审核'),
                'icon' => 'download.gif',
                'submit' => '?app=sysinfo&ctl=admin_article&act=allpass',
                'confirm' => app::get('sysitem')->_('确定要审核选中商品？'),
                    ),
                    array(
                        'label'=>app::get('sysinfo')->_('置顶文章'),
                        'submit'=>'?app=sysinfo&ctl=admin_article&act=dotop',
                    ),
                    array(
                        'label'=>app::get('sysinfo')->_('设置热门文章'),
                        'submit'=>'?app=sysinfo&ctl=admin_article&act=dohot',
                    ),
                )
            ));
    }

    //批量审核
    public function allpass(){
        $this->begin('?app=sysinfo&ctl=admin_article&cat=index');
        $postdata = $_POST;
        if($postdata['item_id'][0] == '_ALL_')  unset($postdata);
        $ojbMdlItem = app::get('sysinfo')->model('article');

        $updata['status'] = 1;
        $updata['modified'] = time();
        $result = $ojbMdlItem->update($updata,$postdata);
        if($result)
        {
            $msg = app::get('sysinfo')->_('商品审核成功');
        }
        else
        {
            $msg = app::get('sysinfo')->_('商品审核失败');
        }
        $this->end($result,$msg);
    }

    //资讯添加
    public function add()
    {
        $article_id = input::get('article_id');
        if($article_id){
        $articles = app::get('sysinfo')->model('article');
        $artnode= app::get('sysinfo')->model('article_nodes');
        $articleinfo = $articles->getRow('*',array('article_id'=>$article_id));
        $pagedata['articleinfo'] = $articleinfo; 
        $pagedata['artnode'] = $artnode->getRow('*',array('node_id'=>$articleinfo['node_id']));
        }
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
        $articlecatlist=app::get("sysinfo")->model("article_nodes")->getlist("*");
        $pagedata["articlecatlist"]=$articlecatlist;

        $pagedata['selectmaps'] = $selectmaps;
        $pagedata['platform_options'] = $this->platforms;
        return $this->page('sysinfo/admin/article/editor.html',$pagedata);
    }
    //资讯编辑
    /*public function update()
    {
        $postData = input::get();
        $rows = "*";
        $article_id = $postData['article_id'];
        $articleInfo = app::get("sysinfo")->model("article")->getList("*",array('article_id'=>$article_id));
        $pagedata['platform_options'] = $this->platforms;
        $pagedata['article'] =  $articleInfo[0];
        $node_id=$articleInfo[0]['node_id'];
        $node = app::get('sysinfo')->model('article_nodes');
        $nodeName =$node->getList($rows,array('node_id'=>$node_id));
        $parent_id=$nodeName[0]['parent_id'];
        $parentId=$node->getList($rows,array('node_id'=>$parent_id));
        $pagedata['nodeName'] =$nodeName[0]['node_name'];
        $pagedata['parentName'] =$parentId[0]['node_name'];
        return $this->page('sysinfo/admin/article/infoeditor.html',$pagedata);
    }*/
    //资讯保存
    public function save()
    {
        $post = input::get('article');

        if(!$post['pubtime']){
            $post['pubtime']=time();
        }
        else{
            $post['pubtime']=strtotime($post['pubtime']);
        }
        if(!$post["articlecat_id"]){
            $post["articlecat_id"]=0;
        }
        $post['modified']=time();
        if(!$post["essaycat_id"]){
            $post["essaycat_id"]=0;
        }
        if($post['article_id']){
            $this->begin("?app=sysinfo&ctl=admin_article&act=index");
        $articles = app::get('sysinfo')->model('article');
        $articleinfo = $articles->getRow('*',array('article_id'=>$post['article_id']));
        try {
            $articles->update($post,$articleinfo);
        } catch (Exception $e) {
            $msg=$e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
        
        }else{  
        $post['towhere']=1;
        $post['status']=0;
        $post['source']='平台方';
        $this->begin("?app=sysinfo&ctl=admin_article&act=index");
        $article = app::get('sysinfo')->model('article');
        try {
            $article->save($post);
        } catch (Exception $e) {
        }
        
        $this->end(true);
        }
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

    public function _views()
    {
         $mdl_all = app::get('sysinfo')->model('article');
         $upfliter = array('istop' => 1);
         $hotfliter = array('ishot' => 1);
         $all=$mdl_all->count();
         $up=$mdl_all->count($upfliter);
         $hot=$mdl_all->count($hotfliter);
         $subMenu = array(
            0=>array(
                'label'=>app::get('sysinfo')->_("全部文章 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysinfo')->_("置顶文章 ( $up )"),
                'optional'=>false,
                'filter'=>array(
                    'istop'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('sysinfo')->_("热门文章 ( $hot )"),
                'optional'=>false,
                'filter'=>array(
                    'ishot'=>1,
                ),
            ),
            );
          return $subMenu;
    }

      public function dotop()
      {
        $this->begin('?app=sysinfo&ctl=admin_article&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["article_id"] as $key => $value) {
                $article= app::get('sysinfo')->model('article')->getRow("*",array("article_id"=>$value));
            if($article["istop"]=="1"){
                $article["istop"]="0";
                app::get('sysinfo')->model('article')->save($article);
            }
            else{
                $article["istop"]="1";
                app::get('sysinfo')->model('article')->save($article);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }

      public function dohot()
      {
        $this->begin('?app=sysinfo&ctl=admin_article&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["article_id"] as $key => $value) {
                $article= app::get('sysinfo')->model('article')->getRow("*",array("article_id"=>$value));
            if($article["ishot"]=="1"){
                $article["ishot"]="0";
                app::get('sysinfo')->model('article')->save($article);
            }
            else{
                $article["ishot"]="1";
                app::get('sysinfo')->model('article')->save($article);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }
      
}
