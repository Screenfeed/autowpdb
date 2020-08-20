<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\DBUtilities;

use Brain\Monkey\Functions;
use Mockery;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\DBUtilitiesUnit;

/**
 * Tests for DBUtilities::prepare_values_list().
 *
 * @covers DBUtilities::prepare_values_list
 * @group  DBUtilities
 */
class Test_PrepareValuesList extends TestCase {

	public function testShouldReturnPreparedList() {
		$this->createMocks();

		$result = DBUtilitiesUnit::prepare_values_list(
			[
				2,
				'3',
				'',
				"string with ' \" quotes",
				'string',
			]
		);

		$this->assertSame( "2,3,'','string with \' \\\" quotes','string'", $result );
	}

	public function createMocks() {
		Functions\expect( 'esc_sql' )
			->once()
			->with( Mockery::type( 'array' ) )
			->andReturnUsing(
				function ( $values ) {
					return array_map( 'addslashes', $values );
				}
			);

		DBUtilitiesUnit::set_mocks( [
			'quote_string' => function ( $value ) {
				switch ( $value ) {
					case '2':
					case '3':
						$return = $value;
						break;
					default:
						$return = "'" . $value . "'";
						break;
				}
				return $return;
			},
		] );
	}
}
