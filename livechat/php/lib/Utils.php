<?php

class Utils
{

    private static function _arrayMergeRecursive($array1, $array2)
    {
        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
            {
                $array1[$key] = self::arrayMergeRecursive($array1[$key], $value);
            }
            else
            {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    public static function arrayMergeRecursive()
    {
        $result = array();
        $arrays = func_get_args();
        
        foreach($arrays as $array)
        {
            $result = self::_arrayMergeRecursive($result, $array);
        }

        return $result;
    }

}

?>