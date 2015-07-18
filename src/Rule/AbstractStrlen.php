<?php

/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */

namespace Aura\Filter\Rule;

/**
 *
 * Abstract rule for string-length filters; supports the `mbstring` extension.
 *
 * @package Aura.Filter
 *
 */
abstract class AbstractStrlen
{
    /**
     *
     * Proxy to `mb_strlen()` when it exists, otherwise to `strlen()`.
     *
     * @param string $str Returns the length of this string.
     *
     * @return int
     *
     */
    protected function strlen($str)
    {
        if (! function_exists('mb_strlen')) {
            return strlen($str);
        }

        return mb_strlen($str);
    }

    /**
     *
     * Proxy to `mb_substr()` when it exists, otherwise to `substr()`.
     *
     * @param string $str The string to work with.
     *
     * @param int $start Start at this position.
     *
     * @param int $length End after this many characters.
     *
     * @param string $encoding The encoding used, UTF-8 by default
     *
     * @return string
     *
     */
    protected function substr($str, $start, $length = null)
    {
        if (! function_exists('mb_substr')) {
            return substr($str, $start, $length);
        }

        if ($length === null) {
            $length = mb_strlen($str);
        }

        return mb_substr($str, $start, $length);
    }

    protected function strpad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        if (! function_exists('mb_strlen')) {
            return str_pad($input, $pad_length, $pad_string, $pad_type);
        }

        return $this->ivanov_str_pad($input, $pad_length, $pad_string, $pad_type);
    }

    // http://php.net/manual/en/function.str-pad.php#111147
    protected function mbstrpad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $encoding = mb_internal_encoding();
        mb_internal_encoding('utf-8');

        $input_length = mb_strlen($input);
        if (! $input_length && ($pad_type == STR_PAD_RIGHT || $pad_type == STR_PAD_LEFT)) {
            $input_length = 1;
        }

        $pad_string_length = mb_strlen($pad_string);
        $result = null;
        $repeat = ceil($input_length - $pad_string_length + $pad_length);

        if (! $pad_length || ! $pad_string_length || $pad_length <= $input_length) {
            $result = $input;
        } elseif ($pad_type == STR_PAD_RIGHT) {
            $result = $input . str_repeat($pad_string, $repeat);
            $result = mb_substr($result, 0, $pad_length);
        } elseif ($pad_type == STR_PAD_LEFT) {
            $result = str_repeat($pad_string, $repeat) . $input;
            $result = mb_substr($result, -$pad_length);
        } elseif ($pad_type == STR_PAD_BOTH) {
            $length = ($pad_length - $input_length) / 2;
            $repeat = ceil($length / $pad_string_length);
            $result = mb_substr(str_repeat($pad_string, $repeat), 0, floor($length))
                    . $input
                    . mb_substr(str_repeat($pad_string, $repeat), 0, ceil($length));
        }

        mb_internal_encoding($encoding);
        return $result;
    }

    protected function ivanov_str_pad($input, $length, $pad_str = " ", $type = STR_PAD_RIGHT, $encoding = "UTF-8") {
        //return str_pad($str, strlen($str)-$this->strlen($str,$encoding)+$pad_length, $pad_string, $pad_type);

        $input_len = $this->strlen($input,$encoding);
        if ($length <= $input_len)
            return $input;
        $pad_str_len = $this->strlen($pad_str,$encoding);
        $pad_len = $length - $input_len;
        if ($type == STR_PAD_RIGHT) {
            $repeat_times = ceil($pad_len / $pad_str_len);
            return $this->substr($input . str_repeat($pad_str, $repeat_times), 0, $length,$encoding);
        }
        if ($type == STR_PAD_LEFT) {
            $repeat_times = ceil($pad_len / $pad_str_len);
            return $this->substr(str_repeat($pad_str, $repeat_times), 0, floor($pad_len),$encoding) . $input;
        }
        if ($type == STR_PAD_BOTH) {
            $pad_len /= 2;
            $pad_amount_left = floor($pad_len);
            $pad_amount_right = ceil($pad_len);
            $repeat_times_left = ceil($pad_amount_left / $pad_str_len);
            $repeat_times_right = ceil($pad_amount_right / $pad_str_len);
            $padding_left = $this->substr(str_repeat($pad_str, $repeat_times_left), 0, $pad_amount_left,$encoding);
            $padding_right = $this->substr(str_repeat($pad_str, $repeat_times_right), 0, $pad_amount_right,$encoding);
            return $padding_left . $input . $padding_right;
        }
    }
}