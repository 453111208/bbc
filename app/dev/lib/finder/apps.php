<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class dev_finder_apps{

    var $actions = array(
        array('label'=>'维护','href'=>"?app=desktop&ctl=appmgr&act=maintenance",'target'=>'command::{title:\'维护\'}')
        );


    var $detail_service='Services';
    function detail_service($app){
        $html = '<table><tr style="border-bottom:1px solid #9DAFC3"><th width="35px">&nbsp;</th><th width="100px">Service</th><th>Class</th></tr>';
        foreach(kernel::single('base_application_service')->detect($app) as $name=>$item){
            foreach((array)$item->current['class'] as $class){
                $bgcolor = ($i++ %2 ==0 )?'#F4F7FB':'#fff';
                $html.="<tr style='background:{$bgcolor}'><td>&nbsp;{$i}</td><td style='padding-right:10px'>{$name}</td><td>".implode('|', $class)."</td></tr>";
            }
        }
        return $html.'</table>';
    }

    var $detail_dbtable='DB tables';
    function detail_dbtable($app)
    {
        $db = db::connection();
        $platform = $db->getDatabasePlatform()->getName();
        if ($platform == 'mysql')
        {
            $tables = $db->executeQuery('SHOW TABLE STATUS like '. $db->quote($app.'%'))->fetchAll();

            return view::make('dev/dbtable_mysql.html', compact('tables', 'app', 'platform'));
        }
        else
        {
            $tables = array_reduce($db->getSchemaManager()->listTableNames(), function($tableList, $tableName) use ($app) {
                if (starts_with($tableName, $app.'_')) {
                    $tableList[]  = ['Name' => $tableName];
                }
                return $tableList;
            }, []);
            return view::make('dev/dbtable_other.html', compact('tables', 'platform'));
        }
    }
}
