<?php
class topm_ctl_search extends topm_controller{
    public function index()
    {
        $keyword = input::get('keyword');
        $searchType = input::get('searchtype');
        if( !empty($keyword) )
        {
            if( $searchType == 'shop' )
            {
                return redirect::action('topm_ctl_shopcenter@search',array('n'=>$keyword));
            }
            else
            {
                return redirect::action('topm_ctl_list@index',array('n'=>$keyword));
            }
        }
        else
        {
            return redirect::back();
        }
    }

}
