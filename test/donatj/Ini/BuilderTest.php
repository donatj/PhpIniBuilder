<?php

namespace donatj\Ini\Test;

use donatj\Ini\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \donatj\Ini\ExceededMaxDepthException
	 */
	public function testMaxDepthException() {
		$data    = array( 'x' => array( 'y' => array( 'z' => array( 'a' => 1 ) ) ) );
		$builder = new Builder();
		$builder->generate($data);
	}

	public function testEnableBoolDetection() {
		$builder = new Builder();
		$this->assertStringEndsWith("true", $builder->generate(array('x' => 1)));

		$builder->enableBoolDetection(false);
		$this->assertStringEndsWith("1", $builder->generate(array('x' => 1)));
	}

	public function testEnableNumericDetection() {
		// Integer
		$builder = new Builder();
		$this->assertStringEndsWith("7", $builder->generate(array('x' => 7)));

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'7'", $builder->generate(array('x' => 7)));

		// Float
		$builder->enableNumericDetection(true);
		$this->assertStringEndsWith("3.14159265", $builder->generate(array('x' => 3.14159265)));

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'3.14159265'", $builder->generate(array('x' => 3.14159265)));
	}

	public function testNumericIndex() {

		$data = array('x' => array('y' => array('a' => 'test', '2','3','4',6 => '4', '7', 5 => 'bbq', 'bbq' => 'soda')));
		$builder = new Builder();

		$this->assertSame($data, parse_ini_string($builder->generate($data), true));
	}

	public function testLateRootValues() {
		$builder = new Builder();
		$data = array(
			'x' => array(
				'y' => 'testValue'
			),
			'late' => 'value',
		);

		$this->assertSame($data, parse_ini_string($builder->generate($data), true));
	}


}
 