<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\AbstractCRUD;

use Screenfeed\AutoWPDB\CRUD\AbstractCRUD;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\src\CRUD\TestCase;

/**
 * Tests for AbstractTableDefinition::get_auto_increment_columns().
 *
 * @covers AbstractTableDefinition::get_auto_increment_columns
 * @group  AbstractTableDefinition
 */
class Test_GetAutoIncrementColumns extends TestCase {

	public function testShouldReturnEmptyArray() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_table_schema' )
			->willReturn( "path varchar(191) NOT NULL default ''" );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_auto_increment_columns' );

		$this->assertSame( [], $result );
	}

	public function testShouldReturnAutoIncrementColumns() {
		$definition = $this->createMock( TableDefinitionInterface::class );
		$definition
			->expects( $this->once() )
			->method( 'get_table_schema' )
			->willReturn( "file_id bigint(20) unsigned NOT NULL auto_increment" );
		$table      = $this->getMockForAbstractClass( AbstractCRUD::class, [ $definition ] );

		$result = $this->invokeMethod( $table, 'get_auto_increment_columns' );

		$this->assertSame( [ 'file_id' => '' ], $result );
	}
}
