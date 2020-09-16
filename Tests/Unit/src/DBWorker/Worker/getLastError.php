<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->get_last_error().
 *
 * @covers Worker::get_last_error
 * @group  Worker
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		global $wpdb;

		$wpdb   = null;
		$worker = new Worker();

		$result = $worker->get_last_error();

		$this->assertSame( '', $result );

		$wpdb = (object) [];

		$result = $worker->get_last_error();

		$wpdb = (object) [
			'last_error' => null,
		];

		$result = $worker->get_last_error();

		$this->assertSame( '', $result );

		$wpdb->last_error = false;

		$result = $worker->get_last_error();

		$this->assertSame( '', $result );

		$wpdb->last_error = 'An error.';

		$result = $worker->get_last_error();

		$this->assertSame( 'An error.', $result );
	}
}
