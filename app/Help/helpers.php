<?php

/**
 *  统一回参接口 将null 转化位空字符串
 */
if (!function_exists('nullToStr')) {

    function nullToStr($arr)
    {
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                $arr[$k] = '';
            }
            if (is_array($v)) {
                $arr[$k] = nullToStr($v);
            }
        }

        return $arr;
    }
}

/**
 *  统一回参接口 将null 转化位空字符串
 */
if (!function_exists('randStr')) {

    function randStr($length)
    {
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($length) - 1;
        $randstr = '';
        for ($i = 0; $i < $len; $i++) {
            $num = random_int(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}
