<?php
/**
 * Test Case for CRUD’s unit tests.
 *
 * @package Screenfeed\AutoWPDB\Tests\Unit
 */

namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD;

use Screenfeed\AutoWPDB\Tests\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

	public static function setUpBeforeClass() {
		foreach( [ ARRAY_A, OBJECT, OBJECT_K ] as $const ) {
			if ( ! defined( $const ) ) {
				define( $const, $const );
			}
		}
	}
}
