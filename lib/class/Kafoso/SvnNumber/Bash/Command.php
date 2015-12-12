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
        $out = $this->exec("tput cols");
        if (is_array($out)) {
            return intval($out[0]);
        }
        return 0;
    }
}
