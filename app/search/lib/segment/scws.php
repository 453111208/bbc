<?php

class search_segment_scws implements search_interface_segment
{
    public $name = 'scws分词组件';

    protected $_input = null;

    protected $_encoding = '';

    private $_position;

    private $_bytePosition;

    private $_cws;

    private $_resObj;

    public function __construct()
    {
        $this->_cws = scws_new();

        $scws_dict = config::get('search.scws.dict', null);
        $scws_rule = config::get('search.scws.rule', null);
        if($scws_dict){
            $this->_cws->set_dict($scws_dict);
        }
        if($scws_dict){
            $this->_cws->set_rule($scws_rule);
        }

        //1-15分词方式
        //$this->_cws->set_multi(8);
    }//End Function

    public function set($input, $encode='')
    {
        $this->_input    = $input;
        $this->_encoding = $encode;
        $this->reset();
    }

    public function reset()
    {
        $this->_position     = 0;
        $this->_bytePosition = 0;

        // convert input into UTF-8
        if (strcasecmp($this->_encoding, 'utf8' ) != 0  && strcasecmp($this->_encoding, 'utf-8') != 0 )
        {
            $this->_input = iconv($this->_encoding, 'UTF-8', $this->_input);
            $this->_encoding = 'utf-8';
        }

        $this->_cws->set_charset($this->_encoding);
        $input = $this->_input;
        $input = $this->normalize($input);
        $this->_cws->send_text($input);
        $this->_get_result();
    }

    protected function _get_result()
    {
        $rows = array();
        while($res = $this->_cws->get_result())
        {
            foreach($res AS $key=>$val)
            {
                $rows[] = $val;
            }
        }
        $obj = new arrayObject($rows);
        $this->_resObj = $obj->getIterator();
    }//End Function

    public function tokenize($input, $encode='')
    {
        $this->set($input, $encode);
        $token_list = array();
        while (($next = $this->next()) !== null)
        {
            $token_list[] = $next;
        }
        return new arrayObject($token_list);
    }

    public function next()
    {
        $res = null;

        if($this->_resObj->valid())
        {
            $res = $this->_resObj->current();
            $this->_resObj->next();
            $res['text'] = $res['word'];
            $res['text'] = mb_strtolower($res['text']);
        }

        return $res;
    }

    public function __destruct()
    {
        $this->_cws->close();
    }//End Function


    public function split_words($words)
    {
        $this->set($words, 'utf8');
        while($row = $this->next())
        {
            $res[] = $row['text'];
        }
        return $res;
    }

    private function normalize($input)
    {
        $search = array(",", "/", "\\", ".", ";", ":", "\"", "!",
                        "~", "`", "^", "(", ")", "?", "-", "\t", "\n", "'",
                        "<", ">", "\r", "\r\n", "$", "&", "%", "#",
                        "@", "+", "=", "{", "}", "[", "]", "：", "）", "（",
                        "．", "。", "，", "！", "；", "“", "”", "‘", "’", "［", "］",
                        "、", "—", "　", "《", "》", "－", "…", "【", "】",
        );
        return str_replace($search, ' ', $input);
    }//End Function

}//End Class
