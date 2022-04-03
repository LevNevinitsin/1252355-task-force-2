<?php

namespace app\components;

use yii\i18n\Formatter;

class FormatterHelper extends Formatter {
    /**
     * Formats 79991234567 as +7 (999) 123-45-67
     *
     * @param string $value
     * @return string
     */
    public function asPhone(string $value): string
    {
        return preg_replace("/^(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/", "+$1 ($2) $3-$4-$5", $value);
    }
}
