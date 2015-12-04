<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * 专注预处理finder filter,
 */

class desktop_finder_filter
{
	/**
	 * 处理finder tag相关filter
	 *
	 * @param  string|array  $filter
	 * @param  base_db_model   $object
	 * @return array
	 */
    private function processTagFilter(&$filter, $object)
    {
        $qb = app::get('desktop')->database()->createQueryBuilder();
        $idColumn = $object->idColumn;

        if(isset($filter['tag']) && $filter['tag'])
        {
            $tags = (array)$filter['tag'];
            unset($filter['tag']);
            
            $tags = array_filter($tags, function($tag){
                return $tag != '_ANY_' && $tag != null && $tag != '';
            });

            if (empty($tags)) return;
                
            $ids = array_column($qb->select('rel_id')->from('desktop_tag_rel')->where($qb->expr()->in('tag_id', $tags))->execute()->fetchAll(), 'rel_id');

            if ($ids)
            {
                if (isset($filter[$idColumn]) && $filter[$idColumn]!=null)
                {
                    $filter[$idColumn] = array_intersect((array)$filter[$idColumn], $ids);
                }
                else
                {
                    $filter[$idColumn] = $ids;
                }
            }
        }
    }

	/**
	 * 处理finder通用的一些filter
	 *
	 * @param  string|array  $filter
	 * @param  base_db_model   $object
	 * @return array
	 */
    private function processCommonFilter(&$filter, $object)
    {
        $idColumn = $object->idColumn;

        if (is_array($idColumn)) throw new \InvalidArgumentException('desktop finder donnot support more than one primary key');
        if (!isset($filter[$idColumn])) return;

        $idValue = $filter[$idColumn];
        if ($idValue == '_ALL_' || $idValue == null || $idValue == '' || $filter[$idColumn]==array('_ALL_'))
        {
            unset($filter[$idColumn]);
        }
    }

	/**
	 * 处理finder搜索相关的filter
	 *
	 * @param  string|array  $filter
	 * @param  base_db_model   $object
	 * @return array
	 */
    private function processSearchFilter(&$filter, $object)
    {
        $newFilter = [];
        $cols = $object->_columns();
        $searchOptions = $object->searchOptions();
        foreach ($filter as $columnName => $filterValue)
        {
            if(isset($cols[$columnName]))
            {
                switch ($cols[$columnName]['type'])
                {
                    case 'time':
                    case 'last_modify':
                        if($filter['_'.$columnName.'_search']=='between')
                        {
                            if ($filter[$columnName.'_from'])
                            {
                                $fromTime = strtotime($filter[$columnName.'_from'].' '.$filter['_DTIME_']['H'][$columnName.'_from'].':'.$filter['_DTIME_']['M'][$columnName.'_from'].':00');
                                $newFilter[$columnName.'|bthan'] = $fromTime;
                            }

                            if ($filter[$columnName.'_to'])
                            {
                                $toTime = strtotime($filter[$columnName.'_to'].' '.$filter['_DTIME_']['H'][$columnName.'_to'].':'.$filter['_DTIME_']['M'][$columnName.'_to'].':00');
                                $newFilter[$columnName.'|lthan'] = $toTime;
                            }
                            
                        }
                        else
                        {
                            $time = strtotime($filter[$columnName].' '.$filter['_DTIME_']['H'][$columnName].':'.$filter['_DTIME_']['M'][$columnName].':00');
                            $newFilter[$columnName.'|'.$filter['_'.$columnName.'_search']] = $time;
                            
                        }
                        break;
                    case 'money':
                    case 'number':
                    case 'decimal':
                    case 'integer':
                    case 'smallint':
                        if($filter['_'.$columnName.'_search']=='between')
                        {
                            if ($from = $filter[$columnName.'_from'])
                            {
                                $newFilter[$columnName.'|bthan'] = $from;
                            }
                            
                            if ($to = $filter[$columnName.'_to'])
                            {
                                $newFilter[$columnName.'|lthan'] = $to;
                            }
                        }
                        elseif (isset($filter['_'.$columnName.'_search']))
                        {
                            $newFilter[$columnName.'|'.$filter['_'.$columnName.'_search']]  = $filterValue;
                        }
                        else
                        {
                            $newFilter[$columnName] = $filterValue;
                        }
                        break;
                    default:
                    case isset($cols[$columnName]['filtertype'])&&isset($filter['_'.$columnName.'_search']):
                        $newFilter[$columnName.'|'.$filter['_'.$columnName.'_search']] = $filterValue;
                        unset($filter[$columnName]);
                    case isset($cols[$columnName]['searchtype']):
                        $newFilter[$columnName.'|'.$cols[$columnName]['searchtype']] = $filterValue;
                        // todo: 因为searchOption 
                        unset($filter[$columnName]);
                        break;
                    default:
                        $newFilter[$columnName] = $filterValue;
                        break;
                }
            }
            elseif(strpos($cols[$columnName], '|'))
            {
                $newFilter[$columnName] = $cols[$columnName];
            }
            elseif(isset($searchOptions[$columnName]))
            {
                $newFilter[$columnName] = $filterValue;
                break;
            }
            
        }
        
        $filter = $newFilter;
    }

	/**
	 * 过滤post/get上来的数据生成finder filter
	 *
	 * @param  string|array  $key
	 * @param  mixed   $value
	 * @return \Illuminate\View\View
	 */
    public function createFinderFilter(&$filter, $object)
    {
        // 过滤tag
        $this->processTagFilter($filter, $object);
        // 通用finder过滤 
        $this->processCommonFilter($filter, $object);
        // 搜索相关过滤
        $this->processSearchFilter($filter, $object);
    }
}