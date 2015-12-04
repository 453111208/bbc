<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class desktop_finder_builder_filter extends desktop_finder_builder_prototype{

    function main()
    {
        $view = $_GET['view'];
        $view_filter = $this->getViews();
        $__filter = $view_filter[$view];
        if( $__filter['filter'] ) $filter = $__filter['filter'];
        $o = new desktop_finder_builder_filter_render($this->finder_aliasname);
        // ȡ��object_name,��model�����ݿ�ʵ�岻��1��1��ʱ��
        if (method_exists($this->object, 'object_name')){
            $object_name = $this->object->object_name();
        }else{
            $object_name = $this->object->table_name();
        }
        return $o->main($object_name,$this->app,$filter);
 
    }

}
