<?php

namespace App\Support;

class CsvSafe
{
    /**
     * Excel/Sheets formula-injection koruması.
     *
     * `=`, `+`, `-`, `@` (ve tab/CR) ile başlayan kullanıcı girdileri tablo
     * programlarında formül olarak çalışabilir. Başına tek tırnak koyarak
     * hücreyi düz metne zorlar. Sayısal/null değerler olduğu gibi geçer.
     */
    public static function cell(mixed $value): mixed
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
