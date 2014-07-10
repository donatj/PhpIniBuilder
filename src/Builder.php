<?php

namespace donatj\Ini;

/**
 * Utility for Converting An Array to a INI string
 *
 * @package donatj\Ini
 */
class Builder {

	/**
	 * @var bool
	 */
	protected $enableBool;
	/**
	 * @var bool
	 */
	protected $enableNumeric;
	/**
	 * @var bool
	 */
	protected $enableAlphaNumeric;

	/**
	 * @param bool $enableBool
	 * @param bool $enableNumeric
	 * @param bool $enableAlphaNumeric
	 */
	public function __construct( $enableBool = true, $enableNumeric = true, $enableAlphaNumeric = true ) {
		$this->enableBool         = $enableBool;
		$this->enableNumeric      = $enableNumeric;
		$this->enableAlphaNumeric = $enableAlphaNumeric;
	}

	/**
	 * INI String Result
	 *
	 * @param array $data
	 * @return string
	 * @throws ExceededMaxDepthException
	 */
	public function generate( array $data ) {
		return $this->build($data);
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function __invoke( array $data ) {
		return $this->generate($data);
	}

	/**
	 * Recursive build function
	 *
	 * @param array $data
	 * @param int   $depth
	 * @param bool  $prevKey
	 * @return string
	 * @throws ExceededMaxDepthException
	 */
	protected function build( array $data, $depth = 0, $prevKey = false ) {

		$output = "";

		if( $depth > 2 ) {
			throw new ExceededMaxDepthException("Max INI Depth of 2 Exceeded");
		}

		$position = 0;
		foreach( $data as $key => $val ) {
			if( is_array($val) ) {
				if( $depth == 0 ) {
					$output .= "\n[{$key}]\n";
				}
				$output .= $this->build($val, $depth + 1, $key);
			} else {
				$valStr = $this->valEscape($val);
				if( $depth > 1 ) {

					if( $key !== $position ) {
						if( ctype_digit((string)$key) ) {
							$position = $key;
						}

						$output .= "{$prevKey}[{$key}] = {$valStr}\n";
					} else {
						$output .= "{$prevKey}[] = {$valStr}\n";
					}

					$position++;
				} else {
					$output .= "{$key} = {$valStr}\n";
				}
			}
		}

		return $depth ? ltrim($output) : trim($output);

	}

	/**
	 * Escapes Values
	 *
	 * @param mixed $value
	 * @return string
	 */
	protected function valEscape( $value ) {
		$value = (string)$value;

		if( $this->enableBool ) {
			if( $value == '' ) {
				return 'false';
			} elseif( $value == '1' ) {
				return 'true';
			}
		}

		if( $this->enableNumeric && is_numeric($value) ) {
			return (string)$value;
		}

		if( $this->enableAlphaNumeric && is_string($value) && ctype_alnum($value) && !is_numeric($value) ) {
			return (string)$value;
		}

		return var_export($value, true);
	}

	/**
	 * Enable / Disable Automatic Boolean Detection
	 *
	 * PHP's built in `parse_ini_*` methods parse `1`, `'1'` and `true` and likewise `''`, and `false` to the same values
	 * when the scanner mode is set to `INI_SCANNER_NORMAL`, enabling this option causes these values to be output
	 * as `true` / `false`
	 *
	 * @param bool $enableBool
	 */
	public function enableBoolDetection( $enableBool ) {
		$this->enableBool = $enableBool;
	}

	/**
	 * Enable / Disable Automatic Numeric Detection
	 *
	 * PHP's built in `parse_ini_*` methods parse all values to string. Enabling this option enables numeric detection
	 * so they will be output once again as floats/ints
	 *
	 * @param boolean $enableNumeric
	 */
	public function enableNumericDetection( $enableNumeric ) {
		$this->enableNumeric = $enableNumeric;
	}

	/**
	 * Enable / Disable Automatic AlphaNumeric Detection
	 *
	 * PHP's built in `parse_ini_*` methods does not require quotation marks around simple strings without spaces. Enabling
	 * this option removes the quotation marks on said simple strings.
	 *
	 * @param boolean $enableAlphaNumeric
	 */
	public function enableAlphaNumericDetection( $enableAlphaNumeric ) {
		$this->enableAlphaNumeric = $enableAlphaNumeric;
	}

}