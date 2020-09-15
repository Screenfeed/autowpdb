<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for AbstractTableDefinition->get_table_name().
 *
 * @covers AbstractTableDefinition::get_table_name
 * @group  AbstractTableDefinition
 */
class Test_GetTableName extends TestCase {

	public function testShouldReturnFullTableName() {
		global $wpdb;

		$table = new CustomTable();

		$result = $table->get_table_name();

		$this->assertSame( $wpdb->base_prefix . 'foobar', $result );

		$table = new CustomTable();
		$table->set_table_is_global( false );

		$result = $table->get_table_name();

		$this->assertSame( $wpdb->prefix . 'foobar', $result );
	}
}
