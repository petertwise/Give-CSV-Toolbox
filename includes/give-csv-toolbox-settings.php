<?php
/**
 * Give Settings Page/Tab
 *
 * @package     Give
 * @subpackage  Classes/Give_Settings_Data
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Give_Settings_CSV' ) ) :

	/**
	 * Give_Settings_CSV.
	 *
	 * @sine 1.0
	 */
	class Give_Settings_CSV {

		/**
		 * Setting page id.
		 *
		 * @since 1.0
		 * @var   string
		 */
		protected $id = '';

		/**
		 * Setting page label.
		 *
		 * @since 1.0
		 * @var   string
		 */
		protected $label = '';

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id    = 'csv-toolbox';
			$this->label = esc_html__( 'CSV Toolbox', 'give' );

			add_filter( 'give-tools_tabs_array', array( $this, 'add_settings_page' ), 99 );
			add_action( "give-tools_settings_{$this->id}_page", array( $this, 'output' ) );

			add_action( 'give_admin_field_csv_toolbox', array( $this, 'view_toolbox' ) );

			// Do not use main form for this tab.
			if ( give_get_current_setting_tab() === $this->id ) {
				add_action( 'give-tools_open_form', '__return_empty_string' );
				add_action( 'give-tools_close_form', '__return_empty_string' );
			}
		}

		/**
		 * Add this page to settings.
		 *
		 * @since  1.0
		 *
		 * @param  array $pages Lst of pages.
		 *
		 * @return array
		 */
		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = $this->label;

			return $pages;
		}


		/**
		 * View the UI.
		 */
		function view_toolbox() {
			?>

			<div class="give-csv-toolbox">
				<div class="give-csv-toolbox-title-wrap">
					<h3><span><?php esc_html_e( 'Export Donation History and Custom Fields to CSV', 'give' ); ?></span>
					</h3>
					<p><?php _e( 'Download an export of donors for specific donation forms with the option to include custom fields.', 'give' ); ?></p>
				</div>
				<td>
					<form method="post" id="give-csv-toolbox" class="give-export-form">
						<div id="give-csv-toolbox-form-select-wrap">
							<h4><?php esc_html_e( 'Select a Donation Form:', 'give' ); ?></h4>
							<?php
							$args = array(
								'name'   => 'forms',
								'id'     => 'give-form-for-csv-export',
								'chosen' => true,
								'number' => - 1,
							);
							echo Give()->html->forms_dropdown( $args ); ?>
						</div>

						<div id="give-csv-toolbox-export-options">

							<div id="give-csv-toolbox-export-date">

								<h4><?php esc_html_e( 'Filter by Date:', 'give' ); ?></h4>
								<?php
								$args = array(
									'id'          => 'give-payment-export-start',
									'name'        => 'start',
									'placeholder' => esc_attr__( 'Start date', 'give' ),
								);
								echo Give()->html->date_field( $args ); ?>
								<?php
								$args = array(
									'id'          => 'give-payment-export-end',
									'name'        => 'end',
									'placeholder' => esc_attr__( 'End date', 'give' ),
								);
								echo Give()->html->date_field( $args ); ?>
							</div>

							<div id="give-csv-toolbox-status">

								<h4><?php esc_html_e( 'Filter by Status:', 'give' ); ?></h4>
								<select name="status">
									<option value="any"><?php esc_html_e( 'All Statuses', 'give' ); ?></option>
									<?php
									$statuses = give_get_payment_statuses();
									foreach ( $statuses as $status => $label ) {
										echo '<option value="' . $status . '">' . $label . '</option>';
									}
									?>
								</select>

							</div>

						</div>
						<?php wp_nonce_field( 'give_ajax_export', 'give_ajax_export' ); ?>


						<div id="give-csv-toolbox-standard-fields" class="give-clearfix">
							<h4><?php esc_html_e( 'Standard Columns:', 'give' ); ?></h4>
							<ul id="give-export-option-ul">
								<li>
									<label for="give-export-donation-id">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donation_id]"
											id="give-export-donation-id"><?php esc_html_e( 'Donation ID', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-first-name">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[first_name]"
											id="give-export-first-name"><?php esc_html_e( 'Donor\'s First Name', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-last-name">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[last_name]"
											id="give-export-last-name"><?php esc_html_e( 'Donor\'s Last Name', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-email">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[email]"
											id="give-export-email"><?php esc_html_e( 'Donor\'s Email', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-address">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[address]"
											id="give-export-address"><?php esc_html_e( 'Donor\'s Billing Address', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-sum">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donation_total]"
											id="give-export-donation-sum"><?php esc_html_e( 'Donation Total', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-status">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donation_status]"
											id="give-export-donation-status"><?php esc_html_e( 'Donation Status', 'give' ); ?>
									</label>
								</li>
								<!-- Add Custom Fields -->
								<li>
									<label for="give-export-give-simple-note-field">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[give_simple_note_field]"
											id="give-export-give-simple-note-field"><?php esc_html_e( 'Note', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-give-simple-anon-field">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[give_simple_anon_field]"
											id="give-export-give-simple-anon-field"><?php esc_html_e( 'Anonymous', 'give' ); ?>
									</label>
								</li>
								<!-- END Add Custom Fields -->
								<li>
									<label for="give-export-payment-gateway">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[payment_gateway]"
											id="give-export-payment-gateway"><?php esc_html_e( 'Payment Gateway', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-form-id">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[form_id]"
											id="give-export-donation-form-id"><?php esc_html_e( 'Donation Form ID', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-form-title">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[form_title]"
											id="give-export-donation-form-title"><?php esc_html_e( 'Donation Form Title', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-form-level-id">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[form_level_id]"
											id="give-export-donation-form-level-id"><?php esc_html_e( 'Donation Form Level ID', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-form-level-title">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[form_level_title]"
											id="give-export-donation-form-level-title"><?php esc_html_e( 'Donation Form Level Title', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-date">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donation_date]"
											id="give-export-donation-date"><?php esc_html_e( 'Donation Date', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donation-time">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donation_time]"
											id="give-export-donation-time"><?php esc_html_e( 'Donation Time', 'give' ); ?>
									</label>
								</li>

								<li>
									<label for="give-export-userid">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[userid]"
											id="give-export-userid"><?php esc_html_e( 'User ID', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donorid">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donorid]"
											id="give-export-donorid"><?php esc_html_e( 'Donor ID', 'give' ); ?>
									</label>
								</li>
								<li>
									<label for="give-export-donor-ip">
										<input type="checkbox" checked
											name="give_csv_toolbox_export_option[donor_ip]"
											id="give-export-donor-ip"><?php esc_html_e( 'Donor IP Address', 'give' ); ?>
									</label>
								</li>
							</ul>
						</div>

						<div id="give-csv-toolbox-submit-wrap">
							<input type="hidden" name="give-export-class" value="Give_CSV_Toolbox_Donations_Export"/>

							<input type="submit" value="<?php esc_attr_e( 'Generate CSV', 'give' ); ?>"
								class="button button-primary"/>
							<span>
								<span class="spinner"></span>
							</span>

						</div>

					</form>
				</td>
			</div>

		<?php }


		/**
		 * Get settings array.
		 *
		 * @since  1.0
		 * @return array
		 */
		public function get_settings() {
			// Hide save button.
			$GLOBALS['give_hide_save_button'] = true;

			// Get settings.
			$settings = apply_filters( 'give_settings_csv_toolbox', array(
				array(
					'id'         => 'give_tools_tools',
					'type'       => 'title',
					'table_html' => false,
				),
				array(
					'id'   => 'csv_toolbox',
					'name' => esc_html__( 'CSV Toolbox', 'give' ),
					'type' => 'csv_toolbox',
				),
				array(
					'id'         => 'give_tools_tools',
					'type'       => 'sectionend',
					'table_html' => false,
				),
			) );

			/**
			 * Filter the settings.
			 *
			 * @since  1.0
			 *
			 * @param  array $settings
			 */
			$settings = apply_filters( 'give_get_settings_' . $this->id, $settings );

			// Output.
			return $settings;
		}

		/**
		 * Output the settings.
		 *
		 * @since  1.0
		 * @return void
		 */
		public function output() {
			$settings = $this->get_settings();

			Give_Admin_Settings::output_fields( $settings, 'give_settings' );
		}
	}

endif;

return new Give_Settings_CSV();
