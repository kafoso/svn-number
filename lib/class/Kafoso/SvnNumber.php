<?php
namespace Kafoso;

use Kafoso\SvnNumber\Diff;
use Kafoso\SvnNumber\Status;

class SvnNumber {
    protected $requestedNumber;
    protected $svnCommand;
    protected $additionalArgs = array();
    protected $status;
    protected $numberedLinesArray;

    public function __construct($args){
        foreach (array_slice($args, 1, 2) as $arg) {
            if (preg_match("/^\d+$/", $arg)) {
                $this->requestedNumber = intval($arg);
            } else {
                $this->svnCommand = $arg;
            }
        }
        $this->additionalArgs = array_slice($args, 3);
    }

    public function exec($cmd){
        exit($cmd);
    }

    public function getAdditionalArgs(){
        return $this->additionalArgs;
    }

    public function getCommand(){
        return $this->svnCommand;
    }

    public function getDiff(){
        return new Diff($this);
    }

    public function getRequestedNumber(){
        return $this->requestedNumber;
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

    public function hasRequestedNumber(){
        return is_int($this->requestedNumber);
    }
}
