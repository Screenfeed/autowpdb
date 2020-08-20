<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->empty().
 *
 * @covers Table::empty
 * @group  Table
 */
class Test_Empty extends TestCase {

	public function testShouldReturnNumberOfDeletedRows() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );

		DBUtilitiesUnit::set_mocks( [
			'empty_table' => function( $table_name ) {
				$this->assertSame( 'custom_table', $table_name );
				return 3;
			},
		] );

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->empty();

		$this->assertSame( 3, $result );
	}
}
