<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
|--------------------------------------------------------------------------
| 二次开发目录设置
|--------------------------------------------------------------------------
|
| 二次开发目录设置,`custom`可以替换为自己的二次开发目录
|
*/
define('CUSTOM_CORE_DIR', ROOT_DIR.'/custom');

//DLC 2015/10/22
define('WITHOUT_POWERED', true);

/**
|--------------------------------------------------------------------------
| xhprof调试开关
|--------------------------------------------------------------------------
|
| 默认为false, 不需要调试时请不要开启
|
*/
define('XHPROF_DEBUG', false);

/**
|--------------------------------------------------------------------------
| 尚未完成改造的部分
|--------------------------------------------------------------------------
|
| 尚未完成改造的部分
|
*/
define('WITH_REWRITE', false); // URL REWRITE配置
define('EDITOR_ALL_SOUCECODE',false);//是否使后台可视化编辑器变为源码编辑模式
define('DONOTUSE_CSSFRAMEWORK',false);//是否禁用前台系统css框架n
define('WITHOUT_AUTOPADDINGIMAGE',false);//图片处理时不需要自动补白

define('WITHOUT_GZIP', false);
define('WITHOUT_STRIP_HTML', true);
define('IMAGE_MAX_SIZE', 1024*1024);

define('ADMIN_OPERATOR_LOG', true); //是否开启平台操作日志
define('SELLER_OPERATOR_LOG', false); //是否开启商家操作日志

# define('GZIP_CSS',true);
# define('GZIP_JS',true);
# define('DEV_CHECKDEMO', true);

/**
|--------------------------------------------------------------------------
| 暂时没地方放的常量定义
|--------------------------------------------------------------------------
|
| 暂时没地方放的常量定义
|
*/
define('SET_T_STR', 0);
define('SET_T_INT', 1);
define('SET_T_ENUM', 2);
define('SET_T_BOOL', 3);
define('SET_T_TXT', 4);
define('SET_T_FILE', 5);
define('SET_T_DIGITS', 6);
/**
|--------------------------------------------------------------------------
| windows安装兼容
|--------------------------------------------------------------------------
|
| windows安装兼容
|
*/
define('LOG_SYS_EMERG', 0);
define('LOG_SYS_ALERT', 1);
define('LOG_SYS_CRIT', 2);
define('LOG_SYS_ERR', 3);
define('LOG_SYS_WARNING', 4);
define('LOG_SYS_NOTICE', 5);
define('LOG_SYS_INFO', 6);
define('LOG_SYS_DEBUG', 7);
