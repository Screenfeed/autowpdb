<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::quote_string().
 *
 * @covers DBUtilities::quote_string
 * @group  DBUtilities
 */
class Test_QuoteString extends TestCase {

	public function testShouldReturnQuotedStrings() {
		$values = [
			''       => "''",
			'string' => "'string'",
		];

		foreach ( $values as $actual => $expected ) {
			$result = DBUtilities::quote_string( $actual );

			$this->assertEquals( $expected, $result );
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
			$result = DBUtilities::quote_string( $expected );

			$this->assertEquals( $expected, $result );
		}
	}
}
