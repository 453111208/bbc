<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
/**
 * @table coupons;
 *
 * @package dbschema
 * @version $v1
 * @copyright 2010 ShopEx
 * @license Commercial
 */

return array (
    'columns' =>
    array (
        'cpns_id' =>
        array (
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'label' => app::get('syslogistics')->_('id'),
            'width' => 110,
            'comment' => app::get('syslogistics')->_('优惠券方案id'),
            'editable' => false,
        ),
        'cpns_name' =>
        array (
            //'type' => 'varchar(255)',
            'type' => 'string',
            'label' => app::get('syslogistics')->_('优惠券名称'),
            'searchable' => true,
            'width' => 110,
            'comment' => app::get('syslogistics')->_('优惠券名称'),
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
        ),
    ),
    
    'primary' => 'cpns_id',
    'comment' => app::get('syslogistics')->_('优惠券表'),
);
