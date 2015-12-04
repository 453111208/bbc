<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 这个类用于获取分析图形的数据的基类
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.analysis
 */
class ectools_analysis_base 
{
	/**
	 * 获取统计图形数据
	 * @param array 过滤条件
	 * @return array data
	 */
    public function fetch_graph_data($params) 
    {
        $qb = app::get('ectools')->database()->createQueryBuilder();
        if ($analysis_info = $qb->select('*')->from('ectools_analysis')->where('service='.$qb->createPositionalParameter($params['service']))->execute()->fetch())
        {
            $qb = app::get('ectools')->database()->createQueryBuilder();
            $qb->select('target,flag,value,time')
               ->from('ectools_analysis_logs')
               ->where('analysis_id='.$qb->createPositionalParameter($analysis_info['id']))
               ->andWhere('target ='.$qb->createPositionalParameter($params['target']))
               ->andWhere('time>='.$qb->createPositionalParameter(strtotime(sprintf('%s 00:00:00', $params['time_from']))))
               ->andWhere('time<='.$qb->createPositionalParameter(strtotime(sprintf('%s 23:59:59', $params['time_to']))));
            if(isset($this->_params['type'])) $qb->andWhere('type = '.$qb->createPositionalParameter($params['type']));
            $rows = $qb->execute()->fetchAll();
        }
        else
        {
            return array('categories'=>array(), 'data'=>array());
        }
        
        for($i=strtotime($params['time_from']); $i<=strtotime($params['time_to']); $i+=($analysis_info['interval'] == 'day')?86400:3600){
            $time_range[] = ($analysis_info['interval'] == 'day') ? date("Y-m-d", $i) : date("Y-m-d H", $i);
        }
        
        $logs_options = kernel::single($params['service'])->logs_options;
        $target = $logs_options[$params['target']];
        if(is_array($target['flag']) && count($target['flag'])){
            foreach($target['flag'] AS $k=>$v){
                foreach($time_range AS $date){
                    $data[$v][$date] = 0;
                }
            }
        }else{
            foreach($time_range AS $date){
                $data['全部'][$date] = 0;
            }
        }

        foreach($rows AS $row){
            $date = ($analysis_info['interval'] == 'day') ? date("Y-m-d", $row['time']) : date("Y-m-d H", $row['time']);
            $flag_name = $target['flag'][$row['flag']];
            if($flag_name){
                $data[$flag_name][$date] = $row['value'];
            }else{
                $data['全部'][$date] = $row['value'];
            }
        }        

        return array('categories'=>$time_range, 'data'=>$data);
    }//End Function

}//End Class
