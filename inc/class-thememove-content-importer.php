<?php
/**
 * Class for declaring the content importer used in the ThemeMove Core plugin
 *
 * @package ThemeMove_Core
 */

/**
 * Class ThemeMove_Content_Importer
 */
class ThemeMove_Content_Importer {

	/**
	 * The importer class object used for importing content.
	 *
	 * @var WXRImporter
	 */
	private $importer;

	/**
	 * Time in milliseconds, marking the beginning of the import.
	 *
	 * @var float
	 */
	private $microtime;

	/**
	 * The instance of the ThemeMove_Import_Logger class.
	 *
	 * @var ThemeMove_Import_Logger
	 */
	public $logger;

	/**
	 * The instance of the ThemeMove_Content_Importer class
	 *
	 * @var ThemeMove_Importer
	 */
	private $tmi;

	/**
	 * Constructor
	 *
	 * @param array  $importer_options Importer Options.
	 * @param object $logger Logger object.
	 */
	public function __construct( $importer_options = array(), $logger = null ) {

		// Set the WordPress Importer v2 as the importer used in this plugin.
		$this->importer = new WXRImporter( $importer_options );

		// Set logger to the importer.
		$this->logger = $logger;
		if ( ! empty( $this->logger ) ) {
			$this->set_logger( $this->logger );
		}

		// Get the ThemeMove_Importer instance.
		$this->tmi = ThemeMove_Importer::instance();
	}

	/**
	 * Set the logger used in the import
	 *
	 * @param object $logger logger instance.
	 */
	public function set_logger( $logger ) {
		$this->importer->set_logger( $logger );
	}

	/**
	 * Imports content from a WordPress export file.
	 *
	 * @param string $data_file path to xml file, file with WordPress export data.
	 */
	public function import( $data_file ) {
		$this->importer->import( $data_file );
	}

	/**
	 * Get all protected variables from the WXR_Importer needed for continuing the import.
	 */
	public function get_importer_data() {
		return $this->importer->get_importer_data();
	}

	/**
	 * Sets all protected variables from the WXR_Importer needed for continuing the import.
	 *
	 * @param array $data with set variables.
	 */
	public function set_importer_data( $data ) {
		$this->importer->set_importer_data( $data );
	}

	/**
	 * Import content XML
	 *
	 * @param string $import_file_path Content.xml file path.
	 */
	public function import_content( $import_file_path ) {
		$this->microtime = microtime( true );

		// Increase PHP max execution time. Just in case, even though the AJAX calls are only 25 sec long.
		set_time_limit( apply_filters( 'tmc_time_limit_for_demo_data_import', 300 ) );

		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Check, if we need to send another AJAX request and set the importing author to the current user.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'new_ajax_request_maybe' ) );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		if ( ! apply_filters( 'tmc_regenerate_thumbnails', false ) ) {
			add_filter( 'intermediate_image_sizes_advanced', '__return_null' );
		}

		// Import content.
		if ( ! empty( $import_file_path ) ) {
			ob_start();
			$this->import( $import_file_path );
			$message = ob_get_clean();
		}

		// Return any error messages for the front page output (errors, critical, alert and emergency level messages only).
		return $this->logger->error_output;
	}

	/**
	 * Check if we need to create a new AJAX request, so that server does not timeout.
	 *
	 * @param array $data current post data.
	 * @return array
	 */
	public function new_ajax_request_maybe( $data ) {
		$time = microtime( true ) - $this->microtime;

		// We should make a new ajax call, if the time is right.
		if ( $time > apply_filters( 'tmc_time_for_one_ajax_call', 25 ) ) {
			$response = array(
				'status'  => 'newAJAX',
				'message' => 'Time for new AJAX request!: ' . $time,
			);

			// Add any output to the log file and clear the buffers.
			$message = ob_get_clean();

			// Add any error messages to the frontend_error_messages variable in OCDI main class.
			if ( ! empty( $message ) ) {
				$this->tmi->append_to_frontend_error_messages( $message );
			}

			// Add message to log file.
			ThemeMove_Import_Logger::append_to_file(
				esc_html__( 'New AJAX call!', 'thememove-core' ) . PHP_EOL . $message,
				$this->tmi->get_log_file_path(),
				''
			);

			// Set the current importer stat, so it can be continued on the next AJAX call.
			$this->set_current_importer_data();

			// Send the request for a new AJAX call.
			wp_send_json( $response );
		}

		// Set importing author to the current user.
		// Fixes the [WARNING] Could not find the author for ... log warning messages.
		$current_user_obj    = wp_get_current_user();
		$data['post_author'] = $current_user_obj->user_login;

		return $data;
	}

	/**
	 * Set current state of the content importer, so we can continue the import with new AJAX request.
	 */
	private function set_current_importer_data() {
		$data = array_merge( $this->tmi->get_current_importer_data(), $this->get_importer_data() );
		set_transient( 'tmc_importer_data', $data, 0.1 * HOUR_IN_SECONDS );
	}
}
