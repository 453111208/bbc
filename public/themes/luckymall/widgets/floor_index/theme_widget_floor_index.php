<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_floor_index( &$setting )
{
    if( !$setting['slider_type'] )
    {
        if( $setting['brand'] )
        {
            $data = app::get('desktop')->rpcCall('category.brand.get.list',['brand_id'=>implode(',',$setting['brand']),['fields'=>'brand_id,brand_logo']]);
            $i = 0;
            $k = 1;
            foreach( $data as $n=>$row )
            {
                $picData[$k][$n]['link'] = $row['brand_logo'];//图片地址
                $picData[$k][$n]['linkinfo'] = $row['brand_name'];//图片描述
                $picData[$k][$n]['linktarget'] = url::action('topc_ctl_list@index',['search_keywords'=>$row['brand_name']]);//链接地址
                $i++;
                if( $i%3 === 0 ) $k++;
            }
            $setting['picData'] = $picData;
        }
    }
    else
    {
        foreach( (array)$setting['pic'] as $n=>$row )
        {
            $picData[][$n] = $row;
        }
        $setting['picData'] = $picData;
    }

    return $setting;
}
?>
