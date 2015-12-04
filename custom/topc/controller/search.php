<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topc_ctl_search extends topc_controller {

    public function index()
    {
        $keyword = input::get('keyword');
        $searchType = input::get('searchtype');
        if( !empty($keyword) )
        {
                try{
                    if($searchType == "shop"){
                        return redirect::action('topc_ctl_gallery@index',array('searchContent'=>$keyword));
                    } else{
                        return redirect::action('topc_ctl_list@index',array('n'=>$keyword));
                    }               
                }catch(Exception $e){
                     $msg = $e->getMessage();
                    return $this->splash('error',null,$msg);
                }
        }
        else
        {
            return redirect::back();
        }
    }
}

