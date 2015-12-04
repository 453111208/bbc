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

class sysplan_ctl_admin_literary extends desktop_controller
{
    //var $workground = 'sysplan.wrokground.theme';
    //资讯节点页
    var $workground = 'sysplan.wrokground.theme';          //
    public function index()
    {
       // $filter = input::get();
        return $this->finder('sysplan_mdl_literary', 
            array(
            'title'=>app::get('sysplan')->_('成功案例列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysplan')->_('添加成功案例'),
                        'href'=>'?app=sysplan&ctl=admin_literary&act=create','target'=>'dialog::{title:\''.app::get('sysplan')->_('添加成功案例').'\',width:800,height:500}'
                    ),
                    array(
                        'label'=>app::get('sysplan')->_('置顶文章'),
                        'submit'=>'?app=sysplan&ctl=admin_literary&act=dotop',
                    ),
                    array(
                        'label'=>app::get('sysplan')->_('设置热门文章'),
                        'submit'=>'?app=sysplan&ctl=admin_literary&act=dohot',
                    ),
                )
            ));
    }

    public function create()
    {     
        $literaryid = input::get('literary_id');
        if($literaryid)
        {
            $literaryInfo = app::get('sysplan')->model('literary')->getRow("*",array("literary_id"=>$literaryid));
            //后台维护数据时，编辑后，选择框字段显示上次保存类型、分类、目标值！
            $literarycat = app::get('sysplan')->model('literarycat');
            $cat = $literarycat->getRow('literarycat',array('literarycat_id'=>$literaryInfo['literarycat_id']));
            $literaryInfo['literarycat'] = $cat['literarycat'];

            $literaryclass = app::get('sysplan')->model('literaryclass');
            $class = $literaryclass->getRow('literaryclass',array('literaryclass_id'=>$literaryInfo['literaryclass_id']));
            $literaryInfo['literaryclass'] = $class['literaryclass'];

            $literarytarget = app::get('sysplan')->model('literarytarget');
            $target = $literarytarget->getRow('literarytarget',array('literarytarget_id'=>$literaryInfo['literarytarget_id']));
            $literaryInfo['literarytarget'] = $target['literarytarget'];

            $pagedata['literaryInfo'] = $literaryInfo;
        }

        $literarycatList=app::get("sysplan")->model("literarycat")->getList("*");
        foreach ($literarycatList as $key => $value){
            if($value["literarycat"]=="其他"){
                unset($literarycatList[$key]);
            }
        }
        $pagedata["literarycatList"]=$literarycatList;


        $literaryclassList=app::get("sysplan")->model("literaryclass")->getList("*"); 
         foreach ($literaryclassList as $key => $value){
            if($value["literaryclass"]=="其他"){
                unset($literaryclassList[$key]);
            }
        }
        $pagedata["literaryclassList"]=$literaryclassList;

        $literarytargetList=app::get("sysplan")->model("literarytarget")->getList("*");
         foreach ($literarytargetList as $key => $value){
            if($value["literarytarget"]=="其他"){
                unset($literarytargetList[$key]);
            }
        }
        $pagedata["literarytargetList"]=$literarytargetList;

        return $this->page('sysplan/admin/adminaddliterary/addLiterary.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data['pubtime'] = strtotime($data['pubtime']);
        // var_dump($data);
        $data["modified"]= time();
        try {
            $literarycatList=app::get("sysplan")->model("literarycat")->getRow("literarycat_id",array("literarycat"=>"其他"));
            $literaryclassList=app::get("sysplan")->model("literaryclass")->getRow("literaryclass_id",array("literaryclass"=>"其他"));
            $literarytargetList=app::get("sysplan")->model("literarytarget")->getRow("literarytarget_id",array("literarytarget"=>"其他"));
            if($data["literarycat_id"]==""){
                $data["literarycat_id"]=$literarycatList["literarycat_id"];
            }
            if($data["literaryclass_id"]==""){
                $data["literaryclass_id"]=$literaryclassList["literaryclass_id"];
            }
            if($data["literarytarget_id"]==""){
                $data["literarytarget_id"]=$literarytargetList["literarytarget_id"];
            }
        $itempropMdl = app::get('sysplan')->model('literary');
        $itempropMdl->save($data);
        } catch (Exception $e) {
           $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        
        $this->end(true, app::get('sysplan')->_('保存成功'));

    }

     public function _views()
    {
         $mdl_all = app::get('sysplan')->model('literary');
         $upfliter = array('istop' => 1);
         $hotfliter = array('ishot' => 1);
         $all=$mdl_all->count();
         $up=$mdl_all->count($upfliter);
         $hot=$mdl_all->count($hotfliter);
         $subMenu = array(
            0=>array(
                'label'=>app::get('sysplan')->_("全部文章 ( $all )"),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysplan')->_("置顶文章 ( $up )"),
                'optional'=>false,
                'filter'=>array(
                    'istop'=>1,
                ),
            ),
            2=>array(
                'label'=>app::get('sysplan')->_("热门文章 ( $hot )"),
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
        $this->begin('?app=sysplan&ctl=admin_literary&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["literary_id"] as $key => $value) {
                $literary= app::get('sysplan')->model('literary')->getRow("*",array("literary_id"=>$value));
            if($literary["istop"]=="1"){
                $literary["istop"]="0";
                app::get('sysplan')->model('literary')->save($literary);
            }
            else{
                $literary["istop"]="1";
                app::get('sysplan')->model('literary')->save($literary);
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
        $this->begin('?app=sysplan&ctl=admin_literary&act=index');
        $postdata=$_POST;
        //var_dump($postdata);
        try {
             foreach ($postdata["literary_id"] as $key => $value) {
                $literary= app::get('sysplan')->model('literary')->getRow("*",array("literary_id"=>$value));
            if($literary["ishot"]=="1"){
                $literary["ishot"]="0";
                app::get('sysplan')->model('literary')->save($literary);
            }
            else{
                $literary["ishot"]="1";
                app::get('sysplan')->model('literary')->save($literary);
            }
          }
        } catch (Exception $e) {
            $msg=$e->getMessage();
             $this->end(false,$msg);
        }
          $this->end("设置成功");
      }
  
}//End Class


