<?php
use Kafoso\SvnNumber\SvnAction\Status\Line;

class LineTest extends \PHPUnit_Framework_TestCase {
    public function testInstantionAndGetters(){
        $line = new Line(1, "foo/bar.txt", "A");
        $this->assertSame(1, $line->getNumber());
        $this->assertSame("foo/bar.txt", $line->getFilePath());
        $this->assertSame("A", $line->getStatusType());
    }
}
