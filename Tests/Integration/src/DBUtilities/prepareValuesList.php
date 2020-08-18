<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::prepare_values_list().
 *
 * @covers DBUtilities::prepare_values_list
 * @group  DBUtilities
 */
class Test_PrepareValuesList extends TestCase {

	public function testShouldReturnPreparedList() {

		$result = DBUtilities::prepare_values_list(
			[
				2,
				'3',
				'',
				"string with ' \" quotes",
				'string',
			]
		);

		$this->assertSame( "2,3,'','string with \' \\\" quotes','string'", $result );
	}
}
