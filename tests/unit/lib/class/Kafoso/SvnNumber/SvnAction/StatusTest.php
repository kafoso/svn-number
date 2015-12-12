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

    public function testThatOutputIsAsExpectedForAll(){
        $status = new Status($this->getSvnNumberMock());
        $expectedArray = array(
            "\33[1m\33[38;5;231m    1  \33[1m\33[38;5;40mA\33[38;5;253m    \33[0m\33[38;5;40mfoo/bar.txt         \33[0m",
            "\33[1m\33[48;5;234m\33[38;5;231m    2  \33[1m\33[48;5;234m\33[38;5;160mD\33[48;5;234m\33[38;5;253m    \33[0m\33[48;5;234m\33[38;5;160mfoo/baz.txt         \33[0m",
        );
        $expected = trim(implode(PHP_EOL, $expectedArray));
        $this->assertSame(
            str_replace("\33", "@", $expected), // To make it readable in terminal
            str_replace("\33", "@", trim($status->getOutput()))
        );
        $this->assertSame($expected, trim($status->getOutput()));
    }

    public function testThatOutputIsAsExpectedForAFiniteNumberOfFiles(){
        $status = new Status($this->getSvnNumberMock());
        $expectedArray = array(
            "\33[1m\33[38;5;231m    1  \33[1m\33[38;5;40mA\33[38;5;253m    \33[0m\33[38;5;40mfoo/bar.txt         \33[0m",
        );
        $expected = trim(implode(PHP_EOL, $expectedArray));
        $this->assertSame(
            str_replace("\33", "@", $expected), // To make it readable in terminal
            str_replace("\33", "@", trim($status->getOutput(array(1))))
        );
        $this->assertSame($expected, trim($status->getOutput(array(1))));
    }

    public function testThatGetLinesReturnsAndArrayOfObjects(){
        $status = new Status($this->getSvnNumberMock());
        $expected = array();
        $this->assertCount(2, $status->getLines());
        $lines = $status->getLines();
        $this->assertInstanceOf('Kafoso\SvnNumber\SvnAction\Status\Line', $lines[1]);
        $this->assertInstanceOf('Kafoso\SvnNumber\SvnAction\Status\Line', $lines[2]);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage    No line exists for numbers:
     */
    public function testThatGetLineInformationFromFileNumbersThrowsExceptionWhenNoIntersectionExists(){
        $status = new Status($this->getSvnNumberMock());
        $status->getLineInformationFromFileNumbers(array());
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
            ->setMethods(array(
                'exec',
                'getMaxTerminalColumns',
            ))
            ->getMock();
        $mock->expects($this->any())
            ->method('exec')
            ->will($this->returnValue($returnValue));
        $mock->expects($this->any())
            ->method('getMaxTerminalColumns')
            ->will($this->returnValue(32));
        return $mock;
    }
}
