<?php

/**
 * @brief 商品看样列表
 */
class sysshoppubt_ctl_sample extends desktop_controller{

 	public function index()
    {
        return $this->finder('sysshoppubt_mdl_sample',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('sysshoppubt')->_('看样标准商品表'),
            'use_buildin_delete'=>false,
        ));
    }

    // public function _views()
    // {
    //     $mdl_all = app::get('sysshoppubt')->model('sprodrelease');
    //     $sample = app::get('sysshoppubt')->model('sample');
    //     $paminfo = $sample->getList('distinct standard_id');
    //     foreach ($paminfo as $key => $value) {
    //     	$change[$key] = $value['standard_id'];
    //     }
    //     if(!$change){
    //         $change = (0);
    //     }
    //     $all = $mdl_all->count(array('standard_id'=>$change));
    //     $subMenu = array(
    //         0=>array(
    //             'label'=>app::get('sysshoppubt')->_("申请看样列表 ( $all )"),
    //             'optional'=>false,
    //             'filter'=>array(
    //                 'standard_id'=>$change,
    //             ),
    //         ),
    //     );
    //     return $subMenu;
    // }
    /*public function tissue($standard_id)
    {
    	if(!$standard_id)
        {
            $standard_ids = input::get('standard_id');
            $standard_id = implode(',',$standard_ids);
        }
    	$oItem = app::get('sysshoppubt')->model('sprodrelease');
        $chech = $oItem->getList('*',array('standard_id' => $standard_id));
        $chech1 = $chech[0];
        $chech1['seegoods_state'] = 1;
        $oItem->update($chech1,$chech[0]);
    }*/
    
}