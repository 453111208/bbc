<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysnotice_ctl_admin_affiche extends desktop_controller {

    public $workground = 'sysnotice.workground.affiche';

    /**
     * 公告列表
     */
    public function index()
    {
        $parames = array(
        	'title'=>app::get('sysnotice')->_('公告列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                array(
                    'label'=>app::get('sysnotice')->_('添加公告'),
                    'href'=>'?app=sysnotice&ctl=admin_affiche&act=create',
                    'target'=>'dialog::{title:\''.app::get('sysnotice')->_('添加公告').'\',width:800,height:500}'
                ),
            )
        );
        return $this->finder('sysnotice_mdl_notice_item',$parames);
    }
 	
 	/**
     * 公告列表添加
     */
    public function create()
    {
    	$noticeid = input::get('notice_id');
    	//$pagedata = array();
        if( $noticeid )
        {
            $noticeInfo = app::get('sysnotice')->model('notice_item')->getRow("*",array("notice_id"=>$noticeid));
            $pagedata['noticeInfo'] = $noticeInfo;
        }
        $noticetypesql = "select * from sysnotice_notice_type";
        $noticetypeList  = app::get("base")->database()->executeQuery($noticetypesql)->fetchAll();
        $pagedata['noticetypeList'] = $noticetypeList;
        return $this->page('sysnotice/admin/noticeitem.html',$pagedata);
    }

    public function typecreate()
    {
    	$typeid = input::get('type_id');
    	if($typeid)
    	{
    		$noticeInfo = app::get('sysnotice')->model('notice_type')->getRow("*",array("type_id"=>$typeid));
            $pagedata['noticetypeInfo'] = $noticeInfo;
    	}

    	return $this->page('sysnotice/admin/noticetype.html',$pagedata);
    }

 	/**
 	 * 公告类型列表
 	 */
 	public function typeindex()
 	{
 		$parames = array(
 			'title'=>app::get('sysnotice')->_('公告类型列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                array(
                    'label'=>app::get('sysnotice')->_('添加公告类型'),
                    'href'=>'?app=sysnotice&ctl=admin_affiche&act=typecreate',
                    'target'=>'dialog::{title:\''.app::get('sysnotice')->_('添加公告类型').'\',width:800,height:500}'
                ),
            )
        );
        return $this->finder('sysnotice_mdl_notice_type',$parames);
 	}

 	/**
 	 * 公告保存
 	 */
 	public function save()
    {
        $this->begin();
        $data = $_POST;
        $data["notice_time"] = time();
        $itempropMdl = app::get('sysnotice')->model('notice_item');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysnotice')->_('保存成功'));
        
    }

    public function typesave()
    {
    	$this->begin();
        $data = $_POST;
        $data["fabu_time"] = time();
        $itempropMdl = app::get('sysnotice')->model('notice_type');
        $itempropMdl->save($data);
        $this->end(true, app::get('sysnotice')->_('保存成功'));
    }
}
