<?php
class sysrate_ctl_consultation extends desktop_controller{
    public $workground = 'sysitem.workground.item';
    public function index()
    {
        return $this->finder( 'sysrate_mdl_consultation', array(
            'title'=>app::get('sysrate')->_('咨询列表'),
            'use_buildin_filter' => true,
            'use_buildin_delete' => false,
            'base_filter' =>array('be_reply_id' => 0),
        ));
    }

    public function _views(){
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysrate')->_('全部咨询'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysrate')->_('商品'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'item',
                ),
            ),
            2=>array(
                'label'=>app::get('sysrate')->_('库存/配送'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'store_delivery',
                ),
            ),
            3=>array(
                'label'=>app::get('sysrate')->_('支付方式'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'payment',
                ),
            ),
            4=>array(
                'label'=>app::get('sysrate')->_('发票维修'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'invoice',
                ),
            ),
        );
        return $subMenu;
    }

    public function to_display()
    {
        $id = input::get('id');
        $status = input::get('display');
        if($status == "true")
        {
            $msg = "关闭显示成功";
        }
        elseif($status == "false")
        {
            $msg = "开启显示成功";
        }

        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        try{
            $objConsultation = kernel::single('sysrate_data_consultation');
            $result = $objConsultation->doDisplay($id,$status);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
        }

        $this->end($result,$msg);
    }

    public function to_delete()
    {
        $this->begin("javascript:finderGroup["."'".$_GET["finder_id"]."'"."].refresh()");
        $meg = "删除咨询/回复成功";
        try{
            $id = input::get('id');
            $type = input::get('type');
            $objConsultation = kernel::single('sysrate_data_consultation');
            $result = $objConsultation->doDelete($id,$type);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
        }
        $this->end($result,$msg);
    }
}


