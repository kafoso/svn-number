<?php
namespace Kafoso\SvnNumber;

class NumberHandler {
    public function stringRangeToArray($str){
        list($min, $max) = explode("-", $str);
        $exceptions = array();
        try {
            $this->validateNumberFromString($min, $str);
        } catch (\Exception $e) {
            $exceptions[] = sprintf(
                "[%s] Invalid 'min' number: %s",
                $str,
                $e->getMessage()
            );
        }
        try {
            $this->validateNumberFromString($max, $str);
        } catch (\Exception $e) {
            $exceptions[] = sprintf(
                "[%s] Invalid 'max' number: %s",
                $str,
                $e->getMessage()
            );
        }
        if ($exceptions) {
            $message = implode(PHP_EOL, array_map(function($v){
                return str_repeat(" ", 4) . $v;
            }, $exceptions));
            throw new \RuntimeException($message);
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

    public function stringToInteger($str){
        $this->validateNumberFromString($str, $str);
        return intval($str);
    }

    protected function validateNumberFromString($str){
        if ($str == "0") {
            throw new \OutOfBoundsException(sprintf(
                "Must be a positive, non-zero integer. Found: %s",
                $str
            ));
        } else {
            $regex = '/^[1-9]\d*$/';
            if (false == preg_match("{$regex}", $str)) {
                throw new \InvalidArgumentException(sprintf(
                    "Not an integer. Expected number the form: '%s'. Found: %s",
                    $regex,
                    $str
                ));
            }
        }
    }
}
