<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;

abstract class AbstractSvnAction implements AbstractSvnActionInterface {
    protected $svnNumber;

    public function __construct(SvnNumber $svnNumber){
        $this->svnNumber = $svnNumber;
    }
}
