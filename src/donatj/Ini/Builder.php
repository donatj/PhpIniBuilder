<?php

namespace donatj\Ini;

class Builder {

	/**
	 * @var bool
	 */
	public $enableBool = true;
	/**
	 * @var bool
	 */
	public $enableNumeric = true;
	/**
	 * @var null|array
	 */
	private $data;

	/**
	 * @param array $data
	 */
	public function __construct( array $data ) {
		$this->data = $data;
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
					echo "[{$key}]\n";
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

		return $output;

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
	 * @param bool $enableBool
	 */
	public function enableBoolDetection( $enableBool ) {
		$this->enableBool = $enableBool;
	}

	/**
	 * Enable / Disable Automatic Numeric Detection
	 *
	 * @param boolean $enableNumeric
	 */
	public function enableNumericDetection( $enableNumeric ) {
		$this->enableNumeric = $enableNumeric;
	}

}