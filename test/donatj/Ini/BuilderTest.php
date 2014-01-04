<?php

namespace donatj\Ini\Test;

use donatj\Ini\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \donatj\Ini\ExceededMaxDepthException
	 */
	public function testMaxDepthException() {
		$data    = array( 'x' => array( 'y' => array( 'z' => array( 'a' => 1 ) ) ) );
		$builder = new Builder($data);
		$builder->generate();
	}

	public function testEnableBoolDetection() {
		$builder = new Builder(array('x' => 1));
		$this->assertStringEndsWith("true", $builder->generate());

		$builder->enableBoolDetection(false);
		$this->assertStringEndsWith("1", $builder->generate());
	}

	public function testEnableNumericDetection() {
		// Integer
		$builder = new Builder(array('x' => 7));
		$this->assertStringEndsWith("7", $builder->generate());

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'7'", $builder->generate());

		// Float
		$builder = new Builder(array('x' => 3.14159265));
		$this->assertStringEndsWith("3.14159265", $builder->generate());

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'3.14159265'", $builder->generate());
	}


}
 