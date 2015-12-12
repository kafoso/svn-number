<?php
namespace Kafoso\SvnNumber\Argument;

class NumberNegotiator {
    protected $argument;

    protected $exceptions = array();

    public function __construct($argument){
        $this->argument = $argument;
    }

    public function getExceptionsAsString(){
        $strings = array();
        foreach ($this->exceptions as $exception) {
            $strings[] = sprintf(
                "[%s::%s] %s: %s",
                $exception->getFile(),
                $exception->getLine(),
                get_class($exception),
                $exception->getMessage()
            );
        }
        return implode(PHP_EOL, $strings);
    }

    public function getExceptions(){
        return $this->exceptions;
    }

    public function getNumbers(){
        $this->guardIsMatch();
        $numbers = array();
        $chunks = preg_split("/,/", $this->argument, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chunks as $chunk) {
            try {
                if (preg_match("/^(\d)+-(\d+)$/", $chunk)) {
                    $rangeArray = $this->stringRangeToArray($chunk);
                    if ($rangeArray) {
                        $numbers = array_merge(
                            $numbers,
                            $rangeArray
                        );
                    }
                } else if (preg_match("/^\d+$/", $chunk)) {
                    $number = $this->stringToInteger($chunk);
                    if ($number) {
                        $numbers[] = $number;
                    }
                } else {
                    throw new \RuntimeException(sprintf(
                        "Invalid value: [%s]",
                        $chunk
                    ));
                }
            } catch (\Exception $e) {
                $this->exceptions[] = $e->getMessage();
            }
        }
        $numbers = array_unique($numbers);
        sort($numbers);
        return $numbers;
    }

    public function hasExceptions(){
        return sizeof($this->exceptions) > 0;
    }

    public function isMatch(){
        return (bool)preg_match("/^\d+[,\-\d]*?$/", $this->argument);
    }

    public function guardIsMatch(){
        if (false == $this->isMatch()) {
            throw new \InvalidArgumentException(sprintf(
                "Argument '%s' is not a match",
                $this->argument
            ));
        }
    }

    protected function stringRangeToArray($str){
        list($min, $max) = explode("-", $str);
        $exceptions = array();
        try {
            $this->validateNumberFromString($min, "Invalid left-hand number");
        } catch (\Exception $e) {
            $exceptions[] = $e;
        }
        try {
            $this->validateNumberFromString($max, "Invalid right-hand number");
        } catch (\Exception $e) {
            $exceptions[] = $e;
        }
        if ($exceptions) {
            $this->exceptions = array_merge(
                $this->exceptions,
                $exceptions
            );
            return array();
        }
        $numbers = array();
        $min = intval($min);
        $max = intval($max);
        if ($min > $max) {
            $maxTemp = $max;
            $max = $min;
            $min = $maxTemp;
        }
        for ($i=$min;$i<=$max;$i++) {
            $numbers[] = $i;
        }
        return $numbers;
    }

    protected function stringToInteger($str){
        try {
            $this->validateNumberFromString($str);
        } catch (\Exception $e) {
            $this->exceptions[] = $e;
            return null;
        }
        return intval($str);
    }

    protected function validateNumberFromString($str, $msgPrepend = null){
        if ($str == "0") {
            $msg = sprintf(
                "Must be a positive, non-zero integer. Found: %s",
                $str
            );
            if ($msgPrepend) {
                $msg = $msgPrepend . ": ". $msg;
            }
            throw new \OutOfBoundsException($msg);
        } else {
            $regex = '/^[1-9]\d*$/';
            if (false == preg_match("{$regex}", $str)) {
                $msg = sprintf(
                    "Not an integer. Expected number the form: '%s'. Found: %s",
                    $regex,
                    $str
                );
                if ($msgPrepend) {
                    $msg = $msgPrepend . ": " . $msg;
                }
                throw new \InvalidArgumentException($msg);
            }
        }
    }
}
