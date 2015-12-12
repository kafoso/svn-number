<?php
use Kafoso\SvnNumber\Bash\Styling;

class StylingTest extends \PHPUnit_Framework_TestCase {
    public function testNormalDefault(){
        $styling = new Styling($this->getBashCommandMock());
        $normal = trim($styling->normal("foo"));
        $this->assertSame("\33[38;5;231mfoo\33[0m", $normal);
    }

    public function testNormal(){
        $styling = new Styling($this->getBashCommandMock());
        $normal = trim($styling->normal("foo", 33));
        $this->assertSame("\33[38;5;33mfoo\33[0m", $normal);
    }

    public function testBoldDefault(){
        $styling = new Styling($this->getBashCommandMock());
        $normal = trim($styling->bold("foo"));
        $this->assertSame("\33[1m\33[38;5;231mfoo\33[0m", $normal);
    }

    public function testBold(){
        $styling = new Styling($this->getBashCommandMock());
        $normal = trim($styling->bold("foo", 33));
        $this->assertSame("\33[1m\33[38;5;33mfoo\33[0m", $normal);
    }

    public function testGetMaxTerminalColumns(){
        $styling = new Styling($this->getBashCommandMock());
        $this->assertSame(0, $styling->getMaxTerminalColumns());
    }

    public function getBashCommandMock(){
        return $this->getMock('Kafoso\SvnNumber\Bash\Command', array("exec"));
    }
}
