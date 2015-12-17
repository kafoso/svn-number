<?php
namespace Kafoso;

use Kafoso\SvnNumber\Bash\Command as BashCommand;
use Kafoso\SvnNumber\Argument\NumberNegotiator;

class SvnNumber {
    protected $bashCommand;

    protected $requestedNumbers = array();
    protected $action = null;
    protected $additionalArgs = array();

    public function __construct(array $args, BashCommand $bashCommand){
        $this->bashCommand = $bashCommand;
        foreach (array_slice($args, 1, 2) as $arg) {
            if (!$this->requestedNumbers) {
                $numberNegotiator = new NumberNegotiator($arg);
                if ($numberNegotiator->isMatch()) {
                    $this->requestedNumbers = $numberNegotiator->getNumbers();
                    if ($numberNegotiator->hasExceptions()) {
                        throw new \RuntimeException(sprintf(
                            "NumberNegotiator Exceptions: " . PHP_EOL . "%s",
                            $numberNegotiator->getExceptionsAsString()
                        ));
                    }
                } else {
                    $this->action = $arg;
                }
            }
        }
        if ($this->requestedNumbers) {
            $this->additionalArgs = array_slice($args, 3);
        } else {
            $this->action = $args[1];
            $this->additionalArgs = array_slice($args, 2);
        }
    }

    public function exec($cmd){
        echo $cmd . PHP_EOL;
        return $this->bashCommand->exec($cmd);
    }

    public function getAction(){
        return $this->action;
    }

    public function getAdditionalArgs(){
        return $this->additionalArgs;
    }

    public function getAdditionalArgsStr(){
        return implode(" ", array_map(function($arg){
            if (preg_match("/^[^-\|]/", $arg)) {
                return escapeshellarg($arg);
            }
            return $arg;
        }, $this->additionalArgs));
    }

    public function getBashCommand(){
        return $this->bashCommand;
    }

    public function getRequestedNumbers(){
        return $this->requestedNumbers;
    }

    public function hasAction(){
        return false == empty($this->action);
    }

    public function hasRequestedNumbers(){
        return sizeof($this->requestedNumbers) > 0;
    }
}
