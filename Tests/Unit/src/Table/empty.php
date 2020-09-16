<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->empty().
 *
 * @covers Table::empty
 * @group  Table
 */
class Test_Empty extends TestCase {

	public function testShouldReturnNumberOfDeletedRows() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'empty_table' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'empty_table' )
			->with( 'custom_table' )
			->willReturn( 3 );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->empty();

		$this->assertSame( 3, $result );
	}
}
