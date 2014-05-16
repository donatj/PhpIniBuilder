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
	private $enableBool;
	/**
	 * @var bool
	 */
	private $enableNumeric;
	/**
	 * @var null|array
	 */
	private $data;

	/**
	 * @param array $data
	 * @param bool  $enableBool Enable bool detection
	 * @param bool  $enableNumeric Enable numeric detection
	 */
	public function __construct( array $data = null, $enableBool = true, $enableNumeric = true ) {
		$this->data          = $data;
		$this->enableBool    = $enableBool;
		$this->enableNumeric = $enableNumeric;
	}

	/**
	 * @param array|null $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * @return array|null
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * INI String Result
	 *
	 * @return string
	 */
	function generate() {
		return $this->build($this->data);
	}

	private function build( array $data, $depth = 0, $prevKey = false ) {

		$output = "";

		if( $depth > 2 ) {
			throw new ExceededMaxDepthException("Max INI Depth of 2 Exceeded");
		}

		$position = 0;
		foreach( $data as $key => $val ) {
			if( is_array($val) ) {
				if( $depth == 0 ) {
					$output .= "\n[{$key}]\n";
					$output .= $this->build($val, $depth + 1);
				} else {
					$output .= $this->build($val, $depth + 1, $key);
				}
			} else {
				$valStr = $this->valEscape($val);
				if( $prevKey !== false ) {

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

	private function valEscape( $value ) {
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
	 * PHP's built in `parse_ini_*` methods parse all values to string, enabling this option enables numeric detection
	 * so they will be output once again as floats/ints
	 *
	 * @param boolean $enableNumeric
	 */
	public function enableNumericDetection( $enableNumeric ) {
		$this->enableNumeric = $enableNumeric;
	}

}