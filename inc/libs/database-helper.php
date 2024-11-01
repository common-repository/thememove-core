<?php
/**
 * Export MySQL Tables.
 *
 * By https://github.com/tazotodua/useful-php-scripts
 *
 * @package ThemeMove Core
 */

/**
 * Export database
 *
 * @param string $host Host.
 * @param string $user Database Username.
 * @param string $pass Database Password.
 * @param string $name Database Name.
 * @param string $tables Tables we need to export.
 * @param string $file_name File name.
 */
function tmc_export_database( $host, $user, $pass, $name, $tables = false, $file_name = false ) {
	set_time_limit( 3000 );

	$mysqli = new mysqli( $host, $user, $pass, $name );
	$mysqli->select_db( $name );
	$mysqli->query( "SET NAMES 'utf8'" );
	$query_tables = $mysqli->query( 'SHOW TABLES' );

	while ( $row = $query_tables->fetch_row() ) {
		$target_tables[] = $row[0];
	}

	if ( false !== $tables ) {
		$target_tables = array_intersect( $target_tables, $tables );
	}

	$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `" . $name . "`\r\n--\r\n\r\n\r\n";

	foreach ( $target_tables as $table ) {
		if ( empty( $table ) ) {
			continue;
		}

		$result          = $mysqli->query( 'SELECT * FROM `' . $table . '`' );
		$fields_amount   = $result->field_count;
		$rows_num        = $mysqli->affected_rows;
		$res             = $mysqli->query( 'SHOW CREATE TABLE ' . $table );
		$table_m_line    = $res->fetch_row();
		$table_m_line[1] = str_ireplace( 'CREATE TABLE `', 'CREATE TABLE IF NOT EXISTS `', $table_m_line[1] );
		$content        .= "\n\n" . $table_m_line[1] . ";\n\n";

		for ( $i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0 ) {
			while ( $row = $result->fetch_row() ) { // when started (and every after 100 command cycle).

				if ( 0 === $st_counter % 100 || 0 === $st_counter ) {
					$content .= "\nINSERT INTO " . $table . ' VALUES';
				}

				$content .= "\n(";

				for ( $j = 0; $j < $fields_amount; $j++ ) {
					$row[ $j ] = str_replace( "\n", "\\n", addslashes( $row[ $j ] ) );

					if ( isset( $row[ $j ] ) ) {
						$content .= '"' . $row[ $j ] . '"';
					} else {
						$content .= '""';
					}     if ( $j < ( $fields_amount - 1 ) ) {
						$content .= ',';
					}
				}

				$content .= ')';

				// every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler.
				if ( ( 0 === ( $st_counter + 1 ) % 100 && 0 !== $st_counter ) || $st_counter++ === $rows_num ) {
					$content .= ';';
				} else {
					$content .= ',';
				}

				$st_counter = $st_counter + 1;
			}
		} $content .= "\n\n\n";
	}
	$content    .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	$file_name  = $file_name ? $file_name : $name . '___(' . date( 'H-i-s' ) . '_' . date( 'd-m-Y' ) . ').sql';

	ob_get_clean();

	header( 'Content-Type: application/octet-stream', true, 200 );
	header( 'Content-Transfer-Encoding: Binary' );
	header( "Content-Disposition: attachment; filename={$file_name}" );
	header( 'Content-Length: ' . ( function_exists( 'mb_strlen' ) ? mb_strlen( $content, '8bit' ) : strlen( $content ) ) );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	echo $content;
	exit;
}

/**
 * Export database
 *
 * @param string $data SQL string.
 */
function tmc_import_database( $data ) {
	global $wpdb;
	$uri      = str_replace( '/', '\\/', TMC_SITE_URI ) . '\\';
	$templine = '';
	$lines    = explode( "\n", $data );

	foreach ( $lines as $line ) {
		if ( '--' === substr( $line, 0, 2 ) || '' === $line ) {
			continue;
		}

		$templine .= $line;

		if ( ';' === substr( trim( $line ), -1, 1 ) ) {
			ob_start();
			$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				str_replace( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					array( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
						'%TMC_SITE_URI%',
						'wp_',
					),
					array( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
						$uri, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
						$wpdb->prefix,
					),
					$templine // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				),
				false
			);

			$templine = '';
			ob_end_clean();
		}
	}
}
