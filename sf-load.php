<?php
/**
 * Bootstrap the ABSPATH constant and load sf-config.php.
 * If sf-config.php is not found, search for it in the parent directory.
 *
 * @package Stuff
 */

/** Define ABSPATH as this files directory */
define( 'ABSPATH', dirname(__FILE__) . '/' );

error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );

if ( file_exists( ABSPATH . 'sf-config.php') ) { 

        /** The config file is here. */
        require_once( ABSPATH . 'sf-config.php' );

} elseif ( file_exists( dirname(ABSPATH) . '/sf-config.php' ) && ! file_exists( dirname(ABSPATH) . '/sf-settings.php' ) ) { 

        /** The config file is one level up. */
        require_once( dirname(ABSPATH) . '/sf-config.php' );

} else {

        die( sprintf( "There's no sf-config.php file. One must be created before you can continue." ) );

}

require_once( ABSPATH . '/sf-includes/ez_sql_core.php' );
require_once( ABSPATH . '/sf-includes/ez_sql_mysql.php' );
require_once( ABSPATH . '/sf-includes/phpass.php' );
require_once( ABSPATH . '/sf-includes/functions.php' );

global $sfdb;
$sfdb = new ezSQL_mysql( DB_USER, DB_PASS, DB_NAME, DB_HOST ) or die( 'Cannot connect to database.' );
