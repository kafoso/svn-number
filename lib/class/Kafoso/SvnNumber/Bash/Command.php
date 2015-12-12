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
            $output
        ));
    }
}
