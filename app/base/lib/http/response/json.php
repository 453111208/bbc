<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_http_response_json extends \Symfony\Component\HttpFoundation\JsonResponse {

	use base_http_response_trait;
    protected function update() {
        $this->headers->set('Content-Type', 'application/json;charset=utf-8');
        return parent::update();
        
    }

}

