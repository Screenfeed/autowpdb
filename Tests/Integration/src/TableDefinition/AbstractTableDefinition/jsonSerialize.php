<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\TableDefinition\AbstractTableDefinition;

use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Integration\TestCase;

/**
 * Tests for AbstractTableDefinition::jsonSerialize().
 *
 * @covers AbstractTableDefinition::jsonSerialize
 * @group  AbstractTableDefinition
 */
class Test_JsonSerialize extends TestCase {

	public function testShouldReturnFullTableName() {
		global $wpdb;

		$table = new CustomTable();

		$result   = $table->jsonSerialize();
		$expected = [
			'table_version'       => $table->get_table_version(),
			'table_short_name'    => $table->get_table_short_name(),
			'table_name'          => $table->get_table_name(),
			'table_is_global'     => $table->is_table_global(),
			'primary_key'         => $table->get_primary_key(),
			'column_placeholders' => $table->get_column_placeholders(),
			'column_defaults'     => $table->get_column_defaults(),
			'table_schema'        => $table->get_table_schema(),
		];

		$this->assertSame( $expected, $result );
	}
}
