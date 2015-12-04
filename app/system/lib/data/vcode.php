<?php
class system_data_vcode extends base_facades_facade{
    /**
     * Return the Request instance
     *
     * @var \Symfony\Component\HttpFoundation\Request;
     */

    private static $__request;

    protected static function getFacadeAccessor() {
        if (!static::$__request)
        {
            static::$__request = new system_data_user_vcode();

        }
        return static::$__request;
    }

}
