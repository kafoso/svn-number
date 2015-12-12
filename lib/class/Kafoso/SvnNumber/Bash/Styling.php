<?php
namespace Kafoso\SvnNumber\Bash;

use Kafoso\SvnNumber\Bash\Command as BashCommand;

class Styling {
    protected $bashCommand;

    public function __construct(BashCommand $bashCommand){
        $this->bashCommand = $bashCommand;
    }

    /**
     * Inspiration: http://unix.stackexchange.com/questions/124407/what-color-codes-can-i-use-in-my-ps1-prompt
     */
    public function normal($str, $foregroundColor = null, $backgroundColor = null){
        if (is_null($foregroundColor)) {
            $foregroundColor = 231;
        }
        return $this->constructColorSequence($foregroundColor, $backgroundColor) . "{$str}\33[0m";
    }

    public function bold($str, $foregroundColor = null, $backgroundColor = null){
        if (is_null($foregroundColor)) {
            $foregroundColor = 231;
        }
        return "\33[1m" . $this->constructColorSequence($foregroundColor, $backgroundColor) . "{$str}\33[0m";
    }

    public function getMaxTerminalColumns(){
        $out = $this->bashCommand->exec("tput cols");
        if (is_array($out)) {
            return intval($out[0]);
        }
        return 0;
    }

    protected function constructColorSequence($foregroundColor, $backgroundColor = null) {
        $sequence = "";
        if ($backgroundColor) {
            $sequence .= sprintf(
                "\33[48;5;%sm",
                $backgroundColor
            );
        }
        return sprintf(
            "%s\33[38;5;%sm",
            $sequence,
            $foregroundColor
        );
    }
}