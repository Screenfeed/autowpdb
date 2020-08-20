<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->delete().
 *
 * @covers Table::delete
 * @group  Table
 */
class Test_Delete extends TestCase {

	public function testShouldReturnTrue() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );

		DBUtilitiesUnit::set_mocks( [
			'delete_table' => function( $table_name, $args ) {
				$this->assertSame( 'custom_table', $table_name );
				$this->assertSame( [ 'foo' => 'bar' ], $args );
				return true;
			},
		] );

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->delete( [ 'foo' => 'bar' ] );

		$this->assertTrue( $result );
	}
}
