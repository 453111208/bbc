<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
	/*
	|--------------------------------------------------------------------------
	| Layout Default
	|--------------------------------------------------------------------------
	|
	| If you don't set a layout when using a "Theme" class the default layout
	| will replace automatically.
	|
	*/
    'layoutDefault' => 'default',

	/*
	|--------------------------------------------------------------------------
	| Path to lookup theme
	|--------------------------------------------------------------------------
	|
	| The root path contains themes collections.
	|
	*/
	'themeDir' => 'themes',

	/*
	|--------------------------------------------------------------------------
	| A pieces of theme collections
	|--------------------------------------------------------------------------
	|
	| Inside a theme path we need to set up directories to
	| keep "layouts", "assets" and "partials".
	|
	*/
	'containerDir' => array(
		'partial' => '',
        'layout' => '',
	),
);
