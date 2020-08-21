<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for AbstractTableDefinition::__toString().
 *
 * @covers AbstractTableDefinition::__toString
 * @group  AbstractTableDefinition
 */
class Test___toString extends TestCase {

	public function testShouldReturnFullTableName() {
		global $wpdb;

		$table = new CustomTable();

		$result = $table->__toString();

		$this->assertIsString( $result );
		$this->assertSame( wp_json_encode( $table ), $result );
	}
}
