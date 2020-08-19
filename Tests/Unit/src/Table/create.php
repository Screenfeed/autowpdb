<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->create().
 *
 * @covers Table::create
 * @group  Table
 */
class Test_Create extends TestCase {

	public function testShouldReturnTrue() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_schema' )
			->willReturn( 'custom_table_schema' );

		DBUtilitiesUnit::$mocks = [
			'create_table' => function( $table_name, $schema_query, $args ) {
				$this->assertSame( 'custom_table', $table_name );
				$this->assertSame( 'custom_table_schema', $schema_query );
				$this->assertSame( [ 'foo' => 'bar' ], $args );
				return true;
			},
		];

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->create( [ 'foo' => 'bar' ] );

		$this->assertTrue( $result );
	}
}
