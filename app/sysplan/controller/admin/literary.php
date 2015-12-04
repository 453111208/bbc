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
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysplan')->_('添加成功案例'),
                        'href'=>'?app=sysplan&ctl=admin_literary&act=create','target'=>'dialog::{title:\''.app::get('sysplan')->_('添加成功案例').'\',width:800,height:500}'
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

  
}//End Class


