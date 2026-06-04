<?php
// In src/Validation/CustomValidator.php

namespace App\Model\Validation;

use Cake\Validation\Validator;

// 未使用
class CustomValidator extends Validator
{
    public static function isValidYear($value)
    {
        // 1900年以降、4桁の整数
        return (bool) preg_match('/^19[0-9]{2}|[2][0-9]{3}$/', $value);
    }

    public static function isMobilePhoneNumber($value)
    {
        // 携帯番号（070/080/090から始まる11桁、ハイフンあり）
        return (bool) preg_match('/^0[789]0-[0-9]{4}-[0-9]{4}$/', $value);
    }

    public static function isLandlinePhoneNumber($value)
    {
        // 固定電話（市外局番から始まる、ハイフンあり）
        return (bool) preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/', $value);
    }

    public static function noSpaceStartEnd($value)
    {
        // 全角スペースが含まれていないこと、かつ先頭と末尾に空白(半角・全角)がないことを確認
        return !preg_match('/　/', $value) && !preg_match('/^[ 　]|[ 　]$/u', $value);
    }
}
