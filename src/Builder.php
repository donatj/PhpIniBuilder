<?php

namespace donatj\Ini;

/**
 * Utility for Converting An Array to a INI string
 */
class Builder {

	/** List of INI Reserved Words */
	private const RESERVED = [ 'true', 'false', 'null' ];

	/** @var bool */
	protected $enableBool;
	/** @var bool */
	protected $enableNumeric;
	/** @var bool */
	protected $enableAlphaNumeric;
	/** @var bool */
	protected $skipNullValues;

	/**
	 * @param bool $enableBool         Enable automatic boolean detection?
	 * @param bool $enableNumeric      Enable automatic numeric detection?
	 * @param bool $enableAlphaNumeric Enable automatic alpha-numeric detection?
	 * @param bool $skipNullValues     Skip null values?
	 */
	public function __construct(
		bool $enableBool = true,
		bool $enableNumeric = true,
		bool $enableAlphaNumeric = true,
		bool $skipNullValues = false
	) {
		$this->enableBool         = $enableBool;
		$this->enableNumeric      = $enableNumeric;
		$this->enableAlphaNumeric = $enableAlphaNumeric;
		$this->skipNullValues     = $skipNullValues;
	}

	/**
	 * INI String Result
	 *
	 * @throws ExceededMaxDepthException
	 */
	public function generate( array $data ) : string {
		return $this->build($data);
	}

	/**
	 * Same as `generate` - exists to make Builder callable.
	 *
	 * @see self::generate
	 */
	public function __invoke( array $data ) : string {
		return $this->generate($data);
	}

	/**
	 * Recursive build function
	 *
	 * @param int|string $prevKey
	 * @throws ExceededMaxDepthException
	 */
	protected function build( array $data, int $depth = 0, $prevKey = null ) : string {
		$valueOutput = '';
		$arrayOutput = '';

		if( $depth > 2 ) {
			throw new ExceededMaxDepthException('Max INI Depth of 2 Exceeded');
		}

		$position = 0;
		foreach( $data as $key => $val ) {
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

			$valStr = $this->escape($val);
			if( $depth <= 1 ) {
				$valueOutput .= "{$key} = {$valStr}\n";
				continue;
			}

			if( $key === $position ) {
				$valueOutput .= "{$prevKey}[] = {$valStr}\n";
				continue;
			}

			if( ctype_digit((string)$key) ) {
				$position = $key;
			}

			$valueOutput .= "{$prevKey}[{$key}] = {$valStr}\n";

			$position++;
		}

		$output = "{$valueOutput}\n{$arrayOutput}";

		return $depth ? ltrim($output) : trim($output);
	}

	/**
	 * Escapes Values According to Currently Set Rules
	 *
	 * @param bool|float|int|string|null $value
	 */
	public function escape( $value ) : string {
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

		if( $this->enableAlphaNumeric
			&& ctype_alnum($value)
			&& !is_numeric($value)
			&& !in_array(strtolower($value), self::RESERVED)
		) {
			return (string)$value;
		}

		return var_export($value, true);
	}

	/**
	 * Enable / Disable Automatic Boolean Detection
	 *
	 * PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same
	 * values when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be
	 * output as `true` / `false`
	 */
	public function enableBoolDetection( bool $enableBool ) : void {
		$this->enableBool = $enableBool;
	}

	/**
	 * Enable / Disable Automatic Numeric Detection
	 *
	 * PHP's built in `parse_ini_*` methods parse all values to string. Enabling this option enables numeric detection
	 * so they will be output once again as floats/ints
	 */
	public function enableNumericDetection( bool $enableNumeric ) : void {
		$this->enableNumeric = $enableNumeric;
	}

	/**
	 * Enable / Disable Automatic AlphaNumeric Detection
	 *
	 * PHP's built in `parse_ini_*` methods does not require quotation marks around simple strings without spaces.
	 * Enabling this option removes the quotation marks on said simple strings.
	 */
	public function enableAlphaNumericDetection( bool $enableAlphaNumeric ) : void {
		$this->enableAlphaNumeric = $enableAlphaNumeric;
	}

	/**
	 * Enable / Disable Skipping Null Values
	 *
	 * When enabled, null values will be skipped.
	 */
	public function enableSkipNullValues( bool $skipNullValues ) : void {
		$this->skipNullValues = $skipNullValues;
	}

}
