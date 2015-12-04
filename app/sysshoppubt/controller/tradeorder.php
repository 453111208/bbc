<?php

/**
 * @brief 交易单列表
 */
class sysshoppubt_ctl_tradeorder extends desktop_controller{



 	public function index()
    {
        return $this->finder('sysshoppubt_mdl_tradeorder',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('交易单列表'),
            'use_buildin_delete'=>false,
        ));
    }

  
    public function create($tradeorderId)
    {
        if( $tradeorderId )
        {
            $tradeorderInfo = app::get('sysshoppubt')->model('tradeorder')->getRow('*',array('tradeorder_id'=>$tradeorderId));
            $pagedata['tradeorderInfo'] = $tradeorderInfo;
        }
        return view::make('sysshoppubt/tradeorder/tradeorder.html', $pagedata);
    }


  
    public function update()
    {
        $data = $_POST;
        if( $_POST['tradeorder_id'] )
        {
            $tradeorderInfo = app::get('sysshoppubt')->model('tradeorder')->getRow('*',array('tradeorder_id'=>$_POST['tradeorder_id']));
            $sql="update sysshoppubt_tradeorder set state= ".$_POST['state']." where tradeorder_id =".$_POST['tradeorder_id'];
            $db = app::get('sysshoppubt')->database();
            $db->exec($sql);
        }
        $msg = app::get('syscategory')->_('成功');
        return $this->splash('success',null,$msg);
    }

}