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

class sysexpert_ctl_admin_literarycat extends desktop_controller
{
    //var $workground = 'sysexpert.wrokground.theme';
    //资讯节点页
    var $workground = 'sysexpert.wrokground.theme';
    public function index()
    {
       // $filter = input::get();
        return $this->finder('sysexpert_mdl_literarycat', array(
            'title'=>app::get('sysexpert')->_('名人专家文章类型列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('sysexpert')->_('添加名人专家文章类型'),
                        'href'=>'?app=sysexpert&ctl=admin_literarycat&act=create','target'=>'dialog::{title:\''.app::get('sysexpert')->_('添加名人专家文章类型').'\',width:800,height:500}'
                    ),
                )
            ));
    }

    public function create()
    {
        $literarycatid = input::get('literarycat_id');
        if( $literarycatid )
        {
            $literarycatInfo = app::get('sysexpert')->model('literarycat')->getRow("*",array("literarycat_id"=>$literarycatid));
            $pagedata['literarycatInfo'] = $literarycatInfo;
        }

        return $this->page('sysexpert/admin/adminaddliterarycat/addLiterarycat.html',$pagedata);
    }
    
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data["modified"] = time();
        $itempropMdl = app::get('sysexpert')->model('literarycat');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysexpert')->_('保存成功'));
        
    }
  
}//End Class
