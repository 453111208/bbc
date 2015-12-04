<?php
class sysstat_analysis_member extends ectools_analysis_abstract implements ectools_analysis_interface
{
	public $detail_options = array(
        'hidden' => true,
    );
    public $graph_options = array(
        'hidden' => true,
    );

    public function finder(){
        return array(
            'model' => 'sysstat_mdl_analysis_member',
            'params' => array(
                'actions'=>array(
                    array(
                        'label'=>app::get('sysstat')->_('生成报表'),
                        'class'=>'export',
                        'icon'=>'add.gif',
                        'href' => '?app=importexport&ctl=admin_export&act=export_view&_params[app]=sysstat&_params[mdl]=sysstat_mdl_analysis_member',
                        'target'=>'{width:400,height:170,title:\''.app::get('sysstat')->_('生成报表').'\'}'),
                ),
                'title'=>app::get('sysstat')->_('会员购物排行'),
                'use_buildin_selectrow'=>false,
                'use_buildin_delete'=>false,
            ),
        );
    }

    public function rank(){
        $filter = $this->_params;
        $filter['time_from'] = isset($filter['time_from'])?$filter['time_from']:'';
        $filter['time_to'] = isset($filter['time_to'])?$filter['time_to']:'';

        $pagedata['timefrom'] = $filter['time_from'];
        $pagedata['timeto'] = $filter['time_to'];

        $html = view::make('sysstat/admin/analysis/member.html', $pagedata)->render();

        $this->pagedata['rank_html'] = $html;
    }
}
