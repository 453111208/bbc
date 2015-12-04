<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_import extends desktop_finder_builder_prototype{

    function main(){
        /*
        $importType = array();
        foreach( kernel::servicelist('desktop_io') as $aio ){
            $importType[] = $aio->io_type_name;
        }
        $pagedata['importType'] = $importType;
         */
        if( !$pagedata['thisUrl'] )
            $pagedata['thisUrl'] = $this->url;
        return view::make('desktop/common/import.html', $pagedata)->render();
    }
}
