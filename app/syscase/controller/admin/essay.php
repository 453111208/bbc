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
    var $workground = 'syscase.wrokground.theme';
    public function index()
    {
       // $filter = input::get();
        return $this->finder('syscase_mdl_essay', 
            array(
            'title'=>app::get('syscase')->_('解决方案文章列表'),
            'use_buildin_set_tag' => true,
            'use_buildin_filter' => true,
            'actions'=>array(
                    array(
                        'label'=>app::get('syscase')->_('添加文章'),
                        'href'=>'?app=syscase&ctl=admin_essay&act=create','target'=>'dialog::{title:\''.app::get('syscase')->_('添加文章').'\',width:800,height:500}'
                    ),
                )
            ));
    }
    public function create()
    {     
        $essayid = input::get('essay_id');
        //var_dump($essayid);
        if($essayid)
            {
                $essayInfo = app::get('syscase')->model('essay')->getRow("*",array("essay_id"=>$essayid));
                $pagedata['essayInfo'] = $essayInfo;
            }
        $essaycatlist=app::get("syscase")->model("essaycat")->getList("*");
        $pagedata["essaycatlist"]=$essaycatlist;
        return $this->page('syscase/admin/adminaddessay/addessay.html',$pagedata);
    }
    public function save()
    {
        $this->begin();
        $data = $_POST;
        $data['pubtime'] = time();
        $essay = app::get('syscase')->model('essay');
        $essay->save($data);
        $this->end(true, app::get('syscase')->_('保存成功'));

        
    }

  
}//End Class


