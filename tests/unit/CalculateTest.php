<?php

class CalculateTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        // self::bootKernel();
        // $this->calculate = static::$kernel->getContainer()->get('app.results_tournament');
    }

    protected function _after()
    {
    }

    // tests
    public function testRightCalculate()
    {
        // $calculate = $this->getContainer()->get('app.results_tournament');
        // $result = $calculate->processing([1, 1, 3, 5], [1, 2, 3, 4]);

        // $this->assertEquals(2, $result);
    }
}