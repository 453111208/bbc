<?php
 class sysshoppubt_ctl_consultation extends desktop_controller{
 	public function index(){
 		return $this->finder('sysshoppubt_mdl_consultation',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('咨询列表'),
            'use_buildin_delete'=>false,
        ));
 	}

    public function _views(){
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysshoppubt')->_('全部咨询'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysshoppubt')->_('交易'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'item',
                ),
            ),
            2=>array(
                'label'=>app::get('sysshoppubt')->_('库存/配送'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'store_delivery',
                ),
            ),
            3=>array(
                'label'=>app::get('sysshoppubt')->_('支付方式'),
                'optional'=>false,
                'filter'=>array(
                    'consultation_type'=>'payment',
                ),
            ),
            4=>array(
                'label'=>app::get('sysshoppubt')->_('发票维修'),
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
            $objMdlConsultation = app::get('sysshoppubt')->model('consultation');
        	if($status == "true")
        	{
        	    $data['is_display'] = "false";
        	}
        	else if($status == "false")
        	{
        	    $data['is_display'] = "true";
        	}
        	$data['consultation_id'] = $id;
        	$result = $objMdlConsultation->save($data);
        	if(!$result && $status == "true")
        	{
        	    throw new LogicException('关闭显示失败');
        	}
        	elseif(!$result && $status == "false")
        	{
        	    throw new LogicException('开启显示失败');
        	}
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
        $msg = "删除咨询/回复成功";
        try{
            $id = input::get('id');
            $type = input::get('type');

            $result = app::get('sysshoppubt')->database()->executeQuery('DELETE FROM sysshoppubt_consultation WHERE consultation_id = '. $id);
            
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
        }
        $this->end($result,$msg);
    }


 }