<?php
/**
 * ShopEx licence
 *
 * @category ecos
 * @package image.lib
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @version 0.1
 */

/**
 * 这个类实现image表的实体
 * @category ecos
 * @package image.model
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class image_mdl_images extends dbeav_model{
	var $has_tag = true;
	var $has_many = array(
		'tag'=>'tag_rel@desktop:replace:id^rel_id'
	);

	/**
	 * @var array 定义这个实体查询列表默认的排序字段，排序方式
	 */
    var $defaultOrder = array('last_modified','desc');

    /**
     * 获取所有的引擎信息（目前为实现）
     * @param null
     * @return mixed 引擎信息
     */
    function all_storages(){
        return;
    }
}
