<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysclearing_ctl_admin_settlement extends desktop_controller {

    public $workground = 'sysclearing_ctl_admin_settlement';

    /**
     * 商家结算单管理列表
     */
    public function index()
    {
        return $this->finder(
            'sysclearing_mdl_settlement',
            array(
                'title'=>app::get('sysclearing')->_('结算单汇总列表'),
            )
        );
    }

    /**
     * 商家结算单明细列表
     */
    public function detail()
    {
        return $this->finder(
            'sysclearing_mdl_settlement_detail',
            array(
                'title'=>app::get('sysclearing')->_('结算单明细列表'),
            )
        );
    }

    public function confirm($settlementNo)
    {
        $pagedata['settlement_no'] = $settlementNo;
        return $this->page('sysclearing/admin/confirm.html', $pagedata);
    }

    public function doConfirm()
    {
        $this->begin("?app=sysclearing&ctl=admin_settlement&act=index");
        $settlementNo = input::get('settlement_no');
        $status = input::get('settlement_status');
        try
        {
            kernel::single('sysclearing_settlement')->doConfirm($settlementNo, $status);
            $this->adminlog("确认结算单[分类ID:{$settlementNo}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("确认结算单[分类ID:{$settlementNo}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }
}
