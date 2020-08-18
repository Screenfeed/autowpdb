<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::get_last_error().
 *
 * @covers DBUtilities::get_last_error
 * @group  DBUtilities
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		global $wpdb;

		$wpdb = null;

		$result = DBUtilities::get_last_error();

		$this->assertSame( '', $result );

		$wpdb = (object) [];

		$result = DBUtilities::get_last_error();

		$wpdb = (object) [
			'last_error' => null,
		];

		$result = DBUtilities::get_last_error();

		$this->assertSame( '', $result );

		$wpdb->last_error = false;

		$result = DBUtilities::get_last_error();

		$this->assertSame( '', $result );

		$wpdb->last_error = 'An error.';

		$result = DBUtilities::get_last_error();

		$this->assertSame( 'An error.', $result );
	}
}
