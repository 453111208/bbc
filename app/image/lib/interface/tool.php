<?php
interface image_interface_tool 
{
    public function resize($src_file, $target_file, $width, $height, $type, $new_width, $new_height);
    public function watermark($file, $mark_image, $set);
}
