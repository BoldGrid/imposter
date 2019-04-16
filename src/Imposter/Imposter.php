<?php
/**
 * Boldgrid Imposter class.
 *
 * @package Boldgrid\Imposter
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Imposter;

/**
 * Boldgrid Imposter class.
 *
 * A quick and dirty version of TypistTech/imposter.
 *
 * @since 1.0.0
 */
class Imposter {
	/**
	 * The custom namespace.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $customNamespace;

	/**
	 * An array of errors.
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $errors = array();

	/**
	 * An array of namespaces needing to be prefixed.
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $namespaces = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setConfigs();
	}

	/**
	 * Set configs.
	 *
	 * Read our configs from the composer.json.
	 *
	 * @since 1.0.0
	 */
	private function setConfigs() {
		// This script is intended to be ran from the directory where your composer.json file is.
		$composerFilepath = getcwd() . '/composer.json';

		if ( ! file_exists( $composerFilepath ) ) {
			$this->errors[] = 'File does not exist: ' . $composerFilepath;
			return;
		}

		$composerConfigs = file_get_contents( $composerFilepath );
		$composerConfigs = json_decode( $composerConfigs );

		$extra = ! empty( $composerConfigs->extra->boldgrid_imposter ) ? $composerConfigs->extra->boldgrid_imposter : null;

		$this->customNamespace = ! empty( $extra->namespace ) ? $extra->namespace : null;
		if ( empty( $this->customNamespace ) ) {
			$this->errors[] = 'Custom namespace not defined.';
			return;
		}

		$this->namespaces = ! empty( $extra->namespaces ) ? $extra->namespaces : null;
		if ( empty( $this->namespaces ) ) {
			$this->errors[] = 'Namespaces not defined.';
			return;
		}
	}

	/**
	 * Imposter.
	 *
	 * @since 1.0.0
	 */
	public function imposter() {
		if ( ! empty( $this->errors ) ) {
			print_r( $this->errors );
			return;
		}

		foreach ( $this->namespaces as $namespace ) {
			$find    = str_replace( '\\', '\\\\', 'namespace ' . $namespace );
			$replace = str_replace( '\\', '\\\\', 'namespace ' . $this->customNamespace . '\\' . $namespace );

 			$cmd = 'find vendor -type f -name "*.php" -print0 | xargs -0 sed -i \'s/' . $find . '/' . $replace . '/g\'';
 			exec( $cmd );
		}
	}

	/**
	 * Unimposter.
	 *
	 * @since 1.0.0
	 */
	public function unimposter() {
		if ( ! empty( $this->errors ) ) {
			print_r( $this->errors );
			return;
		}

		foreach ( $this->namespaces as $namespace ) {
			$find    = str_replace( '\\', '\\\\', 'namespace ' . $this->customNamespace . '\\' . $namespace );
			$replace = str_replace( '\\', '\\\\', 'namespace ' . $namespace );

			$cmd = 'find vendor -type f -name "*.php" -print0 | xargs -0 sed -i \'s/' . $find . '/' . $replace . '/g\'';
			exec( $cmd );
		}
	}
}