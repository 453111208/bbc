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

class sysplan_ctl_admin_literaryclass extends desktop_controller
{
    //var $workground = 'sysplan.wrokground.theme';
    //资讯节点页
    var $workground = 'sysplan.wrokground.theme';
    public function index()
    {
       // $filter = input::get();
        return $this->finder('sysplan_mdl_literaryclass', array(
            'title'=>app::get('sysplan')->_('成功案例分类列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysplan')->_('添加成功案例分类'),
                        'href'=>'?app=sysplan&ctl=admin_literaryclass&act=create','target'=>'dialog::{title:\''.app::get('sysplan')->_('添加成功案例分类').'\',width:800,height:500}'
                    ),
                )
            ));
    }

    public function create()
    {
        $literaryclassid = input::get('literaryclass_id');
        if( $literaryclassid )
        {
            $literaryclassInfo = app::get('sysplan')->model('literaryclass')->getRow("*",array("literaryclass_id"=>$literaryclassid));
            $pagedata['literaryclassInfo'] = $literaryclassInfo;
        }

        return $this->page('sysplan/admin/adminaddliteraryclass/addLiteraryclass.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data["modified"] = time();
        $itempropMdl = app::get('sysplan')->model('literaryclass');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysplan')->_('保存成功'));
        
    }
  
}//End Class
