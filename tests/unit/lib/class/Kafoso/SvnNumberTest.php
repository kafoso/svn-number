<?php
use Kafoso\SvnNumber;

class SvnNumberTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider    dataProvider_Aguments
     */
    public function testArguments($expectedAction, $expectedNumber, $args){
        array_unshift($args, "svn-number.php"); // First argument is the script itself
        $svnNumber = new SvnNumber($args, $this->getBashCommandMock());
        $this->assertSame($expectedAction, $svnNumber->getAction());
        $this->assertSame($expectedNumber, $svnNumber->getRequestedNumbers());
    }

    public function dataProvider_Aguments(){
        return array(
            array("status", array(),  array("status")),
            array("status", array(1), array("status", "1")),
            array(null,     array(1), array(null, "1")),
            array(null,     array(),  array(null)),
        );
    }

    public function testThatExecPrintsTheCommandItRuns(){
        $svnNumber = new SvnNumber(array("svn-number.php", "status"), $this->getBashCommandMock());
        ob_start();
        $svnNumber->exec("svn status");
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertSame("svn status" . PHP_EOL, $output);
    }

    /**
     * @dataProvider    dataProvider_AdditionalArgs
     */
    public function testThatAdditionalArgumentsAreCorrectlyApplied($expected, $args){
        array_unshift($args, "svn-number.php");
        $svnNumber = new SvnNumber($args, $this->getBashCommandMock());
        $this->assertSame($expected, $svnNumber->getAdditionalArgs());
    }

    public function dataProvider_AdditionalArgs(){
        return array(
            array(array("-x", "-w"), array("diff", "-x", "-w")),
        );
    }

    public function testThatAdditionalArgumentsCanBeExtractedAsSpaceSeparatedString(){
        $svnNumber = new SvnNumber(array("svn-number.php", "status", "-x", "--ignore-whitespace"), $this->getBashCommandMock());
        $this->assertSame("-x --ignore-whitespace", $svnNumber->getAdditionalArgsStr());
    }

    public function getBashCommandMock(){
        return $this->getMock('Kafoso\SvnNumber\Bash\Command', array("exec"));
    }
}
