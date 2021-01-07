<?php

namespace donatj\Ini\Test;

use donatj\Ini\Builder;
use donatj\Ini\ExceededMaxDepthException;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase {

	public function testMaxDepthException() {
		try {
			$data    = array( 'x' => array( 'y' => array( 'z' => array( 'a' => 1 ) ) ) );
			$builder = new Builder();
			$builder->generate($data);
		}catch(ExceededMaxDepthException $ex) {
			return;
		}

		self::fail('Expected ExceededMaxDepthException');
	}

	public function testEnableBoolDetection() {
		$builder = new Builder();
		self::assertStringEndsWith("true", $builder->generate(array( 'x' => 1 )));

		$builder->enableBoolDetection(false);
		self::assertStringEndsWith("1", $builder->generate(array( 'x' => 1 )));
	}

	public function testEnableNumericDetection() {
		// Integer
		$builder = new Builder();
		self::assertStringEndsWith("7", $builder->generate(array( 'x' => 7 )));

		$builder->enableNumericDetection(false);
		self::assertStringEndsWith("'7'", $builder->generate(array( 'x' => 7 )));

		// Float
		$builder->enableNumericDetection(true);
		self::assertStringEndsWith("3.14159265", $builder->generate(array( 'x' => 3.14159265 )));

		$builder->enableNumericDetection(false);
		self::assertStringEndsWith("'3.14159265'", $builder->generate(array( 'x' => 3.14159265 )));
	}

	public function testNumericIndex() {

		$data    = array( 'x' => array( 'y' => array( 'a' => 'test', '2', '3', '4', 6 => '4', '7', 5 => 'bbq', 'bbq' => 'soda' ) ) );
		$builder = new Builder();

		self::assertSame($data, parse_ini_string($builder->generate($data), true));
	}

	public function testLateRootValues() {
		$builder = new Builder();
		$data    = array(
			'x'    => array(
				'y' => 'testValue'
			),
			'late' => 'value',
		);

		self::assertTrue($this->arrays_are_similar(parse_ini_string($builder->generate($data), true), $data), 'Assert Late Root Keys Will be Processed');
	}

	public function testSkipNullValues() {
		$builder = new Builder();
		$builder->enableSkipNullValues(true);

		$data = array(
			'x'     => array(
				'z' => null,
			),
			'y'     => array( 1, 2, null, 3 ),
			'other' => null,
		);

		//demands empty x,skip index 2, no other
		self::assertEquals(trim('[x]

[y]
0 = true
1 = 2
3 = 3'), trim($builder->generate($data)));

	}

	public function testReservedWordSEscape() {
		$builder = new Builder();

		$data = array(
			'true_string'  => 'true',
			'true_string2' => 'TRUE',
			'true_literal' => true,

			'false_string'  => 'false',
			'false_string2' => 'FALSE',
			'false_literal' => false,

			'null_string'  => 'null',
			'null_string2' => 'NULL',
			'null_literal' => null,
		);

		self::assertEquals(trim(<<<TAG
true_string = 'true'
true_string2 = 'TRUE'
true_literal = true
false_string = 'false'
false_string2 = 'FALSE'
false_literal = false
null_string = 'null'
null_string2 = 'NULL'
null_literal = false
TAG
		), trim($builder->generate($data)));
	}

	private function arrays_are_similar( $aSide, $bSide ) {

		$keys = array_unique(array_merge(
			array_keys($aSide),
			array_keys($bSide)
		));

		foreach( $keys as $key ) {
			if( !array_key_exists($key, $aSide) || !array_key_exists($key, $bSide) ) {
				return false;
			}

			$aSideValue = $aSide[$key];
			$bSideValue = $bSide[$key];

			if( is_array($aSideValue) && is_array($bSideValue) ) {
				if( !$this->arrays_are_similar($aSideValue, $bSideValue) ) {
					return false;
				}
			} elseif( !is_array($aSideValue) && !is_array($bSideValue) ) {
				if( $aSideValue !== $bSideValue ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return true;
	}

}
