<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->count().
 *
 * @covers Table::count
 * @group  Table
 */
class Test_Count extends TestCase {

	public function testShouldReturnNumberOfRows() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->exactly( 2 ) )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );

		DBUtilitiesUnit::$mocks = [
			'count_table_rows' => function( $table_name, $column ) {
				$this->assertSame( 'custom_table', $table_name );
				$this->assertSame( '*', $column );
				return 3;
			},
		];

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->count();

		$this->assertSame( 3, $result );

		DBUtilitiesUnit::$mocks = [
			'count_table_rows' => function( $table_name, $column ) {
				$this->assertSame( 'custom_table', $table_name );
				$this->assertSame( ' id ', $column );
				return 6;
			},
		];

		$result = $table->count( ' id ' );

		$this->assertSame( 6, $result );
	}
}
