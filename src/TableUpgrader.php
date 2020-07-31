<?php
/**
 * Contains the class that creates or upgrades a table automatically.
 *
 * @package Screenfeed/AutoWPDB
 */

declare( strict_types=1 );

namespace Screenfeed\AutoWPDB;

use Screenfeed\AutoWPDB\Table;
use Screenfeed\AutoWPDB\TableDefinition\TableDefinitionInterface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that creates or upgrades a table automatically.
 *
 * @since 0.1
 * @uses  add_action()
 * @uses  is_multisite()
 * @uses  get_site_option()
 * @uses  get_option()
 * @uses  update_site_option()
 * @uses  update_option()
 * @uses  delete_site_option()
 * @uses  delete_option()
 * @uses  wp_should_upgrade_global_tables()
 * @uses  apply_filters()
 */
class TableUpgrader {

	/**
	 * Suffix used in the name of the options that store the table version.
	 *
	 * @var   string
	 * @since 0.1
	 */
	const TABLE_VERSION_OPTION_SUFFIX = '_db_version';

	/**
	 * A Table object.
	 *
	 * @var   Table
	 * @since 0.1
	 */
	protected $table;

	/**
	 * Tell if table downgrade is allowed.
	 *
	 * @var   bool
	 * @since 0.1
	 */
	protected $handle_downgrade;

	/**
	 * Name of the hook that will trigger the table creation/upgrade.
	 *
	 * @var   string
	 * @since 0.1
	 */
	protected $upgrade_hook;

	/**
	 * Priority for the hook that will trigger the table creation/upgrade. Default is 8.
	 *
	 * @var   int
	 * @since 0.1
	 */
	protected $upgrade_hook_prio;

	/**
	 * Tell if the table is ready to be used.
	 *
	 * @var   bool
	 * @since 0.1
	 */
	protected $table_ready = false;

	/**
	 * Get things started.
	 *
	 * @since 0.1
	 *
	 * @param Table        $table A Table object.
	 * @param array<mixed> $args  {
	 *     Optional arguments.
	 *
	 *     @var bool   $handle_downgrade  Set to true to allow table downgrade. Default is false.
	 *     @var string $upgrade_hook      Name of the hook that will trigger the table creation/upgrade. Use an empty string to not create the hook. Default is 'admin_menu'.
	 *     @var int    $upgrade_hook_prio Priority for the hook that will trigger the table creation/upgrade. Default is 8.
	 * }
	 */
	public function __construct( Table $table, array $args = [] ) {
		$this->table             = $table;
		$this->handle_downgrade  = ! empty( $args['handle_downgrade'] );
		$this->upgrade_hook      = isset( $args['upgrade_hook'] ) ? $args['upgrade_hook'] : 'admin_menu';
		$this->upgrade_hook_prio = isset( $args['upgrade_hook_prio'] ) ? (int) $args['upgrade_hook_prio'] : 8;

		if ( ! $this->table_is_up_to_date() ) {
			/**
			 * The option doesn't exist or is not up-to-date: we must upgrade the table before declaring it ready.
			 * See self::maybe_upgrade_table() for the upgrade.
			 */
			return;
		}

		$this->set_table_ready();
	}

	/**
	 * Init:
	 * - Launch hooks.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function init() {
		if ( empty( $this->upgrade_hook ) ) {
			return;
		}

		add_action( $this->upgrade_hook, [ $this, 'maybe_upgrade_table' ], $this->upgrade_hook_prio );
	}

	/**
	 * Tell if the table is ready to be used.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function table_is_ready(): bool {
		return $this->table_ready;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** TABLE VERSION =========================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Tell if the table is up-to-date.
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public function table_is_up_to_date(): bool {
		$table_version = $this->get_db_version();

		if ( empty( $table_version ) ) {
			return false;
		}

		if ( $this->handle_downgrade ) {
			return $table_version !== $this->table->get_table_definition()->get_table_version();
		}

		return $table_version >= $this->table->get_table_definition()->get_table_version();
	}

	/**
	 * Get the table version stored in DB.
	 *
	 * @since 0.1
	 *
	 * @return int The version. 0 if not set yet.
	 */
	public function get_db_version(): int {
		if ( $this->table->get_table_definition()->is_table_global() && is_multisite() ) {
			return (int) get_site_option( $this->get_db_version_option_name() );
		}

		return (int) get_option( $this->get_db_version_option_name() );
	}

	/**
	 * Update the table version stored in DB.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function update_db_version() {
		$table_definition = $this->table->get_table_definition();

		if ( $table_definition->is_table_global() && is_multisite() ) {
			update_site_option( $this->get_db_version_option_name(), $table_definition->get_table_version() );
		} else {
			update_option( $this->get_db_version_option_name(), $table_definition->get_table_version() );
		}
	}

	/**
	 * Delete the table version stored in DB.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function delete_db_version() {
		if ( $this->table->get_table_definition()->is_table_global() && is_multisite() ) {
			delete_site_option( $this->get_db_version_option_name() );
		} else {
			delete_option( $this->get_db_version_option_name() );
		}
	}

	/**
	 * Get the name of the option that stores the table version.
	 *
	 * @since 0.1
	 *
	 * @return string
	 */
	public function get_db_version_option_name(): string {
		return $this->table->get_table_definition()->get_table_short_name() . self::TABLE_VERSION_OPTION_SUFFIX;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** TABLE CREATION ========================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Maybe create/upgrade the table in the database.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function maybe_upgrade_table() {
		global $wpdb;

		if ( $this->table_is_up_to_date() ) {
			// The table has the right version.
			$this->set_table_ready();
			return;
		}

		if ( ! $this->table_is_allowed_to_upgrade() ) {
			$this->set_table_not_ready();
			return;
		}

		// Create/Upgrade the table.
		$this->upgrade_table();
	}

	/**
	 * Tell if the table is allowed to be created/upgraded.
	 *
	 * @since 0.2
	 *
	 * @return bool
	 */
	public function table_is_allowed_to_upgrade(): bool {
		$allowed          = true;
		$table_version    = $this->get_db_version();
		$table_definition = $this->table->get_table_definition();

		if ( ! empty( $table_version ) && $table_definition->is_table_global() && ! wp_should_upgrade_global_tables() ) {
			// The table exists, is global, but upgrade of the global tables is forbidden.
			$allowed = false;
		}

		/**
		 * Tell if the table is allowed to be created/upgraded.
		 *
		 * @since 0.2
		 *
		 * @param bool                     $allowed          True when the table is allowed to be created/upgraded. False otherwise.
		 * @param TableDefinitionInterface $table_definition An instance of the TableDefinitionInterface used.
		 */
		return (bool) apply_filters( 'screenfeed_autowpdb_table_is_allowed_to_upgrade', $allowed, $table_definition );
	}

	/**
	 * Create/Upgrade the table in the database.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function upgrade_table() {
		if ( ! $this->table->create() ) {
			// Failure.
			$this->set_table_not_ready();
			return;
		}

		// Table successfully created/upgraded.
		$this->set_table_ready();
		$this->update_db_version();
	}

	/**
	 * Set various properties to tell the table is ready to be used.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function set_table_ready() {
		global $wpdb;

		$table_definition        = $this->table->get_table_definition();
		$table_short_name        = $table_definition->get_table_short_name();
		$this->table_ready       = true;
		$wpdb->$table_short_name = $table_definition->get_table_name();

		if ( $table_definition->is_table_global() ) {
			$wpdb->global_tables[] = $table_short_name;
		} else {
			$wpdb->tables[] = $table_short_name;
		}
	}

	/**
	 * Unset various properties to tell the table is NOT ready to be used.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	protected function set_table_not_ready() {
		global $wpdb;

		$table_definition  = $this->table->get_table_definition();
		$table_short_name  = $table_definition->get_table_short_name();
		$this->table_ready = false;
		unset( $wpdb->$table_short_name );

		if ( $table_definition->is_table_global() ) {
			$wpdb->global_tables = array_diff( $wpdb->global_tables, [ $table_short_name ] );
		} else {
			$wpdb->tables = array_diff( $wpdb->tables, [ $table_short_name ] );
		}
	}
}
