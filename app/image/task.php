<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class image_task
{
    function post_install()
    {
        logger::info('Initial image');
        kernel::single('base_initial', 'image')->init();

        $conf = app::get('image')->getConf('image.default.set');

        $objImage = kernel::single('image_data_image');
        $app_dir = app::get('image')->app_dir;
        foreach($conf as &$item)
        {
            $data = $objImage->store($app_dir.'/initial/default_images/'.$item['default_image'].'.gif', 'admin', 'size', true);
            $objImage->rebuild($data['ident']);
            $item['default_image'] = $data['url'];
        }

        app::get('image')->setConf('image.set',$conf);

    }//End Function

}//End Class

