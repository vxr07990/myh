<?php
/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_ThemeColor_Helper {
    public $baseColor;

    /* ShadeColor */

    public function shadeColor($percent) {
        $num = base_convert(substr($this->baseColor, 1), 16, 10);
        $amt = round(2.55 * $percent);
        $r = ($num >> 16) + $amt;
        $b = ($num >> 8 & 0x00ff) + $amt;
        $g = ($num & 0x0000ff) + $amt;

        return '#'.substr(base_convert(0x1000000 + ($r<255?$r<1?0:$r:255)*0x10000 + ($b<255?$b<1?0:$b:255)*0x100 + ($g<255?$g<1?0:$g:255), 10, 16), 1);
    }
}
