<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->delete().
 *
 * @covers Table::delete
 * @group  Table
 */
class Test_Delete extends TestCase {

	public function testShouldReturnTrue() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'delete_table' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'delete_table' )
			->with( 'custom_table', [ 'foo' => 'bar' ] )
			->willReturn( true );

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
		$result = $table->delete( [ 'foo' => 'bar' ] );

		$this->assertTrue( $result );
	}
}
