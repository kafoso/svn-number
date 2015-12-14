<?php
namespace Kafoso\SvnNumber\Bash;

/**
 * Colorization and styling of textual terminal output.
 * Inspiration: http://unix.stackexchange.com/questions/124407/what-color-codes-can-i-use-in-my-ps1-prompt
 */
class Styling {
    const BOLD = "\33[1m";
    const DEFAULT_FOREGROUND_COLOR = 253;
    const ESCAPE = "\33[0m";
    const FOREGROUND_COLOR_PATTERN = "\33[38;5;%FOREGROUND_COLOR%m";
    const BACKGROUND_COLOR_PATTERN = "\33[48;5;%BACKGROUND_COLOR%m";

    public function normal($str, $foregroundColor = null, $backgroundColor = null, $escape = false){
        if (is_null($foregroundColor)) {
            $foregroundColor = self::DEFAULT_FOREGROUND_COLOR;
        }
        $output = $this->constructColorSequence($foregroundColor, $backgroundColor) . $str;
        if ($escape) {
            $output .= self::ESCAPE;
        }
        return $output;
    }

    public function bold($str, $foregroundColor = null, $backgroundColor = null, $escape = false){
        if (is_null($foregroundColor)) {
            $foregroundColor = self::DEFAULT_FOREGROUND_COLOR;
        }
            return self::BOLD . $this->normal($str, $foregroundColor, $backgroundColor, $escape);
    }

    public function escape($str = null){
        return strval($str) . self::ESCAPE;
    }

    protected function constructColorSequence($foregroundColor, $backgroundColor = null) {
        $sequence = "";
        if ($backgroundColor) {
            $backgroundColor = str_pad($backgroundColor, 3, "0", STR_PAD_LEFT);
            $sequence .= str_replace(
                "%BACKGROUND_COLOR%",
                $backgroundColor,
                self::BACKGROUND_COLOR_PATTERN
            );
        }
        $foregroundColor = str_pad($foregroundColor, 3, "0", STR_PAD_LEFT);
        return $sequence . str_replace(
            "%FOREGROUND_COLOR%",
            $foregroundColor,
            self::FOREGROUND_COLOR_PATTERN
        );
    }
}
