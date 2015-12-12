<?php
use Kafoso\SvnNumber\Argument\NumberNegotiator;

class NumberNegotiatorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @dataProvider    dataProvider_ValidNumberStrings
     */
    public function testValidNumberStrings($expected, $string){
        $numberNegotiator = new NumberNegotiator($string);
        $this->assertSame($expected, $numberNegotiator->getNumbers());
        $this->assertFalse($numberNegotiator->hasExceptions());
    }

    public function dataProvider_ValidNumberStrings(){
        return array(
            array(array(1),     "1"),
            array(array(1,2),   "1,2"),
            array(array(1,2),   "2,1"),
            array(array(2,3,4), "2-4"),
            array(array(2,3,4), "4-2"),
            array(array(1,2,4,5), "1-2,4-5"),
            array(array(1,2,4,5), "5-4,2-1"),
        );
    }

    /**
     * @dataProvider    dataProvider_NoMatchNumberStrings
     */
    public function testNoMatchNumberStrings($string){
        $numberNegotiator = new NumberNegotiator($string);
        $this->assertFalse($numberNegotiator->isMatch());
    }

    public function dataProvider_NoMatchNumberStrings(){
        return array(
            array(""),
            array(",1"),
            array("-1"),
        );
    }

    public function testStringIsInvalidWhenStartingWithZero(){
        $numberNegotiator = new NumberNegotiator("01");
        $this->assertTrue($numberNegotiator->isMatch());
        $numberNegotiator->getNumbers();
        $exceptions = $numberNegotiator->getExceptions();
        $this->assertCount(1, $exceptions);
        $this->assertSame("Not an integer. Expected number the form: '/^[1-9]\d*$/'. Found: 01", $exceptions[0]->getMessage());
    }

    public function testStringRangeIsInvalidWhenStartingWithZero(){
        $numberNegotiator = new NumberNegotiator("03-02");
        $this->assertTrue($numberNegotiator->isMatch());
        $numberNegotiator->getNumbers();
        $exceptions = $numberNegotiator->getExceptions();
        $this->assertCount(2, $exceptions);
        $this->assertSame("Invalid left-hand number: Not an integer. Expected number the form: '/^[1-9]\d*$/'. Found: 03", $exceptions[0]->getMessage());
        $this->assertSame("Invalid right-hand number: Not an integer. Expected number the form: '/^[1-9]\d*$/'. Found: 02", $exceptions[1]->getMessage());
    }
}
