<?php
namespace Kafoso\SvnNumber\SvnAction\Status;

class Staging {
    protected $stagingFilePath;
    protected $staged = array();

    public function __construct($stagingFilePath){
        $this->stagingFilePath = $stagingFilePath;
        if (file_exists($this->stagingFilePath)) {
            $fileContents = trim(file_get_contents($this->stagingFilePath));
            if ($fileContents) {
                $this->staged = unserialize($fileContents);
            }
        }
    }

    public function addLine(Line $line){
        $this->staged[$line->getNumber()] = $line;
        return $this;
    }

    public function clear(){
        $this->staged = array();
        return $this;
    }

    public function removeLine(Line $line){
        unset($this->staged[$line->getNumber()]);
        return $this;
    }

    public function save(){
        file_put_contents($this->stagingFilePath, serialize($this->staged));
        return $this;
    }

    public function getStaged(){
        return $this->staged;
    }

    public function getStagedFilePaths(){
        $stagedLineNumbers = array_map(function(Line $line){
            return $line->getFilePath();
        }, $this->staged);
        return array_values($stagedLineNumbers);
    }

    public function hasStaged(){
        return sizeof($this->staged) > 0;
    }
}
