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

class syscase_ctl_admin_essaycat extends desktop_controller
{
    var $workground = 'syscase.wrokground.theme';
    public function index()
    {
        return $this->finder('syscase_mdl_essaycat', 
            array(
            'title'=>app::get('syscase')->_('解决方案文章类型列表'),
            // 'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('syscase')->_('添加文章类型'),
                        'href'=>'?app=syscase&ctl=admin_essaycat&act=create','target'=>'dialog::{title:\''.app::get('syscase')->_('添加名人专家文章').'\',width:800,height:500}'
                    ),
                )
            ));
    }

    public function create()
    {     
        $essaycatid = input::get('essaycat_id');
        //var_dump($essaycatid);
        if($essaycatid)
            {
                $essaycatInfo = app::get('syscase')->model('essaycat')->getRow("*",array("essaycat_id"=>$essaycatid));
                $pagedata["essaycatInfo"] = $essaycatInfo;
            }
        return $this->page('syscase/admin/adminaddessaycat/addessaycat.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $itempropMdl = app::get('syscase')->model('essaycat');
        $itempropMdl->save($data);
        $this->end(true, app::get('syscase')->_('保存成功'));

        
    }

  
}//End Class


