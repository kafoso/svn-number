<?php
namespace Kafoso\SvnNumber\Bash;

class Command {
    public function exec($cmd){
        exec("{$cmd} 2>&1", $output, $return);
        if ($return === 0) {
            return $output;
        }
        throw new \RuntimeException(sprintf(
            "Shell command error: %s",
            implode(PHP_EOL, $output)
        ));
    }

    public function getMaxTerminalColumns(){
        exec("tput cols", $output, $return);
        if ($return === 0) {
            if (is_array($output)) {
                return intval($output[0]);
            }
            return 0;
        }
        throw new \RuntimeException(sprintf(
            "Shell command error: %s",
            implode(PHP_EOL, $output)
        ));
    }
}
