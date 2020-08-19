<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\Table;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for Table->get_last_error().
 *
 * @covers Table::get_last_error
 * @group  Table
 */
class Test_GetLastError extends TestCase {

	public function testShouldReturnLastError() {
		$table_definition = $this->createMock( TableDefinitionInterface::class );

		DBUtilitiesUnit::$mocks = [
			'get_last_error' => 'An error.',
		];

		$table  = new Table( $table_definition, DBUtilitiesUnit::class );
		$result = $table->get_last_error();

		$this->assertSame( 'An error.', $result );
	}
}
