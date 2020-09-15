<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->get_last_error().
 *
 * @covers Table::get_last_error
 * @group  Table
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'get_last_error' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'get_last_error' )
			->willReturn( 'An error.' );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->get_last_error();

		$this->assertSame( 'An error.', $result );
	}
}
