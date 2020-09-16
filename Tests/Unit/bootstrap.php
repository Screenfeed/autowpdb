<?php
/**
 * Bootstraps the AutoWPDB Unit Tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit;

use function Screenfeed\AutoWPDB\Tests\init_test_suite;

require_once dirname( dirname( __FILE__ ) ) . '/bootstrap-functions.php';

init_test_suite( 'Unit' );
