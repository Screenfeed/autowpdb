<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for Table->exists().
 *
 * @covers Table::exists
 * @group  Table
 */
class Test_Exists extends TestCase {

	public function testShouldReturnTrue() {
		$table_definition = new CustomTable();
		$table_definition->set_table_short_name( 'posts' );

		$table  = new Table( $table_definition );
		$result = $table->exists();

		$this->assertTrue( $result );
	}

	public function testShouldReturnFalse() {

		$table  = new Table( new CustomTable() );
		$result = $table->exists();

		$this->assertFalse( $result );
	}
}
