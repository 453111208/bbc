<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_syscache_adapter_filesystem extends base_syscache_adapter_abstract implements base_interface_syscache_adapter{

    private $_controller = null;

    protected $_handler = null;

    public $_data = array();

    private $rs = null;

    private $maxSize = 1048576; 

    public function __construct($handler)
    {
        parent::__construct($handler);
        if (kernel::single('base_filesystem')->exists($this->_get_pathname()))
        {
            $this->rs = fopen($this->_get_pathname(), 'rb+');
        }
        else
        {
            $this->rs = fopen($this->_get_pathname(), 'wb+');
        }
    }
    
    private function _get_pathname() {
        return TMP_DIR.'/'.$this->get_key();
    }

    public function init_data()
    {
        if ($this->lockShare())
        {
            fseek($this->rs, 0);
            $ret = unpack('V', $a= fread($this->rs, 4));
            $length = $ret[1];
            if ($length > $this->maxSize)
            {
                $this->unlock();
                return false;
            }
            
            $originalData = fread($this->rs, $length);
            $this->unlock();
            //if (($data = unserialize(base64_decode($originalData))) !== false)
            if (($data = unserialize($originalData)) !== false)
            {
                $this->_data = $data;
                return true;
            }
            return false;
        }
        else
        {
            throw new \RuntimeException('Couldn\'t lock the file :'.$this->_get_pathname());
        }
    }
    
    public function create($data)
    {
        //$data = base64_encode(serialize($data));
        $data = serialize($data);
        $length = strlen($data);
        if ($length>$this->maxSize) throw new \RuntimeException('syscache couldn\'t save over '.$this->maxSize.' byte.');
            
        if ($this->lockExclusive())
        {
            // ftruncate($this->rs, 0);
            fseek($this->rs, 0);
            fputs($this->rs, pack('V', $length));
            fputs($this->rs, $data);
             $this->unlock();
        }
        else
        {
            throw new \RuntimeException('Couldn\'t lock the file !');
        }
    }

    public function get($key)
    {
        if ($key===null) return $this->_data;
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
    }


    protected function lockShare()
    {
        return $this->lock(LOCK_SH);
    }

    
    protected function lockExclusive()
    {
        return $this->lock(LOCK_EX);
    }

    /**
     * lock
     * 如果flock不管用，请继承本类，并重载此方法
     *
     * @param mixed $is_block 是否阻塞
     * @access public
     * @return void
     */
    protected function lock($operation)
    {
        ignore_user_abort(true);
        return flock($this->rs, $operation);
    }

    /**
     * unlock
     * 如果flock不管用，请继承本类，并重载此方法
     *
     * @access public
     * @return void
     */
    function unlock(){
        ignore_user_abort(false);
        return flock($this->rs, LOCK_UN);
    }
    
}


