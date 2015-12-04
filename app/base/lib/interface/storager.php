<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

interface base_interface_storager {


    public function save($fileObject);

    public function remove($id);

    public function getFile($id);

}
