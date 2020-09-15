<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->count().
 *
 * @covers Table::count
 * @group  Table
 */
class Test_Count extends TestCase {

	public function testShouldReturnNumberOfRows() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'count_table_rows' ] )
			->getMock();
		$worker
			->expects( $this->exactly( 2 ) )
			->method( 'count_table_rows' )
			->willReturnCallback( function ( $table_name, $column ) {
				$this->assertSame( 'custom_table', $table_name );
				switch( $column ) {
					case '*':
						return 3;
					case 'id':
						return 6;
					default:
						return 0;
				}
			} );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->exactly( 2 ) )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );
		$table_definition
			->expects( $this->exactly( 2 ) )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->count();

		$this->assertSame( 3, $result );

		$result = $table->count( 'id' );

		$this->assertSame( 6, $result );
	}
}
