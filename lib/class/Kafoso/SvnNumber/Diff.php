<?php
namespace Kafoso\SvnNumber;

use Kafoso\SvnNumber;

class Diff {
    protected $svnNumber;

    public function __construct(SvnNumber $svnNumber){
        $this->svnNumber = $svnNumber;
    }
    public function getOutputForFile($filePath) {
        // TODO: svn diff $filePath
        $diff = trim("

        ");
    }
}
