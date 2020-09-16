<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::init().
 *
 * @covers TableUpgrader::init
 * @group  TableUpgrader
 */
class Test_Init extends TestCase {

	public function testShouldSetTableReadyAndAddHook() {
		$upgrader = $this->createMocks(
			[
				'upgrade_hook'               => 'foobar',
				'upgrade_hook_prio'          => 5,
				'expected_upgrade_hook'      => 'foobar',
				'expected_upgrade_hook_prio' => 5,
			],
			true
		);

		$upgrader->init();

		$upgrader = $this->createMocks(
			[
				'expected_upgrade_hook'      => 'admin_menu',
				'expected_upgrade_hook_prio' => 8,
			],true
		);

		$upgrader->init();
	}

	public function testShouldNotSetTableReadyNorAddHook() {
		$upgrader = $this->createMocks(
			[
				'upgrade_hook' => false,
			],
			false
		);

		$upgrader->init();
	}

	public function createMocks( $args = [], $table_ready ) {
		$table    = $this->createMock( Table::class );
		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'table_is_up_to_date', 'set_table_ready' ] )
			->setConstructorArgs( [ $table, $args ] )
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'table_is_up_to_date' )
			->with()
			->willReturn( $table_ready );
		$upgrader
			->expects( $table_ready ? $this->once() : $this->never() )
			->method( 'set_table_ready' );

		if ( ! empty( $args['expected_upgrade_hook'] ) ) {
			Actions\expectAdded( $args['expected_upgrade_hook'] )->with( [ $upgrader, 'maybe_upgrade_table' ], $args['expected_upgrade_hook_prio'] );
		} else {
			Functions\expect( 'add_action' )->never();
		}

		return $upgrader;
	}
}
