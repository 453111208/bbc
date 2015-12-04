<?php
/**
* 这个类加载用户自定义密码加密方式
*/
class pam_encrypt
{
	/**
	* 得到自定义加密方式的密文
	* @param string $password 密码明文
	* @param string $account_type 加密类型，对应的类文件
	* @return string 返回加密后的密文
	*/
    //public static function get_encrypted_password($password,$account_type,$userdata=null){

    public static function make($password, base_hashing_hasher_interface $hasher = null)
    {
        if (!$hasher)
        {
            $hasher = kernel::single('base_hashing_hasher_bcrypt');
        }
        return $hasher->make($password);
    }

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param  string  $value
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool
	 */
	public static function check($value, $hashedValue, base_hashing_hasher_interface $hasher = null )
	{
        if (!$hasher)
        {
            $hasher = kernel::single('base_hashing_hasher_bcrypt');
        }
        logger::error(var_export(array($value, $hashedValue), 1));
        return $hasher->check($value, $hashedValue);
    }

}
