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
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysexpert')->_('添加名人专家文章'),
                        'href'=>'?app=sysexpert&ctl=admin_literary&act=create','target'=>'dialog::{title:\''.app::get('sysexpert')->_('添加名人专家文章').'\',width:800,height:500}'
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
            $pagedata['literaryInfo'] = $literaryInfo;
        }
        //$expertInfo=app::get("sysexpert")->model("expert")->getList("*");  //查询语句<==>
        $sql="select * from sysexpert_expert";
        $expertInfo = app::get("base")->database()->executeQuery($sql)->fetchAll();

        $pagedata["expertList"]=$expertInfo;

        $expertInfo=app::get("sysexpert")->model("literarycat")->getList("*"); //取sysexpert>dbschema>literarycat所有值赋给变量$expertInfo
        $pagedata["literarycatList"]=$expertInfo;  //再将数组$expertInfo的值赋值给数组$pagedata。
        //var_dump($pagedata);
        return $this->page('sysexpert/admin/adminaddliterary/addliterary.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data['pubtime'] = time();
        $data["modified"]= time();
        $itempropMdl = app::get('sysexpert')->model('literary');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysexpert')->_('保存成功'));

        
    }

  
}//End Class


