
<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syslogistics_ctl_admin_logistics extends desktop_controller{

    public $workground = 'syslogistics.workground.logistics';


    /**
     * 物流公司列表
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function index()
    {
        return $this->finder('syslogistics_mdl_dlycorp',array(
            'title' => app::get('syslogistics')->_('物流公司列表'),
            'actions' => array(
                array(
                    'label'=>app::get('syslogistics')->_('添加物流公司'),
                    'href'=>'?app=syslogistics&ctl=admin_logistics&act=create',
                    'target'=>'dialog::{title:\''.app::get('syslogistics')->_('添加物流公司').'\',  width:500,height:320}',
                ),
            ),
        ));
    }

    /**
     * 物流公司添加
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function create(){
        $pagedata['corpcode'] = $this->_corpCode();
        return $this->page('syslogistics/admin/logistics.html', $pagedata);
    }

    /**
     * 物流公司添加
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function edit($corp_id)
    {
        $filter = array(
            'corp_id'=>$corp_id,
        );
        $dlycorpMdl = app::get('syslogistics')->model('dlycorp');
        $dlycorpRow = $dlycorpMdl->getRow("*",$filter);
        $pagedata['dlycorp'] = $dlycorpRow;
        $pagedata['corpcode'] = $this->_corpCode();
        return $this->page('syslogistics/admin/logistics.html', $pagedata);
    }

    /**
     * 读取系统现有的物流公司代码
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    private function _corpCode()
    {
        $objDlycorp = kernel::single('syslogistics_data_dlycorp');
        $corpcode = $objDlycorp->getDlycorp();
        return $corpcode;
    }

    /**
     * 物流公司保存
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function dlycorpSave()
    {
        $this->begin();
        $dlycorp = $_POST['dlycorp'];
        if($dlycorp['custom'] && $_POST['custom_code']){
            $dlycorp['corp_code'] = $_POST['custom_code'];
        }else{
            $dlycorp['custom'] = 0;
        }
        $dlycorpMdl = app::get('syslogistics')->model('dlycorp');
        $result = $dlycorpMdl->save($dlycorp);
        $this->adminlog("添加物流公司", $result ? 1 : 0);
        $this->end($result);
    }

}

