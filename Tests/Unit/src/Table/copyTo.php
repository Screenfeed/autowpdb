<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->copy_to().
 *
 * @covers Table::copy_to
 * @group  Table
 */
class Test_CopyTo extends TestCase {

	public function testShouldReturnNumberOfInsertedRows() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );

		DBUtilitiesUnit::$mocks = [
			'copy_table'         => function( $table_name, $new_table_name ) {
				$this->assertSame( 'custom_table', $table_name );
				$this->assertSame( 'new_custom_table', $new_table_name );
				return 3;
			},
			'sanitize_table_name' => function( $table_name ) {
				$this->assertSame( 'n€w_custom_tabl€', $table_name );
				return 'new_custom_table';
			},
		];

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->copy_to( 'n€w_custom_tabl€' );

		$this->assertSame( 3, $result );
	}

	public function testShouldReturnZeroWhenNewTableNameIsInvalid() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->never() )
			->method( 'get_table_name' );

		DBUtilitiesUnit::$mocks = [
			'sanitize_table_name' => function( $table_name ) {
				$this->assertSame( '&%£', $table_name );
				return null;
			},
		];

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->copy_to( '&%£' );

		$this->assertSame( 0, $result );
	}
}
