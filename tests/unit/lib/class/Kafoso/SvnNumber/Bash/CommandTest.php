<?php
use Kafoso\SvnNumber\Bash\Command;

class CommandTest extends \PHPUnit_Framework_TestCase {
    public function testReturnsOutputOnExec(){
        $command = new Command;
        $result = $command->exec("ls");
        $this->assertInternalType("array", $result);
    }

    /**
     * @expectedException   RuntimeException
     * @expectedExceptionMessage    Shell command error: 'e973ff3ef060fdf06469822419504bfa' 
     */
    public function testExceptionIsThrowOnInvalidInput(){
        $command = new Command;
        $command->exec("e973ff3ef060fdf06469822419504bfa");
    }
}
