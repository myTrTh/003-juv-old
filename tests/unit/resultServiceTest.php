<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultsServiceTest extends KernelTestCase
{
	public function setUp()
	{
		self::bootKernel();
		$this->results_tournament = static::$kernel->getContainer()->get('app.results_tournament');
	}

	public function testCalculate()
	{
		// processing test
		$result = $this->results_tournament->processing([1, 2, 3], [1, 2, 3]);
		$this->assertEquals(3, $result);

		$result = $this->results_tournament->processing([1, 2, 2], [1, 2, 3]);
		$this->assertEquals(2, $result);

		$result = $this->results_tournament->processing([1, 1, 2], [1, 2, 3]);
		$this->assertEquals(1, $result);

		$result = $this->results_tournament->processing([2, 2, 2], [1, 2, 3]);
		$this->assertEquals(0, $result);

		// annihilation test
		$class = new ReflectionClass($this->results_tournament);
		$method = $class->getMethod('annihilation');
		$method->setAccessible(true);
		$obj = $this->results_tournament;

		$result = $method->invoke($obj, [1, 0]);
		$this->assertEquals([1, 1, 1, 1], $result);

		$result = $method->invoke($obj, [0, 0]);
		$this->assertEquals([0, 0, 0, 0], $result);

		$result = $method->invoke($obj, [3, 1]);
		$this->assertEquals([1, 2, 3, 4], $result);

		$result = $method->invoke($obj, [1, 2]);
		$this->assertEquals([2, 1, 1, 3], $result);						
	}
}