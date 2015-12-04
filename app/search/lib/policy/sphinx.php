<?php

class search_policy_sphinx implements search_interface_policy {

    public $name = 'sphinx搜索';
    public $type = 'sphinx';
    public $description = '基于sphinxql开发的搜索引擎';
    public $index = null;

    /**
     *__construct 初始化类,连接sphinx服务
     */
    public function __construct()
    {
        $this->sphinx_config = $this->_config();
        if (is_array ( $this->sphinx_config ))
        {
            $this->link = $this->link();
            return $this;
        }
    }//End Function

    public function index($index, $extends=false)
    {
        $this->index = $index;
        return $this;
    }

    public function checkColumnsReturn($columns)
    {
        if( $columns == '*' )
        {
            return false;
        }

        $colsArr = explode(',', $columns);

        //查询到索引的字段
        $columns = $this->getDescribe();
        $describeField = $columns['field'];
        foreach( $colsArr as $cols )
        {
            if( in_array($cols, $describeField) )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * link 连接sphinxql
     *
     * @access protected
     * @return obj
     */
    public function link()
    {
        if (is_resource ( $this->link ))
        {
            return $this->link;
        }
        $connect = ($this->sphinx_config['pconnect'] == true) ? 'mysql_pconnect' : 'mysql_connect';
        $link = $connect($this->sphinx_config['host']);
        if (!$link)
        {
            return false;
        }
        return $link;
    }//End Function

    /**
     * _config 获取sphinxql连接配置信息
     *
     * @access private
     * @return array
     */
    private function _config()
    {
        $host = config::get('search.sphinx.host');
        if (!empty($host))
        {
            $option ['host']= $host;
        }
        $sphinx_config['host'] = ($option ['host'] ? $option ['host'] : '127.0.0.1:9306');
        $pconnect = config::get('search.sphinx.pconnect', false);
        if ($pconnect)
        {
            $sphinx_config['pconnect']  == true;
        }
        return $sphinx_config;
    }//End Function

    /**
     * 设置索引所需参数信息
     *
     * @param string $indexName 索引名称
     * @param array $params  索引参数
     *
     * @return bool
     */
    public function setIndexParams($indexName, $params)
    {
        $key = 'search_index_setting_'.$indexName;
        return app::get('search')->setConf($key,$params);
    }

    /**
     * @brief 根据索引名称获取索引的配置参数信息
     *
     * @param $indexName
     *
     * @return array
     */
    public function getIndexParams($indexName)
    {
        $tablesInfo = $this->getDescribe($indexName);
        $column = array_combine($tablesInfo['int'],$tablesInfo['int']);
        $setting = app::get('search')->getConf('search_index_setting_'.$indexName);

        $data['setting']['ranker'] = empty($setting['ranker']) ? 'proximity_bm25' : $setting['ranker'];
        $data['setting']['order_value'] = empty($setting['order_value']) ? current($column) : $setting['order_value'];
        $data['setting']['order_type'] =  empty($setting['order_type']) ? 'desc' : $setting['order_type']; //默认降序
        $data['setting']['max_limit'] = empty($setting['max_limit']) ? '1000' : $setting['max_limit']; ;

        $data['search_ranker'] =
            array(
                'proximity_bm25'=>'proximity_bm25',
                'bm25'=>'bm25',
                'none'=>'none',
                'wordcount'=>'wordcount',
                'proximity'=>'proximity',
                'matchany'=>'matchany',
                'fieldmask'=>'fieldmask'
            );
        $data['column'] = $column;
        return $data;
    }

    /**
     * get_rt 获取索引的索引类型(实时|非实时)
     *
     * @access public
     * @return array 返回索引类型（实时|非实时）
     */
    public function get_rt()
    {
        $list = $this->query('show tables');
        foreach($list as $val)
        {
            if($val['Index'] == $this->index && $val['Type'] == 'rt')
            {
                return true;
            }
        }
        return false;
    }//End function

    /**
     *exec 直接执行sphinxql语句
     *
     * @param string $sql sphinxql语句
     * @access public
     * @return source $rs
     */
    public function exec($sql)
    {
        $rs = mysql_query($sql,$this->link);
        if(!$rs && $this->link)
        {
            $this->_error($sql);
        }
        return $rs;
    }//End Function

    /**
     *_error 记录错误和警告log
     *
     * @param string $query_sql 执行的sphinxql语句
     * @access public
     */
    public function _error($query_sql)
    {
        $br = "\r\n\t\t\t\t";
        $msg = 'sphinxql执行错误：'.$query_sql;

        if(mysql_error())
        {
            $error = mysql_error();
            $msg .= $br.'ERROR :'.$error;
            throw new Exception($msg);
        }

        $warnings = $this->query('SHOW WARNINGS');
        if( $warnings )
        {
            foreach($warnings as $row)
            {
                $msg .= $br.'WARNING ('.$row['Code'].'):'.$row['Message'];
            }
            logger::warning($msg);
        }
    }//End Function

    /**
     * query 执行sphinxql语句返回对应的数据
     *
     * @param string $sql sphinxql语句
     * @access public
     * @return array $data 查找到的数据
     */
    public function query($sql)
    {
        $rs = $this->exec($sql);
        if($rs && !is_bool($rs))
        {
            $data = array();
            while($row = mysql_fetch_assoc($rs))
            {
                $data[]=$row;
            }
        }
        else
        {
            $data = $rs ? true : false;
        }
        return $data;
    }//End Function

    /**
     * @brief 获取到索引名称和索引类型
     *
     * @return array
     */
    public function getIndex()
    {
        $tables = $this->query('show tables');
        foreach($tables as $key=>$row)
        {
            $data[$key]['index_name'] = $row['Index'];
            $data[$key]['index_type'] = $row['Type'];
        }
        return $data;
    }

    /**
     * select 在索引中搜索到索引ID
     *
     * @param array $queryArr 搜索条件
     * @access public
     * @return array 返回索引ID
     */
    public function select($cols='*', $offset=0, $limit=1000, $orderBy=null, $groupBy='')
    {
        $sphinxql = $this->_sphinxql($cols, $offset, $limit, $orderBy, $groupBy);
        $data = $this->query($sphinxql);
        $total = $this->show_meta();
        $list['list'] = $data;
        $list['total_found'] = $total[1]['Value'];
        return $list;
    }//End Function

    /**
     * 统计满足条件的总数
     *
     * @param $queryFilter 查询条件
     *
     * @return $totalFound 总数
     */
    public function count()
    {
        $sphinxql = $this->_sphinxql('id',0,1);
        $data = $this->query($sphinxql);
        $total = $this->show_meta();
        $totalFound = $total[1]['Value'];
        return $totalFound;
    }

    /**
     * insert 插入一条索引(只能是实时索引有效)
     *
     * @param array $queryArr
     * @access public
     * @return bool
     */
    public function insert($queryArr=array())
    {
        $rt = $this->get_rt();
        if(!$rt)
        {
            return false;
            //trigger_error(app::get('search')->_('插入只支持实时索引').E_USER_ERROR);
        }
        $fieldsArr = array_keys($queryArr);
        //插入索引必须要id
        if(!in_array('id',$fieldsArr))
        {
            $fieldsArr[] = 'id';
            $listid_sphinxql = 'select id from '.$this->index.' order by id desc limit 1';
            $list = $this->query($listid_sphinxql);
            $queryArr [] = intval($list[0]['id'])+1;
        }
        $fields = implode(',',$fieldsArr);
        $values = implode("','",$queryArr);
        if($fields && $values)
        {
            $sphinxql = 'INSERT INTO '.$this->index.' ( '.$fields.' ) VALUES ( \''.$values.'\' )';
        }
        $res = $this->query($sphinxql);
        return $res;
    }//End Function

    /**
     * update 更新索引
     *
     * @param array $queryArr 需要更新的数据
     * @param array $where    更新的条件
     * @access public
     * @return bool
     */
    public function update($queryArr=array(),$where=array())
    {
        if(is_array($queryArr))
        {
            foreach($queryArr as $uint=>$value)
            {
                if(is_array($value))
                {
                    $setArr[] = $uint .' = ('.implode(',',$value).')';
                }
                else
                {
                    $setArr[] = $uint .' = '.$value;
                }
            }
            $set = implode(',',$setArr);
        }
        else
        {
            $set = $queryArr;
        }
        $where = $this->__filter($where);
        if($where) $where_str = ' WHERE '.$where;
        $sphinxql = 'UPDATE '.$this->index .' SET '.$set.$where_str;
        $res = $this->query($sphinxql);
        return $res;
    }//End Function

    /**
     *delete 删除索引(只支持实时索引)
     *
     */
    public function delete($queryArr=array())
    {
        $rt = $this->get_rt();
        if(!$rt)
        {
            return false;
            //trigger_error(app::get('search')->_('删除只支持实时索引').E_USER_ERROR);
        }
        $where = $this->__filter($queryArr);
        $sphinxql = 'DELETE FROM '.$this->index.' WHERE ' . $where;
        $res = $this->query($sphinxql);
        return $res;

    }//End Function

    /**
     *show_meta 获取上一条显示查询状态信息
     *@access public
     */
    public function show_meta()
    {
        return $this->query('show meta');
    }//End Function

    /**
     * buildExcerpts 高亮显示
     *
     * @param string $text  待高亮的字符串
     * @param array  $opts  sphinx BuildExcerpts的opt参数
     * @param string $index 索引名称
     * @access public
     * @return string        添加过标签的字符串
     */
    public function buildExcerpts($text, $opts=array(), $index=null)
    {
      if(!$index) $index = $this->index;
      if(empty($opts))
      {
          $opts=array(
              'before_match'=>'<span class=\"highlight\">',
              'after_match'=>'</span>'
          );
      }
      foreach($opts as $key=>$val)
      {
          $opts_str .= " '".$val."' as ". $key .",";
      }

      $sphinxql = "CALL SNIPPETS('".addslashes($text)."' , '".$index."' , '".addslashes(implode('" "' , (array)$this->word))."' , ".substr($opts_str,0,-1)." )";
      $res = $this->query($sphinxql);
      return $res[0]['snippet'];
    }//End Function


    /**
     * getDescribe 获取对应索引中可搜索的字段
     *
     * @param string $index 索引名称
     * @access public
     * @return array $columns 可以索引字段
     */
    public function getDescribe($index=null)
    {
        if(!$index) $index = $this->index;
        $columns = app::get('search')->getConf('describe_'.$index);
        if(!$columns)
        {
            $columns = $this->set_describe($index);
        }
        return $columns;
    }

    /**
     * set_describe 设置对应索引中可搜索字段
     * @param string $index 索引名称
     * @access public
     * @return array $columns 可以索引字段
     */
    public function set_describe($index=null)
    {
        if(!$index) $index = $this->index;
        $setConfIndex = $index;
        $res = $this->query('show tables');
        foreach($res as $index_row)
        {
            if($index == $index_row['Index'] && $index_row['Type'] == 'distributed')
            {
                $index = $index.'_merge';
            }
        }
        if($index)
        {
            $sql = 'DESCRIBE '.$index;
        }
        else
        {
            return false;
            //trigger_error(app::get('search')->_('索引名称为空').mysql_error().E_USER_ERROR);
        }
        $data = $this->query($sql);
        $columns = array();
        foreach($data as $key=>$val)
        {
            if($val['Type'] != 'field')
            {//可返回字段
                $columns['int'][] = $val['Field'];
            }
            else
            {//可检索字段
                $columns['field'][] = $val['Field'];
            }
            $columns['all'][] = $val['Field'];
        }
        app::get('search')->setConf('describe_'.$setConfIndex,$columns);
        return $columns;
    }//End Function

    /**
     * 获取sphinx的运行状态
     *
     */
    public function status(&$msg)
    {
        $status = $this->query('SHOW STATUS');
        if($status[1]['Variable_name'] == 'connections' || $status[1]['Counter'] == 'connections')
        {
            $msg = '已建立连接';
            return $status;
        }
        else
        {
            $msg = '连接状态异常';
            return false;
        }
    }//End Function

    /**
     * _sphinxql 根据搜索条件生成sphinxql语句
     *
     * @access public
     * @return string $query     sphinxql语句
     */
    public function _sphinxql($cols='*', $offset=0, $limit=100000, $orderBy=null, $groupBy='')
    {
        $setting = $this->getIndexParams($this->index)['setting'];

        if( $offset <=0 ) $offset = 0;
        if( $limit <= 0 || !$limit ) $limit = $setting['max_limit'];
        $orderBy = $this->__preOrderBy($orderBy, $setting);
        $groupBy = $groupBy ? ' GROUP BY '.$groupBy : ' ';

        $option = array('ranker'=>$setting['ranker']);
        if(!empty($option))
        {
            $optionStr = ' OPTION ';
            foreach($option as $key=>$row)
            {
                if($row)
                {
                    $optionStr .= $key.'='.$row;
                }
            }
        }

        if( $this->match )
        {
            $where[] = sprintf("MATCH('%s')", addslashes($this->match));
        }

        if($this->filter)
        {
            $where[] = $this->__filter($this->filter);
        }

        if( $where )
        {
            $whereStr = ' WHERE ' . implode(' AND ',$where);
        }

        $query = 'SELECT '. $cols . ' FROM ' . $this->index . $whereStr .' '.$groupBy.' '. $orderBy.'  limit ' . $offset.','.$limit.$optionStr;

        return $query;
    }//End Function

    /**
     * 设置排序
     *
     */
    private function __preOrderBy($orderBy=null, $setting)
    {
        if( $orderBy )
        {
            $string = ' ORDER BY '.(is_array($orderBy) ? implode(' ',$orderBy) : $orderBy);
        }
        else
        {
            $string = ' ORDER BY '.$setting['order_value'].' '.$setting['order_type'];
        }

        return $string;
    }

    public function splitWords($word)
    {
        $segmentServer = config::get('search.segment');
        $segmentClass = $segmentServer[config::get('search.segment_default')];
        if( !$segmentClass ) return $word;

        $objSegment = kernel::single($segmentClass);

        return $objSegment->split_words($word);
    }

    /**
     * 检查查询条件是否需要使用sphinx进行搜索
     *
     * @param array $filter 查询条件
     *
     * @return string|bool 如果需要查询则返回处理过的filter
     */
    public function queryFilter($filter)
    {
        //查询到索引的字段
        $columns = $this->getDescribe();
        $describeInt = $columns['int'];
        $describeField = $columns['field'];
        foreach( $filter as $key=>$val )
        {
            $cols = explode('|',$key);
            $col = $cols[0];

            if( $col == 'search_keywords' )
            {
                $this->word = $this->splitWords($val);
                $searchKeyword .=  ' @* ("'.implode('" "' , (array)$this->word).'")';
            }

            if( in_array($col,$describeInt ) )
            {
                $intFilter[$key] = $val;
                continue;
            }

            if( in_array($col, $describeField) )
            {
                if( !is_array(current($val)) )
                {
                    $searchKeyword .=  ' @'.$col.' ("'.implode('" | "' , (array)$val).'")';
                }
                else
                {
                    foreach( $val as $row )
                    {
                        $searchKeyword .=  ' @'.$col.' ("'.implode('" | "' , (array)$row).'")';
                    }
                }
            }
        }

        $this->match = $searchKeyword;
        $this->filter = $intFilter;

        return  $this;
    }

    /**
     * filter sphinxql的条件组织(不包含match)
     *
     * @param array $filter
     * @access public
     * @return string
     */
    private function __filter($filter)
    {
        if(!$filter) return '';
        if(!is_array($filter)) return addslashes($filter);
        $columns = $this->getDescribe();
        foreach($filter as $key=>$val)
        {
            $type_info = explode('|',$key);
            if(!in_array($type_info[0],$columns['int']))
            {
              unset($filter[$type_info[0]]);
              continue;
            }
            $_str = $this->_inner_getFilterType($type_info[1],$val);
            if( strpos($_str,'{field}')!==false )
            {
                $where[] = str_replace('{field}',$type_info[0],$_str);
            }
            else
            {
                $where[] = $type_info[0].$_str;
            }
        }
        return implode(' AND ',$where);
    }//End Function

    /**
     * _inner_getFilterType 转换运算符号
     *
     * @param  string      $type 要转换的类型
     * @param  int|array   $var  运算符号对应的值
     * @access  public
     * @return  string
     */
    public function _inner_getFilterType($type,$var)
    {
        if(!is_array($var) && !$type)
        {
            $type = 'nequal';
        }
        if(is_array($var) && !$type)
        {
          $type = 'in';
        }
        $FilterArray=
            array(
                'than'=>' > '.$var,
                'lthan'=>' < '.$var,
                'nequal'=>' = '.$var,
                'noequal'=>' <> '.$var,
                'tequal'=>' = '.$var,
                'sthan'=>' <= '.$var,
                'bthan'=>' >= '.$var,
                'between'=>' {field}>='.$var[0].' and '.' {field}<='.$var[1],
                'in' =>" in (".implode(",",(array)$var).") ",
                'notin' =>" not in (".implode(",",(array)$var).") ",
            );
        return $FilterArray[$type];
    }//End Function

}//End Class

