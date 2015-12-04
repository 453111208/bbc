<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*
 * @package base
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license
 */
class base_setup_lock
{

    /*
     * @var string $lockcode_prefix
     * @access private
     */
    private $lockcode_prefix = "If you want to reinstall system, delete this file! <?php exit();?> \ncode: ";

    /*
     * @var string $codecookie_name
     * @access private
     */
    private $codecookie_name = '_ecos_setup_lockcode';

    /*
     * lockfile路径
     * @access private
     * @return string
     */
    private function lockfile()
    {
        return ROOT_DIR . '/config/install.lock.php';
    }//End Function

    /*
     * 写入lockfile文件
     * @access private
     * @return boolean
     */
    private function put_lockfile($content)
    {
        return file_put_contents($this->lockfile(), $content);
    }//End Function

    /*
     * 写入lockfile文件
     * @access private
     * @return string
     */
    private function get_lockfile()
    {
        return file_get_contents($this->lockfile());
    }//End Function

    /*
     * 检查是否有lock文件
     * @access public
     * @return boolean
     */
    public function lockfile_exists()
    {
        return file_exists($this->lockfile());
    }//End Function

    /*
     * 写入锁文件
     * @access public
     * @return string
     */
    public function write_lock_file($cookie=true){
        $lock_code = md5(microtime()).md5(print_r($_SERVER,1));
        if($this->put_lockfile($this->lockcode_prefix.$lock_code)){
            $path = kernel::base_url();
            $path = $path?$path:'/';
            if($cookie) setcookie($this->codecookie_name,$lock_code,0,$path);
            return true;
        }else{
            return false;
        }
    }

    /*
     * 读取锁码
     * @access public
     * @return string
     */
    public function get_lock_code()
    {
        $content = $this->get_lockfile($this->lockfile());
        $ncode = substr($content, strlen($this->lockcode_prefix));
        return $ncode;
    }//End Function

    /*
     * 验证锁码
     * @access public
     * @return string
     */
    public function check_lock_code()
    {
        if(isset($_COOKIE[$this->codecookie_name])){
            $code = $this->get_lock_code();
            if($code && $this->get_lock_code() === $_COOKIE[$this->codecookie_name]){
                return true;
            }
        }
        return false;
    }//End Function

}//End Class
