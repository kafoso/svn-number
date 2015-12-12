<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;

interface AbstractSvnActionInterface {
    public function __construct(SvnNumber $svnNumber);
}
