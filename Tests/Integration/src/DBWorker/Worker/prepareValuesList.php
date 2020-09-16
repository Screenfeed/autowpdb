<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->prepare_values_list().
 *
 * @covers Worker::prepare_values_list
 * @group  Worker
 */
class Test_PrepareValuesList extends TestCase {

	public function testShouldReturnPreparedList() {

		$result = ( new Worker() )->prepare_values_list(
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
