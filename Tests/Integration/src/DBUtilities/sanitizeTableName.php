<?php
namespace Screenfeed\AutoWPDB\Tests\Integration\src\DBUtilities;

use Screenfeed\AutoWPDB\DBUtilities;

/**
 * Tests for DBUtilities::sanitize_table_name().
 *
 * @covers DBUtilities::sanitize_table_name
 * @group  DBUtilities
 */
class Test_SanitizeTableName extends TestCase {

	public function testShouldReturnSanitizedTableName() {

		$result = DBUtilities::sanitize_table_name( ' _tâBLé---_nàm€_%$&0- ' );

		$this->assertSame( 'table_name_0', $result );

		$result = DBUtilities::sanitize_table_name( ' 0 ' );

		$this->assertSame( '0', $result );
	}

	public function testShouldReturnNullWhenOnlyInvalidCharacters() {

		$result = DBUtilities::sanitize_table_name( '&%£' );

		$this->assertNull( $result );
	}
}
