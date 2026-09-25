<?php

namespace App\Services\Utility;

class ProductUtility
{
    public static function get_attribute_options($collection)
    {
        $options = array();
        if (isset($collection['choice_no']) && $collection['choice_no']) {
            foreach ($collection['choice_no'] as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();

                if (!is_null(request()[$name])) {
                    foreach (request()[$name] as $eachValue) {
                        array_push($data, $eachValue);
                    }
                }
                if (count($data) == 0) {
                    continue;
                }

                array_push($options, $data);
            }
        }

        return $options;
    }

    public static function get_combination_string($combination, $collection)
    {
        $str = '';
        foreach ($combination as $key => $item) {
            $attribute_name = \App\Models\AttributeValue::where('id', $item)->first()->name;
            if ($key > 0) {
                $str .= '-';
            }
            $str .= str_replace(' ', '', $attribute_name);
        }
        return $str;
    }
}
