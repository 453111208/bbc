<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


/*
 * @package content
 * @subpackage essay
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */

class syscase_ctl_admin_essay extends desktop_controller
{
    //var $workground = 'syscase.wrokground.theme';
    //资讯节点页
    var $workground = 'syscase.wrokground.theme';          //
    public function index()
    {
       // $filter = input::get();
        return $this->finder('syscase_mdl_essay', 
            array(
            'title'=>app::get('syscase')->_('解决方案列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'use_view_tab'=>true,
            'actions'=>array(
                    array(
                        'label'=>app::get('syscase')->_('添加解决方案'),
                        'href'=>'?app=syscase&ctl=admin_essay&act=create','target'=>'dialog::{title:\''.app::get('syscase')->_('添加解决方案').'\',width:800,height:500}'
                    ),
                    array(
                        'label'=>app::get('syscase')->_('置顶文章'),
                        'submit'=>'?app=syscase&ctl=admin_essay&act=dotop',
                    ),
                    array(
                        'label'=>app::get('syscase')->_('设置热门文章'),
                        'submit'=>'?app=syscase&ctl=admin_essay&act=dohot',
                    ),
                )
            ));
    }

    public function create()
    {     
        $essayid = input::get('essay_id');
        if($essayid)
        {
            $essayInfo = app::get('syscase')->model('essay')->getRow("*",array("essay_id"=>$essayid));
            //后台维护数据时，编辑后，选择框字段显示上次保存类型值！
            $essaycat = app::get('syscase')->model('essaycat');
            $cat = $essaycat->getRow('essaycat',array('essaycat_id'=>$essayInfo['essaycat_id']));
            $essayInfo['essaycat'] = $cat['essaycat'];
            $pagedata['essayInfo'] = $essayInfo;
        }

        $essaycatlist=app::get("syscase")->model("essaycat")->getlist("*");
        foreach ($essaycatlist as $key => $value){
            if($value["essaycat"]=="其他"){
                unset($essaycatlist[$key]);
            }
        }
        $pagedata["essaycatlist"]=$essaycatlist;

        return $this->page('syscase/admin/adminaddessay/addessay.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data['pubtime'] = strtotime($data['pubtime']);
        // var_dump($data);
        $data["modified"]= time();
        try {
            $essaycatlist=app::get("syscase")->model("essaycat")->getRow("essaycat_id",array("essaycat"=>"其他"));
            if($data["essaycat_id"]==""){
                $data["essaycat_id"]=$essaycatlist["essaycat_id"];
            }
        $itempropMdl = app::get('syscase')->model('essay');
        $itempropMdl->save($data);
        } catch (Exception $e) {
           $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        
        $this->end(true, app::get('syscase')->_('保存成功'));     
    }

     public function _views()
    {
         $mdl_all = app::get('syscase')->model('essay');
         $upfliter = array('istop' => 1);
         $hotfliter = array('ishot' => 1);
         $all=$mdl_all->count();
         $up=$mdl_all->count($upfliter);
         $hot=$mdl_all->count($hotfliter);
         $subMenu = array(
            0=>array(
                'label'=>app::get('syscase')->_("全部文章 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('syscase')->_("置顶文章 ( $up )"),
                'optional'=>false,
                'filter'=>array(
                    'istop'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('syscase')->_("热门文章 ( $hot )"),
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
        $this->begin('?app=syscase&ctl=admin_essay&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["essay_id"] as $key => $value) {
                $essay= app::get('syscase')->model('essay')->getRow("*",array("essay_id"=>$value));
            if($essay["istop"]=="1"){
                $essay["istop"]="0";
                app::get('syscase')->model('essay')->save($essay);
            }
            else{
                $essay["istop"]="1";
                app::get('syscase')->model('essay')->save($essay);
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
        $this->begin('?app=syscase&ctl=admin_essay&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["essay_id"] as $key => $value) {
                $essay= app::get('syscase')->model('essay')->getRow("*",array("essay_id"=>$value));
            if($essay["ishot"]=="1"){
                $essay["ishot"]="0";
                app::get('syscase')->model('essay')->save($essay);
            }
            else{
                $essay["ishot"]="1";
                app::get('syscase')->model('essay')->save($essay);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }
  
}//End Class


