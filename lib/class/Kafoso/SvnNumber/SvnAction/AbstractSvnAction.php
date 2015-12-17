<?php
namespace Kafoso\SvnNumber\SvnAction;

use Kafoso\SvnNumber;

abstract class AbstractSvnAction {
    const COLOR_CODE_BLUE = 33;
    const COLOR_CODE_GRAY = 242;
    const COLOR_CODE_GRAY_DARK = 234;
    const COLOR_CODE_GRAY_LIGHT = 246;
    const COLOR_CODE_GREEN = 40;
    const COLOR_CODE_ORANGE = 208;
    const COLOR_CODE_RED = 160;
    const COLOR_CODE_TEAL = 45;
    const COLOR_CODE_WHITE = 231;
    const COLOR_CODE_YELLOW = 226;

    protected $svnNumber;

    public function __construct(SvnNumber $svnNumber){
        $this->svnNumber = $svnNumber;
    }
}
