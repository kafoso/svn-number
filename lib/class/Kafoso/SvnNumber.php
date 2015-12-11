<?php
namespace Kafoso;

use Kafoso\SvnNumber\Diff;
use Kafoso\SvnNumber\Status;
use Kafoso\SvnNumber\NumberHandler;

class SvnNumber {
    protected $requestedNumbers = array();
    protected $svnCommand;
    protected $additionalArgs = array();

    protected $diff;
    protected $status;

    public function __construct($args){
        foreach (array_slice($args, 1, 2) as $arg) {
            if (preg_match("/^\d+[,-\d]*?$/", $arg)) {
                $numbers = array();
                $exceptions = array();
                $chunks = explode(",", $arg);
                $numberHandler = new NumberHandler;
                foreach ($chunks as $chunk) {
                    try {
                        if (preg_match("/^(\d)+-(\d+)$/", $chunk)) {
                            $rangeArray = $numberHandler->stringRangeToArray($chunk);
                            $numbers = array_merge(
                                $numbers,
                                $rangeArray
                            );
                        } else if (preg_match("/^\d+$/", $chunk)) {
                            $numbers[] = $numberHandler->stringToInteger($chunk);
                        } else {
                            $exceptions[] = sprintf(
                                "Invalid value: [%s]",
                                $chunk
                            );
                        }
                    } catch (\Exception $e) {
                        $exceptions[] = $e->getMessage();
                    }
                }
                if ($exceptions) {
                    throw new \InvalidArgumentException(implode(PHP_EOL, $exceptions));
                }
                $this->requestedNumbers = array_unique($numbers);
            } else {
                $this->svnCommand = $args[1];
            }
        }
        if ($this->requestedNumbers) {
            $this->additionalArgs = array_slice($args, 3);
        } else {
            $this->svnCommand = $args[1];
            $this->additionalArgs = array_slice($args, 2);
        }
        if (!$this->svnCommand) {
            throw new \RuntimeException(sprintf(
                "'%s' is not defined! \"Tremble, mortals, and despair! Doom has come to this world!\"",
                '$this->svnCommand'
            ));
        }
    }

    public function exec($cmd){
        echo $cmd . PHP_EOL;
        $output = "";
        exec($cmd, $output);
        return $output;
    }

    public function getAdditionalArgs(){
        return $this->additionalArgs;
    }

    public function getAdditionalArgsStr(){
        return implode(" ", $this->additionalArgs);
    }

    public function getCommand(){
        return $this->svnCommand;
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

    public function hasCommand(){
        return false == empty($this->svnCommand);
    }

    public function hasRequestedNumbers(){
        return sizeof($this->requestedNumbers) > 0;
    }
}
