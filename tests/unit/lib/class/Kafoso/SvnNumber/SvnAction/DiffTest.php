<?php
use Kafoso\SvnNumber\SvnAction\Diff;

class DiffTest extends \PHPUnit_Framework_TestCase {
    public function testThatOutputIsRetrievableForASingleFile(){
        $returnValue = array(
            "Index: my/little/file.txt",
            "===================================================================",
            "--- my/little/file.txt	(revision 3)",
            "+++ my/little/file.txt	(working copy)",
            "@@ -1,4 +1,5 @@",
            "-bar",
            "+foo",
            " something unchanged"
        );
        $bashCommandMock = $this->getMockBuilder('Kafoso\SvnNumber\Bash\Command')
            ->setMethods(array(
                'exec',
                'getMaxTerminalColumns',
            ))
            ->getMock();
        $bashCommandMock->expects($this->any())
            ->method('exec')
            ->will($this->returnValue($returnValue));
        $bashCommandMock->expects($this->any())
            ->method('getMaxTerminalColumns')
            ->will($this->returnValue(32));
        $svnNumberMock = $this->getMockBuilder('Kafoso\SvnNumber')
            ->setMethods(array('getBashCommand'))
            ->setConstructorArgs(array(
                array("svn-number.php", "diff", "1"),
                $bashCommandMock
            ))
            ->getMock();
        $svnNumberMock->expects($this->any())
            ->method('getBashCommand')
            ->will($this->returnValue($bashCommandMock));

        $diff = new Diff($svnNumberMock);
        $expectedArray = array(
            "\33[38;5;226mIndex: my/little/file.txt\33[0m",
            "\33[38;5;242m===================================================================\33[0m",
            "\33[38;5;160m--- my/little/file.txt	(revision 3)\33[0m",
            "\33[38;5;40m+++ my/little/file.txt	(working copy)\33[0m",
            "\33[38;5;45m@@ -1,4 +1,5 @@\33[0m",
            "\33[38;5;160m-bar\33[0m",
            "\33[38;5;40m+foo\33[0m",
            " something unchanged"
        );
        $expected = implode(PHP_EOL, $expectedArray);
        ob_start();
        $found = trim($diff->getOutputForFilePaths(array("my/little/file.txt")));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertSame("svn diff \"my/little/file.txt\"", trim($output));
        $this->assertSame($expected, $found);
    }
}
