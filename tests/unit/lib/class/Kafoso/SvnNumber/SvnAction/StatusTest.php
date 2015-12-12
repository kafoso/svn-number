<?php
use Kafoso\SvnNumber\SvnAction\Status;

class StatusTest extends \PHPUnit_Framework_TestCase {
    public function testRunsSvnStatusOnConstruction(){
        $status = new Status($this->getSvnNumberMock());
        $expected = array(
            "A    foo/bar.txt",
            "D    foo/baz.txt",
        );
        $this->assertSame($expected, $status->getSvnStatus());
    }

    public function testThatGetLinesReturnsAndArrayOfObjects(){
        $status = new Status($this->getSvnNumberMock());
        $expected = array();
        $this->assertCount(2, $status->getLines());
        $lines = $status->getLines();
        $this->assertInstanceOf('Kafoso\SvnNumber\SvnAction\Status\Line', $lines[1]);
        $this->assertInstanceOf('Kafoso\SvnNumber\SvnAction\Status\Line', $lines[2]);
    }

    protected function getSvnNumberMock(){
        $bashCommandMock = $this->getBashCommandMock();
        $mock = $this->getMockBuilder('Kafoso\SvnNumber')
            ->setMethods(array('getBashCommand'))
            ->setConstructorArgs(array(
                array("svn-number.php", "status"),
                $bashCommandMock
            ))
            ->getMock();
        $mock->expects($this->any())
            ->method('getBashCommand')
            ->will($this->returnValue($bashCommandMock));
        return $mock;
    }

    protected function getBashCommandMock(){
        $returnValue = array(
            "A    foo/bar.txt",
            "D    foo/baz.txt",
        );
        $mock = $this->getMockBuilder('Kafoso\SvnNumber\Bash\Command')
            ->setMethods(array('exec'))
            ->getMock();
        $mock->expects($this->any())
            ->method('exec')
            ->will($this->returnValue($returnValue));
        return $mock;
    }
}
