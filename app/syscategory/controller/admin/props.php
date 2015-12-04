<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syscategory_ctl_admin_props extends desktop_controller {

    public $workground = 'syscategory.workground.category';

    /**
     * 属性管理列表
     */
    public function index()
    {
        return $this->finder(
            'syscategory_mdl_props',
            array(
                'title'=>app::get('syscategory')->_('属性列表'),
                'actions'=>array(
                    array(
                        'label'=>app::get('syscategory')->_('添加属性'),
                        'href'=>'?app=syscategory&ctl=admin_props&act=create','target'=>'dialog::{title:\''.app::get('syscategory')->_('添加属性').'\',width:600,height:420}'
                    ),
                    array(
                        'label'=>'属性图片设置',
                        'href'=>'?app=syscategory&ctl=admin_props&act=settingDefaultPic','target'=>'dialog::{title:\''.app::get('syscategory')->_('属性图片设置').'\',width:400,height:200}'
                    ),
                ),
                'use_view_tab' => true,
            )
        );
    }

    public function _views(){
        $objMdlProps = app::get('syscategory')->model('props');
        $sub_menu = array(
            0=>array('label'=>app::get('syscategory')->_('全部'),'optional'=>false,'filter'=>array('disabled'=>0)),
            1=>array('label'=>app::get('syscategory')->_('销售属性'),'optional'=>true,'filter'=>array('prop_type'=>array('spec'),'disabled'=>0)),
            2=>array('label'=>app::get('syscategory')->_('自然属性'),'optional'=>false,'filter'=>array('prop_type'=>array('nature'),'disabled'=>0)),
        );

        if(isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;

        foreach($sub_menu as $k=>$v){
            if($v['optional']==false){
                $show_menu[$k] = $v;
                if(is_array($v['filter'])){
                    $v['filter'] = array_merge(array(),$v['filter']);
                }else{
                    $v['filter'] = array();
                }
                $show_menu[$k]['filter'] = $v['filter']?$v['filter']:null;
                if($k==$_GET['view']){
                    $show_menu[$k]['newcount'] = true;
                    $show_menu[$k]['addon'] = $objMdlProps->count($v['filter']);
                }
                $show_menu[$k]['href'] = '?app=syscategory&ctl=admin_props&act=index&view='.($k).(isset($_GET['optional_view'])?'&optional_view='.$_GET['optional_view'].'&view_from=dashboard':'');
            }elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view']){
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;
    }

    /**
     * 添加属性页面
     */
    public function create($propId)
    {
        if( $propId )
        {
            $propInfo = app::get('syscategory')->model('props')->getPropRow($propId);
            $pagedata['propInfo'] = $propInfo;
        }
        return $this->page('syscategory/admin/props.html', $pagedata);
    }

    public function saveProp()
    {
        $this->begin();
        $data = $_POST;
        if( empty($data['prop_id']) )
        {
            $flag = kernel::single('syscategory_data_props')->add($data,$msg);
            $this->adminlog("添加商品属性[分类ID:{$data['prop_id']}]", $flag ? 1 : 0);
        }
        else
        {
            $flag = kernel::single('syscategory_data_props')->update($data,$msg);
            $this->adminlog("编辑商品属性[分类ID:{$data['prop_id']}]", $flag ? 1 : 0);
        }
        $this->end($flag,$msg);
    }

    /**
     * 检查当前属性的属性值是否可以删除
     */
    public function checkPropValueId()
    {
        $propValueId = $_POST['propValueId'];
        echo 'can';
    }

    public function settingDefaultPic()
    {
        $pic = kernel::single('syscategory_data_props')->getPropDefaultPic();
        $pagedata = $pic;
        return view::make('syscategory/admin/prop/default_pic.html', $pagedata);
    }

    public function saveDefaultPic(){
        $this->begin();
        foreach( $_POST['set'] as $k => $v ){
            app::get('syscategory')->setConf($k,$v);
        }
        $this->adminlog("修改属性默认图片]", 1);
        $this->end(true,app::get('syscategory')->_('保存成功'));
    }

    function selPropDialog() {
        $oProp = app::get('syscategory')->model('props');
        $aProp = $oProp->getList('prop_id,prop_name,prop_memo,prop_type',null,0,-1);
        $pagedata['props'] = $aProp;
        return view::make('syscategory/admin/prop/prop_select.html', $pagedata);
    }

    function previewProp(){
        $objMdlProps = app::get('syscategory')->model('props');
        $pagedata['prop'] = $objMdlProps->dump( $_POST['prop_id'], '*',array('prop_value'=>array('*')));
        $pagedata['prop_default_pic'] = app::get('syscategory')->getConf('prop.default.pic');
        return view::make('syscategory/admin/prop/prop_value_preview.html', $pagedata);
    }

    function delete($prop_id)
    {
        $this->begin('?app=syscategory&ctl=admin_props&act=index');
        try{
            $delFlag = app::get('syscategory')->model('props')->doDelete($prop_id);
            $this->adminlog("删除商品属性[分类ID:{$prop_id}]", 1);
        }catch(Exception $e){
            $this->adminlog("删除商品属性[分类ID:{$prop_id}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true,app::get('syscategory')->_('删除成功'));
    }

}
