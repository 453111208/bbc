<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysstat_desktop_widgets_exstatistics implements desktop_interface_widget
{

    var $order = 1;
    function __construct($app)
    {
        $this->app = app::get('sysstat');
    }

    function get_title(){

        return app::get('sysstat')->_("运营分析");

    }

    function get_html()
    {

        $mdlTrade = $this->app->model('trade_statics');
        //近7日的订单量
        $filter = array(
                    'createtime|sthan'=>strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00'),
                    'createtime|bthan'=>strtotime(date("Y-m-d", time()-86400*8) . ' 00:00:00'),
                );
        $from = time();
        $to = strtotime('-1 week');
        $db = app::get('sysstat')->database();
        $rows = $db->executeQuery('select already_send_fee as order_amount,already_send_trade as order_nums,createtime as mydate from sysstat_trade_statics  where createtime>=? and createtime<? group by createtime', [$to, $from], [\PDO::PARAM_INT, \PDO::PARAM_INT])->fetchAll();

        foreach($rows as $row)
        {
            $data[$row['mydate']] = array('order_amount'=>$row['order_amount'],'order_nums'=>$row['order_nums']);
        }

        $pagedata['data'] = $data;
        $agedata['this_week_from'] = date("Y-m-d", time()-(date('w')?date('w')-1:6)*86400);
        $pagedata['this_week_to'] = date("Y-m-d", strtotime($pagedata['this_week_from'])+86400*7-1);
        return view::make('sysstat/desktop/widgets/orderaccount.html', $pagedata)->render();
    }
    public function get_className()
    {
        return " valigntop";
    }
    public function get_width()
    {
        return "l-1";
    }

}
