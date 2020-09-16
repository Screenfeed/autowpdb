<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->quote_string().
 *
 * @covers Worker::quote_string
 * @group  Worker
 */
class Test_QuoteString extends TestCase {

	public function testShouldReturnQuotedStrings() {
		$values = [
			''       => "''",
			'string' => "'string'",
		];

		foreach ( $values as $actual => $expected ) {
			$result = ( new Worker() )->quote_string( $actual );

			$this->assertSame( $expected, $result );
		}
	}

	public function testShouldNotReturnQuotedStrings() {
		$values = [
			7,
			'6',
			'0.123',
			0.0,
			[],
		];

		foreach ( $values as $expected ) {
			$result = ( new Worker() )->quote_string( $expected );

			$this->assertSame( $expected, $result );
		}
	}
}
