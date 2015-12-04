<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class addItem extends PHPUnit_Framework_TestCase
{
    public function setUp(){
        //$this->model = app::get('base')->model('members');
    }

    public function testRequest(){
        $item = array (
            'item' =>
            array (
                'item_id' => '',
                'sku' => '{}',
                'spec' => '{}',
                'shop_cids' =>
                array (
                    0 => '8',
                ),
                'title' => 'aaa',
                'sub_title' => '',
                'brand_id' => '10',
                'bn' => '',
                'use_platform' => '0',
                'price' => '10000',
                'store' => '10000',
                'sub_stock' => '0',
                'mkt_price' => '10000',
                'cost_price' => '10000',
                'weight' => '2',
                'order_sort' => 1,
                'desc' => '',
                'wap_desc' => '',
                'shop_id' => 1,
                'cat_id' => '185',
                'approve_status' => 'instock',
                'shop_cat_id' => '8',
            ),
            'return_to_url' => 'http://localhost/bbc2/public/index.php/shop/item/itemList.html',
            'cat_id' => '185',
        );
        $itemname = 'itemname';
        for($i=0; $i<10000; $i++)
        {
            $item['item']['title'] = $itemname . $i;
            $res = kernel::single('sysitem_data_item')->add($item);

            echo "添加{$item['item']['title']}，成功！\n";
        }
    }
}
