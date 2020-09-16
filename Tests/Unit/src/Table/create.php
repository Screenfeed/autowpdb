<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->create().
 *
 * @covers Table::create
 * @group  Table
 */
class Test_Create extends TestCase {

	public function testShouldReturnTrue() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'create_table' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'create_table' )
			->with( 'custom_table', 'custom_table_schema', [ 'foo' => 'bar' ] )
			->willReturn( true );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_schema' )
			->willReturn( 'custom_table_schema' );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->create( [ 'foo' => 'bar' ] );

		$this->assertTrue( $result );
	}
}
