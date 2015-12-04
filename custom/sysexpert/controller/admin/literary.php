<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


/*
 * @package content
 * @subpackage literary
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */

class sysexpert_ctl_admin_literary extends desktop_controller
{
    //var $workground = 'sysexpert.wrokground.theme';
    //资讯节点页
    var $workground = 'sysexpert.wrokground.theme';          //
    public function index()
    {
       // $filter = input::get();
        return $this->finder('sysexpert_mdl_literary', 
            array(
            'title'=>app::get('sysexpert')->_('名人专家文章列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'use_view_tab'=>true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysexpert')->_('添加名人专家文章'),
                        'href'=>'?app=sysexpert&ctl=admin_literary&act=create','target'=>'dialog::{title:\''.app::get('sysexpert')->_('添加名人专家文章').'\',width:800,height:500}'
                    ),
                    array(
                        'label'=>app::get('sysexpert')->_('置顶文章'),
                        'submit'=>'?app=sysexpert&ctl=admin_literary&act=dotop',
                    ),
                    array(
                        'label'=>app::get('sysexpert')->_('设置热门文章'),
                        'submit'=>'?app=sysexpert&ctl=admin_literary&act=dohot',
                    ),
                )
            ));
    }

    public function create()
    {     
        $literaryid = input::get('literary_id');
        //var_dump($literaryid);
        if($literaryid)
        {
            $literaryInfo = app::get('sysexpert')->model('literary')->getRow("*",array("literary_id"=>$literaryid));
            //后台维护数据时，编辑后，选择框字段显示上次保存专家和类型值！
            $expert = app::get('sysexpert')->model('expert');
            $name = $expert->getRow('name',array('expert_id'=>$literaryInfo['expert_id']));
            $literaryInfo['name'] = $name['name'];

            $literarycat = app::get('sysexpert')->model('literarycat');
            $cat = $literarycat->getRow('literarycat',array('literarycat_id'=>$literaryInfo['literarycat_id']));
            $literaryInfo['literarycat'] = $cat['literarycat'];
            $pagedata['literaryInfo'] = $literaryInfo;
        }
        //$expertInfo=app::get("sysexpert")->model("expert")->getList("*");  //查询语句<==>
        $sql="select * from sysexpert_expert";
        $expertInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();
        $pagedata["expertList"]=$expertInfo;

        $literarycatList=app::get("sysexpert")->model("literarycat")->getList("*"); //取sysexpert>dbschema>literarycat所有值赋给变量$expertInfo
        $pagedata["literarycatList"]=$literarycatList;  //再将数组$expertInfo的值赋值给数组$pagedata。
        //var_dump($pagedata);
        return $this->page('sysexpert/admin/adminaddliterary/addLiterary.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        // $data['pubtime'] = time();
        $data['pubtime'] = strtotime($data['pubtime']);
        $data["modified"]= time();
        try {
            if(!$data["expert_id"]){
                $this->end(false,"请选择发布文章的专家！");
            }
            if(!$data["literarycat_id"]){
                $this->end(false,"请选择文章的类型！");
            }
            $itempropMdl = app::get('sysexpert')->model('literary');
            $itempropMdl->save($data);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
       
        $this->end(true, app::get('sysexpert')->_('保存成功'));

        
    }

     public function _views()
    {
         $mdl_all = app::get('sysexpert')->model('literary');
         $upfliter = array('istop' => 1);
         $hotfliter = array('ishot' => 1);
         $all=$mdl_all->count();
         $up=$mdl_all->count($upfliter);
         $hot=$mdl_all->count($hotfliter);
         $subMenu = array(
            0=>array(
                'label'=>app::get('sysexpert')->_("全部文章 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysexpert')->_("置顶文章 ( $up )"),
                'optional'=>false,
                'filter'=>array(
                    'istop'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('sysexpert')->_("热门文章 ( $hot )"),
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
    $this->begin('?app=sysexpert&ctl=admin_literary&act=index');
    $postdata=$_POST;
    //var_dump($postdata);
    try {
         foreach ($postdata["literary_id"] as $key => $value) {
            $literary= app::get('sysexpert')->model('literary')->getRow("*",array("literary_id"=>$value));
        if($literary["istop"]=="1"){
            $literary["istop"]="0";
            app::get('sysexpert')->model('literary')->save($literary);
        }
        else{
            $literary["istop"]="1";
            app::get('sysexpert')->model('literary')->save($literary);
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
    $this->begin('?app=sysexpert&ctl=admin_literary&act=index');
    $postdata=$_POST;
    //var_dump($postdata);
    try {
         foreach ($postdata["literary_id"] as $key => $value) {
            $literary= app::get('sysexpert')->model('literary')->getRow("*",array("literary_id"=>$value));
        if($literary["ishot"]=="1"){
            $literary["ishot"]="0";
            app::get('sysexpert')->model('literary')->save($literary);
        }
        else{
            $literary["ishot"]="1";
            app::get('sysexpert')->model('literary')->save($literary);
        }
      }
    } catch (Exception $e) {
        $msg=$e->getMessage();
         $this->end(false,$msg);
    }
      $this->end("设置成功");
  }

}//End Class


