<?php

namespace Screenfeed\AutoWPDB\Tests;

/**
 * Initialize the test suite.
 *
 * @param string $test_suite Directory name of the test suite. Default is 'Unit'.
 */
function init_test_suite( $test_suite = 'Unit' ) {
	check_readiness();

	init_constants( $test_suite );

	// Load the Composer autoloader.
	require_once AUTOWPDB_ROOT . '/vendor/autoload.php';
	require_once __DIR__ . '/TestCaseTrait.php';

	// Load Patchwork before everything else in order to allow us to redefine WordPress, 3rd party, and plugin's functions.
	require_once AUTOWPDB_ROOT . '/vendor/antecedent/patchwork/Patchwork.php';
}

/**
 * Check the system's readiness to run the tests.
 */
function check_readiness() {
	if ( version_compare( phpversion(), '7.3.0', '<' ) ) {
		trigger_error( 'AutoWPDB Unit Tests require PHP 7.3 or higher.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}

	if ( ! file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
		trigger_error( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}
}

/**
 * Initialize the constants.
 *
 * @param string $test_suite_folder Directory name of the test suite, like 'Unit' or 'Integration'.
 */
function init_constants( $test_suite_folder ) {
	define( 'AUTOWPDB_ROOT', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
	define( 'AUTOWPDB_TESTS_ROOT', __DIR__ . DIRECTORY_SEPARATOR . $test_suite_folder . DIRECTORY_SEPARATOR );
	define( 'AUTOWPDB_FIXTURES_ROOT', __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR );

	if ( 'Unit' === $test_suite_folder && ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'WordPress' . DIRECTORY_SEPARATOR );
	}

	if ( 'Integration' === $test_suite_folder && ! defined( 'SCREENFEED_IS_TESTING' ) ) {
		define( 'SCREENFEED_IS_TESTING', true );
	}
}
