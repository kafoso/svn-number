<?php
namespace Kafoso;

use Kafoso\SvnNumber\Bash\Command as BashCommand;
use Kafoso\SvnNumber\SvnAction\Diff;
use Kafoso\SvnNumber\SvnAction\Status;
use Kafoso\SvnNumber\Argument\NumberNegotiator;

class SvnNumber {
    protected $bashCommand;

    protected $requestedNumbers = array();
    protected $action = null;
    protected $additionalArgs = array();
    protected $diff;
    protected $status;

    public function __construct(array $args, BashCommand $bashCommand){
        $this->bashCommand = $bashCommand;
        foreach (array_slice($args, 1, 2) as $arg) {
            if (!$this->requestedNumbers) {
                $numberNegotiator = new NumberNegotiator($arg);
                if ($numberNegotiator->isMatch()) {
                    $this->requestedNumbers = $numberNegotiator->getNumbers();
                    if ($numberNegotiator->hasExceptions()) {
                        echo "<pre>";var_dump("Kafoso ".__FILE__."::".__LINE__, $numberNegotiator->getExceptions()[0]->getTraceAsString());die("</pre>");
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
        return implode(" ", $this->additionalArgs);
    }

    public function getDiff(){
        if (!$this->diff) {
            $this->diff = new Diff($this);
        }
        return $this->diff;
    }

    public function getRequestedNumbers(){
        return $this->requestedNumbers;
    }

    public function getStatus(){
        if (!$this->status) {
            $this->status = new Status;
        }
        return $this->status;
    }

    public function hasAction(){
        return false == empty($this->action);
    }

    public function hasRequestedNumbers(){
        return sizeof($this->requestedNumbers) > 0;
    }
}
