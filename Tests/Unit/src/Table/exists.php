<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->exists().
 *
 * @covers Table::exists
 * @group  Table
 */
class Test_Exists extends TestCase {

	public function testShouldReturnTrue() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );

		DBUtilitiesUnit::set_mocks( [
			'table_exists' => function( $table_name ) {
				$this->assertSame( 'custom_table', $table_name );
				return true;
			},
		] );

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->exists();

		$this->assertTrue( $result );
	}
}
