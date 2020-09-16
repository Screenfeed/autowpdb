<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBWorker\Worker;

use Screenfeed\AutoWPDB\DBWorker\Worker;

/**
 * Tests for Worker->sanitize_table_name().
 *
 * @covers Worker::sanitize_table_name
 * @group  Worker
 */
class Test_SanitizeTableName extends TestCase {

	public function testShouldReturnSanitizedTableName() {

		$worker = new Worker();
		$result = $worker->sanitize_table_name( ' _tâBLé---_nàm€_%$&0- ' );

		$this->assertSame( 'table_name_0', $result );

		$result = $worker->sanitize_table_name( ' 0 ' );

		$this->assertSame( '0', $result );
	}

	public function testShouldReturnNullWhenOnlyInvalidCharacters() {

		$result = ( new Worker() )->sanitize_table_name( '&%£' );

		$this->assertNull( $result );
	}
}
