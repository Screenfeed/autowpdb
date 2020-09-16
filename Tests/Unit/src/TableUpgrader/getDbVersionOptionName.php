<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::get_db_version_option_name().
 *
 * @covers TableUpgrader::get_db_version_option_name
 * @group  TableUpgrader
 */
class Test_GetDbVersionOptionName extends TestCase {

	public function testShouldReturnOptionName() {
		$table_def = $this->createMock( CustomTable::class );
		$table_def
			->expects( $this->once() )
			->method( 'get_table_short_name' )
			->with()
			->willReturn( 'wp_table_short_name' );

		$table = $this->createMock( Table::class );
		$table
			->expects( $this->once() )
			->method( 'get_table_definition' )
			->with()
			->willReturn( $table_def );

		$upgrader = new TableUpgrader( $table );

		$this->assertSame( 'wp_table_short_name_db_version', $upgrader->get_db_version_option_name() );
	}
}
