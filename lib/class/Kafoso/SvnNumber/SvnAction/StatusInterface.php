<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;
use Kafoso\SvnNumber\SvnAction\Status\Staging;

interface StatusInterface {
    public function __construct(SvnNumber $svnNumber, Staging $staging);
}
