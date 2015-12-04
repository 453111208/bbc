<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class base_vcode_gd
{
    //图片资源
    private $image=null;
    //生成的验证码字的个数
    public $codeNum;
    //验证码高度
    public $height = 35;
    //验证码宽度
    public $width = 100;
    //干扰元素数量
    private $disturbColorNum;
    //生成的code
    public $code='';
    //是否生成中文验证码

    public function setPicSize($height=35, $width=100)
    {
        $this->height = $height;
        $this->width = $width;
    }

    public function length($length)
    {
        //设置干扰元素数量
        $number=floor($this->width*$this->height/20);
        if($number > 240-$this->codeNum)
        {
            $this->disturbColorNum=	240-$this->codeNum;
        }
        else
        {
            $this->disturbColorNum=$number;
        }
        $this->codeNum = $length;

        //生成code
        $this->createCode();
    }

    //显示验证码图片
    public function display()
    {

        if( is_file(app::get('base')->res_dir.'/fonts/Menlo.ttc') )
        {
            $fontFace = app::get('base')->res_dir.'/fonts/Menlo.ttc';
        }

        //创建图片资源
        $this->createImage();
        //设置干扰颜色
        //$this->setDisturbColor();

        //往图片上添加文本
        $this->outputText($fontFace);

        $this->distortionText();

        //输出图像
        $this->ouputImage();
    }

    public function get_code()
    {
        return strtolower($this->code);
    }

    //创建图片 无边框
    private function createImage()
    {
        //生成图片资源
        $this->image=imagecreatetruecolor($this->width,$this->height);
        //画出图片背景
        $this->backColor=imagecolorallocate($this->image,mt_rand(255,255),mt_rand(255,255),mt_rand(255,255));
        imagefill($this->image,0,0,$this->backColor);
    }

    //设置干扰颜色
    private function setDisturbColor()
    {
        //画出点干扰
        for($i=0;$i<=$this->disturbColorNum;$i++)
        {
            $pixelColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$pixelColor);
        }
        //画出干扰线
        //$this->__wirteSinLine();
    }

    //@扭曲文字
    public function distortionText()
    {
        $this->distortionImage = imagecreatetruecolor($this->width, $this->height);
        imagefill($this->distortionImage, 0, 0, $this->backColor);
        for ($x = 0; $x < $this->width; $x++)
        {
            for ($y = 0; $y < $this->height; $y++)
            {
                $rgbColor = imagecolorat($this->image, $x, $y);
                imagesetpixel($this->distortionImage, (int) ($x + sin($y / $this->height * 2 * M_PI - M_PI * 0.5) * 3), $y, $rgbColor);
            }
        }
        $this->image = $this->distortionImage;
    }

    //往图片上添加文本
    private function outputText($fontFace='')
    {
        //画出code
        for($i=0;$i<$this->codeNum;$i++)
        {
            $fontColor=imagecolorallocate($this->image,0,0,255);
            //设置了fontFace 则使用imagettftext
            if($fontFace)
            {
                $fontSize=mt_rand($this->width/$this->codeNum-5,$this->width/$this->codeNum-4);
                $x=floor(($this->width-3)/$this->codeNum)*$i+5;
                $y=mt_rand($fontSize, $this->height-5);
                imagettftext($this->image,$fontSize,mt_rand(-30, 30),$x,$y ,$fontColor, $fontFace,self::msubstr($this->code,$i));
            }
            else
            {
                //没有设置 fontFace 则使用 imagechar
                $fontSize=mt_rand(4,6);
                $x=floor($this->width/$this->codeNum)*$i+3;
                $y=mt_rand(0,$this->height-20);
                imagechar($this->image,$fontSize,$x,$y,$this->code{$i},$fontColor);
            }
        }
    }

    //生成code
    private function createCode()
    {
        if( base_mobiledetect::isMobile() && $_COOKIE['browse'] != 'pc')
        {
            $string = '0123456789';
        }
        else
        {
            $string='0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $code='';
        for($i=0;$i<$this->codeNum;$i++)
        {
            $char=self::msubstr($string,mt_rand(0,mb_strlen($string,'utf-8')-1));
            $this->code.=$char;
        }
    }

    //输出图像
    private function ouputImage()
    {
        ob_clean();	//防止出现'图像因其本身有错无法显示'的问题
        if(imagetypes() & IMG_GIF)
        {
            header("Content-Type:image/gif");
            imagepng($this->image);
        }
        else if(imagetypes() & IMG_JPG)
        {
            header("Content-Type:image/jpeg");
            imagepng($this->image);
        }
        else if(imagetypes() & IMG_PNG)
        {
            header("Content-Type:image/png");
            imagepng($this->image);
        }
        else
        {
            header("Content-Type:image/vnd.wap.wbmp");
            imagepng($this->image);
        }
    }

    //画正弦干扰线
    private function __wirteSinLine($w=100)
    {
        $color = imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));

        $img=$this->image;

        $h=$this->height;
        $h1=rand(-5,5);
        $h2=rand(-1,1);
        $w2=rand(10,15);
        $h3=rand(4,6);
        $funArr = array('sin','cos');
        $funKey = rand(0,1);
        $fun = $funArr[$funKey];
        for($i=-$w/2;$i<$w/2;$i=$i+0.1)
        {
            $y=$h/$h3*$fun($i/$w2)+$h/2+$h1;
            imagesetpixel($img,$i+$w/2,$y,$color);
            $h2!=0?imagesetpixel($img,$i+$w/2,$y+$h2,$color):null;
        }
    }

    /*
     * msubstr() 截取字符串
     *
     */
    static private function msubstr($str, $start=0, $length=1, $charset="utf-8")
    {
        if(function_exists("mb_substr"))
        {
            $slice = mb_substr($str, $start, $length, $charset);
        }
        elseif(function_exists('iconv_substr'))
        {
            $slice = iconv_substr($str,$start,$length,$charset);
        }
        else
        {
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $slice;
    }

    // 摧毁资源
    public function __destruct()
    {
        imagedestroy($this->image);
    }
}
