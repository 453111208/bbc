<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='相关资讯';
$setting['version'] ='v1.0';
$setting['stime'] ='2013-07';
$setting['catalog'] ='资讯中心';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '相关资讯.';
$setting['userinfo'] = '相关资讯.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );
$articleSql = "SELECT
		sysinfo_article_nodes.node_name,
		sysinfo_article.*
	FROM
		sysinfo_article
	LEFT JOIN sysinfo_article_nodes ON sysinfo_article.node_id = sysinfo_article_nodes.node_id";
$articleList = app::get("base")->database()->executeQuery($articleSql)->fetchAll();
$setting['articleList'] = $articleList;
?>
