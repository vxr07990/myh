<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/29/2016
 * Time: 10:06 AM
 */

namespace MoveCrm;

use PearDatabase;
use DateTimeField;
use Exception;

class InputUtils
{
    public static function CheckboxToBool($input)
    {
        if (in_array(strtolower($input), ['on', 'yes', 'true', 'y'])) {
            return 1;
        }
        if ((int)$input) {
            return 1;
        }
        return 0;
    }

    public static function MultiselectIntersects($input, $compare)
    {
        $input = explode(' |##| ', $input);
        $compare = explode(' |##| ', $compare);
        if (count(array_intersect($input, $compare)) === 0) {
            return false;
        }
        return true;
    }

    public static function encodeURIComponent($str)
    {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }
}
