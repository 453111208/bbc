<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
return array (
  'columns' => 
  array (
    'app' => array(
        //'type'=>'varchar(50)',
        'type' => 'string',
        'length' => 50,
        //'pkey' => true,
        'comment' => app::get('base')->_('app名'),
    ),
    'key' => array(
        //'type'=>'varchar(255)',
        'type' => 'string',
        //'pkey' => true,
        'comment' => app::get('base')->_('setting键值'),
        
    ),
    'value' => array(
        //'type'=>'longtext',
        'type' => 'text',
        'comment' => app::get('base')->_('setting存储值'),
    ),
  ),
  'primary' => ['app', 'key'],
  'comment' => app::get('base')->_('setting存储表'),
);
