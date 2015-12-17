<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;

interface DiffInterface {
    public function __construct(SvnNumber $svnNumber);
}
