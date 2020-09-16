<?php
namespace Screenfeed\AutoWPDB\Tests\Unit\src\TableUpgrader;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableUpgrader;
use Screenfeed\AutoWPDB\Tests\Fixtures\src\Table\CustomTable;
use Screenfeed\AutoWPDB\Tests\Unit\TestCase;

/**
 * Tests for TableUpgrader::maybe_upgrade_table().
 *
 * @covers TableUpgrader::maybe_upgrade_table
 * @group  TableUpgrader
 */
class Test_MaybeUpgradeTable extends TestCase {

	public function testShouldSetReadyWhenUpToDate() {
		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'table_is_up_to_date', 'set_table_ready', 'table_is_allowed_to_upgrade', 'set_table_not_ready', 'upgrade_table' ] )
			->disableOriginalConstructor()
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'table_is_up_to_date' )
			->with()
			->willReturn( true );
		$upgrader
			->expects( $this->once() )
			->method( 'set_table_ready' );
		$upgrader
			->expects( $this->never() )
			->method( 'table_is_allowed_to_upgrade' );
		$upgrader
			->expects( $this->never() )
			->method( 'set_table_not_ready' );
		$upgrader
			->expects( $this->never() )
			->method( 'upgrade_table' );

		$upgrader->maybe_upgrade_table();
	}

	public function testShouldSetNotReadyWhenNotUpToDateAndDowngradeNotAllowed() {
		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'table_is_up_to_date', 'set_table_ready', 'table_is_allowed_to_upgrade', 'set_table_not_ready', 'upgrade_table' ] )
			->disableOriginalConstructor()
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'table_is_up_to_date' )
			->with()
			->willReturn( false );
		$upgrader
			->expects( $this->never() )
			->method( 'set_table_ready' );
		$upgrader
			->expects( $this->once() )
			->method( 'table_is_allowed_to_upgrade' )
			->with()
			->willReturn( false );
		$upgrader
			->expects( $this->once() )
			->method( 'set_table_not_ready' );
		$upgrader
			->expects( $this->never() )
			->method( 'upgrade_table' );

		$upgrader->maybe_upgrade_table();
	}

	public function testShouldLaunchUpgrade() {
		$upgrader = $this->getMockBuilder( TableUpgrader::class )
			->setMethods( [ 'table_is_up_to_date', 'set_table_ready', 'table_is_allowed_to_upgrade', 'set_table_not_ready', 'upgrade_table' ] )
			->disableOriginalConstructor()
			->getMock();

		$upgrader
			->expects( $this->once() )
			->method( 'table_is_up_to_date' )
			->with()
			->willReturn( false );
		$upgrader
			->expects( $this->never() )
			->method( 'set_table_ready' );
		$upgrader
			->expects( $this->once() )
			->method( 'table_is_allowed_to_upgrade' )
			->with()
			->willReturn( true );
		$upgrader
			->expects( $this->never() )
			->method( 'set_table_not_ready' );
		$upgrader
			->expects( $this->once() )
			->method( 'upgrade_table' );

		$upgrader->maybe_upgrade_table();
	}
}
