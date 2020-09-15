<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->exists().
 *
 * @covers Table::exists
 * @group  Table
 */
class Test_Exists extends TestCase {

	public function testShouldReturnTrue() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'table_exists' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'table_exists' )
			->with( 'custom_table' )
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
		$result = $table->exists();

		$this->assertTrue( $result );
	}
}
