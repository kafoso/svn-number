<?php
use Kafoso\SvnNumber\Bash\Styling;

class StylingTest extends \PHPUnit_Framework_TestCase {
    public function testNormalDefault(){
        $styling = new Styling;
        $normal = trim($styling->normal("foo", null, null, true));
        $this->assertSame("\33[38;5;253mfoo\33[0m", $normal);
    }

    public function testNormal(){
        $styling = new Styling;
        $normal = trim($styling->normal("foo", 33, null, true));
        $this->assertSame("\33[38;5;33mfoo\33[0m", $normal);
    }

    public function testBoldDefault(){
        $styling = new Styling;
        $normal = trim($styling->bold("foo", null, null, true));
        $this->assertSame("\33[1m\33[38;5;253mfoo\33[0m", $normal);
    }

    public function testBold(){
        $styling = new Styling;
        $normal = trim($styling->bold("foo", 33, null, true));
        $this->assertSame("\33[1m\33[38;5;33mfoo\33[0m", $normal);
    }
}
