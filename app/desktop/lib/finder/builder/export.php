<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_export extends desktop_finder_builder_prototype{

    function main(){
        $ioType = array();
        foreach( kernel::servicelist('desktop_io') as $aio ){
            $ioType[] = $aio->io_type_name;
        }
        $pagedata['ioType'] = $ioType;
        if( $_GET['change_type'] )
            $pagedata['change_type'] = $_GET['change_type'];
        
        if( !$pagedata['thisUrl'] )
            $pagedata['thisUrl'] = $this->url;
        return view::make('desktop/common/export.html', $pagedata)->render();
    }
}
