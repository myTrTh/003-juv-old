<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultServiceTest extends KernelTestCase
{
	public function setUp()
	{
		self::bootKernel();
		$this->results_tournament = static::$kernel->getContainer()->get('app.results_tournament');
	}

	public function testCalculate()
	{
		$result = $this->results_tournament->processing([1, 2, 3], [1, 2, 3]);
		$this->assertEquals(3, $result);

		$result = $this->results_tournament->processing([1, 2, 2], [1, 2, 3]);
		$this->assertEquals(2, $result);

		$result = $this->results_tournament->processing([1, 1, 2], [1, 2, 3]);
		$this->assertEquals(1, $result);

		$result = $this->results_tournament->processing([2, 2, 2], [1, 2, 3]);
		$this->assertEquals(0, $result);		
	}
}