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

class sysplan_ctl_admin_literarytarget extends desktop_controller
{
    //var $workground = 'sysplan.wrokground.theme';
    //资讯节点页
    var $workground = 'sysplan.wrokground.theme';
    public function index()
    {
       // $filter = input::get();
        return $this->finder('sysplan_mdl_literarytarget', array(
            'title'=>app::get('sysplan')->_('成功案例目标列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysplan')->_('添加成功案例目标'),
                        'href'=>'?app=sysplan&ctl=admin_literarytarget&act=create','target'=>'dialog::{title:\''.app::get('sysplan')->_('添加成功案例目标').'\',width:800,height:500}'
                    ),
                )
            ));
    }

    public function create()
    {
        $literarytargetid = input::get('literarytarget_id');
        if( $literarytargetid )
        {
            $literarytargetInfo = app::get('sysplan')->model('literarytarget')->getRow("*",array("literarytarget_id"=>$literarytargetid));
            $pagedata['literarytargetInfo'] = $literarytargetInfo;
        }

        return $this->page('sysplan/admin/adminaddliterarytarget/addLiterarytarget.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data["modified"] = time();
        $itempropMdl = app::get('sysplan')->model('literarytarget');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysplan')->_('保存成功'));
        
    }
  
}//End Class
