<?php
namespace Kafoso\SvnNumber;

class BashStyling {
    /**
     * Inspiration: http://unix.stackexchange.com/questions/124407/what-color-codes-can-i-use-in-my-ps1-prompt
     */
    public function normal($color, $str){
        return "\e[38;5;{$color}m{$str}\e[0m";
    }
    public function bold($color, $str){
        return "\e[1m\e[38;5;{$color}m{$str}\e[0m";
    }
}
