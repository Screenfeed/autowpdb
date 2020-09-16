<?php
/**
 * Test Case for Basic’s unit tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\Basic;

use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected $table_name = 'wp_foobar';
}
