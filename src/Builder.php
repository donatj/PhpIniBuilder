<?php

namespace donatj\Ini;

/**
 * Builds an INI string from an array.
 */
class Builder {

	/** List of INI Reserved Words */
	private const RESERVED = [ 'true', 'false', 'null' ];

	private bool $enableBool;
	private bool $enableNumeric;
	private bool $enableAlphanumeric;
	private bool $skipNullValues;

	/**
	 * @param bool $enableBool         Enable automatic boolean detection?
	 * @param bool $enableNumeric      Enable automatic numeric detection?
	 * @param bool $enableAlphanumeric Enable automatic alphanumeric detection?
	 * @param bool $skipNullValues     Skip null values?
	 */
	public function __construct(
		bool $enableBool = true,
		bool $enableNumeric = true,
		bool $enableAlphanumeric = true,
		bool $skipNullValues = false
	) {
		$this->enableBool         = $enableBool;
		$this->enableNumeric      = $enableNumeric;
		$this->enableAlphanumeric = $enableAlphanumeric;
		$this->skipNullValues     = $skipNullValues;
	}

	/**
	 * INI String Result
	 *
	 * @param array<int|string, mixed> $data
	 * @throws ExceededMaxDepthException When an array is nested more than two levels below the root.
	 * @throws TypeError When a leaf value is not a scalar, null, or stringable object.
	 */
	public function generate( array $data ) : string {
		return $this->build($data);
	}

	/**
	 * Same as `generate` - exists to make Builder callable.
	 *
	 * @param array<int|string, mixed> $data
	 * @see self::generate
	 */
	public function __invoke( array $data ) : string {
		return $this->generate($data);
	}

	/**
	 * Recursive build function
	 *
	 * @param array<int|string, mixed> $data
	 * @throws ExceededMaxDepthException When an array is nested more than two levels below the root.
	 * @throws TypeError When a leaf value is not a scalar, null, or stringable object.
	 */
	private function build( array $data, int $depth = 0, int|string|null $prevKey = null ) : string {
		$valueOutput = '';
		$arrayOutput = '';

		if( $depth > 2 ) {
			throw new ExceededMaxDepthException('Max INI Depth of 2 Exceeded');
		}

		$listMax = -1;
		foreach( $data as $key => $val ) {
			$prevMax = $listMax;
			if( ctype_digit((string)$key) ) {
				$listMax = max($listMax, (int)$key);
			}

			if( $this->skipNullValues && $val === null ) {
				continue;
			}

			if( is_array($val) ) {
				if( $depth === 0 ) {
					$arrayOutput .= "\n[{$key}]\n";
				}

				$arrayOutput .= $this->build($val, $depth + 1, $key);

				continue;
			}

			if( !is_scalar($val) && $val !== null && !$val instanceof \Stringable ) {
				throw new TypeError('INI values must be scalar, null, or stringable.');
			}

			$valStr = $this->escape($val);
			if( $depth <= 1 ) {
				$valueOutput .= "{$key} = {$valStr}\n";

				continue;
			}

			if( $key === $prevMax + 1 ) {
				$valueOutput .= "{$prevKey}[] = {$valStr}\n";

				continue;
			}

			$valueOutput .= "{$prevKey}[{$key}] = {$valStr}\n";
		}

		$output = "{$valueOutput}\n{$arrayOutput}";

		return $depth ? ltrim($output) : trim($output);
	}

	/**
	 * Escapes values according to the current rules.
	 */
	public function escape( bool|float|int|string|\Stringable|null $value ) : string {
		$value = (string)$value;

		if( $this->enableBool ) {
			if( $value === '' ) {
				return 'false';
			}

			if( $value === '1' ) {
				return 'true';
			}
		}

		if( $this->enableNumeric && is_numeric($value) ) {
			return (string)$value;
		}

		if( $this->enableAlphanumeric
			&& ctype_alnum($value)
			&& !is_numeric($value)
			&& !in_array(strtolower($value), self::RESERVED)
		) {
			return (string)$value;
		}

		return var_export($value, true);
	}

	/**
	 * Configure whether empty strings and `1` are output as INI boolean values. The original builder is not changed.
	 */
	public function withBoolDetection( bool $enableBool ) : static {
		$builder = clone $this;
		$builder->enableBool = $enableBool;

		return $builder;
	}

	/**
	 * Configure whether numeric values are output without quotation marks. The original builder is not changed.
	 */
	public function withNumericDetection( bool $enableNumeric ) : static {
		$builder = clone $this;
		$builder->enableNumeric = $enableNumeric;

		return $builder;
	}

	/**
	 * Configure whether simple unreserved strings are output without quotation marks. The original builder is not changed.
	 */
	public function withAlphanumericDetection( bool $enableAlphanumeric ) : static {
		$builder = clone $this;
		$builder->enableAlphanumeric = $enableAlphanumeric;

		return $builder;
	}

	/**
	 * Configure whether null values are skipped. The original builder is not changed.
	 */
	public function withSkipNullValues( bool $skipNullValues ) : static {
		$builder = clone $this;
		$builder->skipNullValues = $skipNullValues;

		return $builder;
	}

}
