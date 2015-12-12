<?php
namespace Kafoso\SvnNumber\SvnAction\Status;

class Line {
    protected $number;
    protected $filePath;
    protected $statusType;

    public function __construct($number, $filePath, $statusType){
        $this->number = $number;
        $this->filePath = str_replace("\\", "/", $filePath);
        $this->statusType = $statusType;
    }

    public function getNumber(){
        return $this->number;
    }

    public function getFilePath(){
        return $this->filePath;
    }

    public function getStatusType(){
        return $this->statusType;
    }
}
