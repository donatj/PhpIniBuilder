<?php

namespace donatj\Ini\Test;

use donatj\Ini\Builder;
use donatj\Ini\ExceededMaxDepthException;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase {

	public function testMaxDepthException() : void {
		$this->expectException(ExceededMaxDepthException::class);

		$data    = [ 'x' => [ 'y' => [ 'z' => [ 'a' => 1 ] ] ] ];
		$builder = new Builder;
		$builder->generate($data);
	}

	public function testEnableBoolDetection() : void {
		$builder = new Builder;
		$this->assertStringEndsWith("true", $builder->generate([ 'x' => 1 ]));

		$builder->enableBoolDetection(false);
		$this->assertStringEndsWith("1", $builder->generate([ 'x' => 1 ]));
	}

	public function testEnableNumericDetection() : void {
		// Integer
		$builder = new Builder;
		$this->assertStringEndsWith("7", $builder->generate([ 'x' => 7 ]));

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'7'", $builder->generate([ 'x' => 7 ]));

		// Float
		$builder->enableNumericDetection(true);
		$this->assertStringEndsWith("3.14159265", $builder->generate([ 'x' => 3.14159265 ]));

		$builder->enableNumericDetection(false);
		$this->assertStringEndsWith("'3.14159265'", $builder->generate([ 'x' => 3.14159265 ]));
	}

	public function testNumericIndex() : void {

		$data    = [ 'x' => [ 'y' => [ 'a' => 'test', '2', '3', '4', 6 => '4', '7', 5 => 'bbq', 'bbq' => 'soda' ] ] ];
		$builder = new Builder;

		$this->assertSame($data, parse_ini_string($builder->generate($data), true));
	}

	public function testLateRootValues() : void {
		$builder = new Builder;
		$data    = [
			'x'    => [
				'y' => 'testValue',
			],
			'late' => 'value',
		];

		$this->assertTrue($this->arrays_are_similar(parse_ini_string($builder->generate($data), true), $data), 'Assert Late Root Keys Will be Processed');
	}

	public function testSkipNullValues() : void {
		$builder = new Builder;
		$builder->enableSkipNullValues(true);

		$data = [
			'x'     => [
				'z' => null,
			],
			'y'     => [ 1, 2, null, 3 ],
			'other' => null,
		];

		//demands empty x,skip index 2, no other
		$this->assertEquals(trim('[x]

[y]
0 = true
1 = 2
3 = 3'), trim($builder->generate($data)));
	}

	public function testListArray() : void {
		$builder = new Builder;

		$data = [ 'foo' => [ 'bar' => [ 'a', 2 => 'b', 1 => 'c', 'd' ] ] ];
		$out  = $builder->generate($data);
		$this->assertSame($data, parse_ini_string($out, true));

		$data = [ 'bar' => [ 'a', 2 => 'b', 1 => 'c', 'd' ] ];
		$out  = $builder->generate($data);
		$this->assertSame($data, parse_ini_string($out, true));

		$data = [ 'foo' => [ 'bar' => [ 'a', 2 => 'b', 1 => 'c', 'd' ] ] ];
		$out  = $builder->generate($data);
		$this->assertSame($data, parse_ini_string($out, true));
	}

	public function testReservedWordSEscape() : void {
		$builder = new Builder;

		$data = [
			'true_string'  => 'true',
			'true_string2' => 'TRUE',
			'true_literal' => true,

			'false_string'  => 'false',
			'false_string2' => 'FALSE',
			'false_literal' => false,

			'null_string'  => 'null',
			'null_string2' => 'NULL',
			'null_literal' => null,
		];

		$this->assertEquals(trim(<<<'TAG'
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

	private function arrays_are_similar( array $aSide, array $bSide ) : bool {

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

	/**
	 * @dataProvider encodedDataProvider
	 */
	public function testEncoding( array $input, string $expected ) : void {
		$builder = new Builder;
		$this->assertEquals($expected, $builder->generate($input));
	}

	public static function encodedDataProvider() : array {
		return [
			[
				[ 'foo' => 'bar' ],
				'foo = bar',
			],
			[
				[ 'foo' => [ 'bar' => 'baz' ] ],
				<<<'INI'
[foo]
bar = baz
INI
				,
			],
			[
				[ 'foo' => [ 'bar' => [ 'baz' => 'qux' ] ] ],
				<<<'INI'
[foo]
bar[baz] = qux
INI
				,
			],
			[
				[ 'foo' => [ 'bar' => [ 'a', 2 => 'b', 1 => 'c', 'd' ] ] ],
				<<<'INI'
[foo]
bar[] = a
bar[2] = b
bar[1] = c
bar[] = d
INI
				,
			],
		];
	}

	/**
	 * @dataProvider symmetryProvider
	 */
	public function testSymmetry( array $input ) : void {
		$builder = new Builder;
		$builder->enableNumericDetection(true);

		$this->assertSame($input, parse_ini_string($builder->generate($input), true, INI_SCANNER_TYPED));
	}

	public static function symmetryProvider() : array {
		$out = [
			[ [ 'foo' => 7 ] ],
		];

		$base = self::encodedDataProvider();
		foreach( $base as $data ) {
			$out[] = [ $data[0] ];
		}

		return $out;
	}

}
