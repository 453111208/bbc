<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class desktop_ctl_code extends base_routing_controller
{
    public function __construct($app)
    {
    }

    //激活码校验
    public function codecheck()
    {
        //if ($_POST['auth_code'] && preg_match("/^\d{19}$/", substr($_POST['auth_code'], 1)))
        if ($_POST['auth_code'])
        {
            $code = kernel::single('desktop_cert_certcheck');
            $result = $code->check_code($_POST['auth_code']);
            if ($result['res'] == 'succ' && $result)
            {
                $activation_arr = $_POST['auth_code'];
                app::get('desktop')->setConf('activation_code', $activation_arr);

                $objArr = kernel::servicelist("desktop.cert.succ");
                foreach ($objArr as $obj)
                {
                    if(method_exists($obj , 'notify')){
                        $obj->notify($result);
                    }
                }

                header('Location:' .url::route('shopadmin'));
                exit;
            }
            else
            {
                switch ($result['msg'])
                {
                    case 'key_false_type':
                        $auth_error_msg = '激活码类型不对!';break;
                    case 'key_false_ac':
                        $auth_error_msg = '验证标签错误!';break;
                    case 'key_false_expir':
                        $auth_error_msg = '此激活码大于最大有效期限制!';break;
                    case 'key_false_times':
                        $auth_error_msg = '失败：您已经连续6次提交失败，为了您的网店安全，请3小时后再次尝试|';break;
                    case 'key_false_key':
                        $auth_error_msg = '无效的激活码，请您重新输入激活码以便正常使用。';break;
                    case 'key_false_actived':
                        $auth_error_msg = '您的激活码已经失效，请您重新输入激活码以便正常使用。';break;
                    case 'key_false_oem':
                        $auth_error_msg = '您的网店License与输入的激活码类型不一，请联系激活码销售商!';break;
                    case 'key_false_type_1':
                        $auth_error_msg = '您的网店License与输入的激活码类型不一，请联系激活码销售商!';break;
                    case 'key_false_type_2':
                        $auth_error_msg = '您的网店License与输入的激活码类型不一，请联系激活码销售商!';break;
                    case 'certificate_id_is_false':
                        $auth_error_msg = ' 您的网店证书有误，请查证!';break;
                    case 'temp_key_false':
                        $auth_error_msg = '临时激活码激活失败,此站点已经用正式激活码激活过了，不能再用临时激活码';break;
                    case 'active_key_false':
                        $auth_error_msg = '激活错误,此激活码已被使用或者激活码输入错误';break;
                }

                die($this->error_view($auth_error_msg));
            }

            header("Location: index.php");
            exit();
        }
    }

    function error_view($auth_error_msg)
    {
        $pagedata['res_url'] = app::get('desktop')->res_url;
        $pagedata['auth_error_msg'] = $auth_error_msg;
        return view::make('desktop/active_code.html', $pagedata);
    }
}
