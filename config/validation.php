<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "int"                  => "参数 :attribute 必须为int型 .",
    "accepted"             => "参数 :attribute 的值必须是 yes、 on 或者是 1",
    "active_url"           => "参数 :attribute 不是有效的URL地址.",
    "after"                => "参数 :attribute 的值必须在给定日期之后，.",
    "alpha"                => "参数 :attribute 只能包含字母.",
    "alpha_dash"           => "参数 :attribute 只能由字母，数字和中划线或下划线字符构成.",
    "alpha_num"            => "参数 :attribute 必须由字母和数字构成.",
    "array"                => "参数 :attribute 必须是一个数组.",
    "before"               => "参数 :attribute 必须在给定的日期 :date 之前.",
    "between"              => array(
        "numeric" => "数字参数 :attribute 必须在 :min 和 :max 数值之间.",
        "file"    => "文件参数 :attribute 必须在 :min 和 :max 千字节之间.",
        "string"  => "字符串参数 :attribute 必须在 :min 和 :max 字符数之间.",
        "array"   => "数组参数 :attribute 必须在 :min 和 :max 数量之间.",
    ),
    "boolean"              => "参数 :attribute 必须是boolean类型（true, false, 0, 1, '0', '1'）.",
    "confirmed"            => "参数 :attribute 必须和参数 :attribute_confirmation 的值相同.",
    "date"                 => "参数 :attribute 不是一个有效的日期.",
    "date_format"          => "参数 :attribute 不符合给定的 :format 的格式.",
    "different"            => "参数 :attribute 和参数 :other 必须不同.",
    "digits"               => "参数 :attribute 此规则的值必须是一个 :digits 数字.",
    "digits_between"       => "参数 :attribute 必须在 :min 和 :max 数字之间.",
    "email"                => "参数 :attribute 必须是一个有效的邮件地址",
    "exists"               => "参数 选择的 :attribute 是无效的",
    "image"                => "参数 :attribute 必须为一张图片",
    "in"                   => "参数 选择的 :attribute 不在可选值范围内.",
    "integer"              => "参数 :attribute 必须为整数",
    "ip"                   => "参数 :attribute 必须是一个有效的IP地址.",
    "max"                  => array(
        "numeric" => "数字参数 :attribute 不能 大于 :max",
        "file"    => "文件参数 :attribute 不能 大于 :max 千字节.",
        "string"  => "字符串参数 :attribute 不能 大于 :max 字符.",
        "array"   => "数组参数 :attribute 可能 包含大于 :max 数量.",
    ),
    "mimes"                => "参数 :attribute 文件的类型必须满足 :values .",
    "min"                  => array(
        "numeric" => "数字参数 :attribute 至少大于 :min 最小的数值.",
        "file"    => "文件参数 :attribute 至少大于 :min 千字节（KB）.",
        "string"  => "字符串参数 :attribute 至少是 :min 字母.",
        "array"   => "数组参数 :attribute 至少有 :min 数量.",
    ),
    "not_in"               => "存在了参数 :attribute ",
    "numeric"              => "参数 :attribute 必须是一个数字.",
    "regex"                => "参数 :attribute 格式是无效.",
    "required"             => "参数 :attribute 必填.",
    "required_if"          => "参数 :attribute 必填，当参数 :other 的值等于 :value .",
    "required_with"        => "参数 :attribute 必填，当其值 :values 存在 .",
    "required_with_all"    => "参数 :attribute 必填, 当其他所有参数 :values 都存在.",
    "required_without"     => "参数 :attribute 仅当 其它指定的字段 :values 不存在的时候，验证此规则的值必须存在。.",
    "required_without_all" => "参数 :attribute 仅当 其它指定的字段 :values 都不存在的时候，验证此规则的值必须存在.",
    "same"                 => "参数 :attribute 必须 :other 一致.",
    "size"                 => array(
        "numeric" => "数字参数 :attribute 必须与 :size 大小相同.",
        "file"    => "文件参数 :attribute 必须与 :size 字节相同.",
        "string"  => "字符串参数 :attribute 必须与 :size 字符相同.",
        "array"   => "数组参数大小 :attribute 必须包含 :size 数量.",
    ),
    "unique"               => "参数 :attribute 必须是唯一的.",
    "url"                  => "参数 :attribute 无效的URL",
    "timezone"             => "参数 :attribute 必须为一个有效的时区.",
    "mobile"             => "参数 :attribute 必须为一个有效的手机号.",
    "tel"             => "参数 :attribute 必须为一个有效的固定电话.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => array(
        'attribute-name' => array(
            'rule-name' => 'custom-message',
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => array(),

);
