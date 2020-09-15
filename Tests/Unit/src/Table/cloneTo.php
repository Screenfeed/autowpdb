<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\DBWorker\Worker;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->clone_to().
 *
 * @covers Table::clone_to
 * @group  Table
 */
class Test_CloneTo extends TestCase {

	public function testShouldReturnTrue() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'clone_table', 'sanitize_table_name' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'clone_table' )
			->with( 'custom_table', 'new_custom_table' )
			->willReturn( true );
		$worker
			->expects( $this->once() )
			->method( 'sanitize_table_name' )
			->with( 'n€w_custom_tabl€' )
			->willReturn( 'new_custom_table' );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_name' )
			->willReturn( 'custom_table' );
		$table_definition
			->expects( $this->exactly( 2 ) )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->clone_to( 'n€w_custom_tabl€' );

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalseWhenNewTableNameIsInvalid() {
		$worker = $this->getMockBuilder( Worker::class )
			->setMethods( [ 'sanitize_table_name' ] )
			->getMock();
		$worker
			->expects( $this->once() )
			->method( 'sanitize_table_name' )
			->with( '&%£' )
			->willReturn( null );

		$table_definition = $this->createMock( TableDefinitionInterface::class );
		$table_definition
			->expects( $this->never() )
			->method( 'get_table_name' );
		$table_definition
			->expects( $this->once() )
			->method( 'get_table_worker' )
			->willReturn( $worker );

		$table  = new Table( $table_definition );
		$result = $table->clone_to( '&%£' );

		$this->assertFalse( $result );
	}
}
