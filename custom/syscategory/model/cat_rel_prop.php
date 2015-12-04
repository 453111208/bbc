<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class syscategory_mdl_cat_rel_prop extends dbeav_model{

    function save(&$sdf,$mustUpdate = null,$mustInsert = false){
        $data_list = $this->getList('*',array('cat_id'=>$sdf['cat_id']));
        $sdf['order_sort']=count($data_list)+1;
        return parent::save($sdf);
    }
}
