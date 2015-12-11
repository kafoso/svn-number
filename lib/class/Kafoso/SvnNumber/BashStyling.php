<?php
namespace Kafoso\SvnNumber;

class BashStyling {
    /**
     * Inspiration: http://unix.stackexchange.com/questions/124407/what-color-codes-can-i-use-in-my-ps1-prompt
     */
    public function normal($str, $foregroundColor = null, $backgroundColor = null){
        if (is_null($foregroundColor)) {
            $foregroundColor = 231;
        }
        return "\33[38;5;{$foregroundColor}m{$str}\33[0m";
    }
    public function bold($str, $foregroundColor = null, $backgroundColor = null){
        if (is_null($foregroundColor)) {
            $foregroundColor = 231;
        }
        return "\33[1m\33[38;5;{$foregroundColor}m{$str}\33[0m";
    }
}
