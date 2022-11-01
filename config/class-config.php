<?php
defined( 'ABSPATH' ) or die( "you do not have access to this page!" );

if ( ! class_exists( "burst_config" ) ) {

	class burst_config {
		private static $_this;
		public $fields = array();
		public $steps = array();
		public $sections;
		public $pages;
		public $warning_types;
		public $yes_no;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( burst_sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;


			//common options type
			$this->yes_no = array(
				'yes' => __( 'Yes', 'burst-statistics' ),
				'no'  => __( 'No', 'burst-statistics' ),
			);

			/* config files */
			require_once( burst_path . '/config/general-settings.php' );

			/**
			 * The integrations are loaded with priority 10
			 * Because we want to initialize after that, we use 15 here
			 */
			add_action( 'plugins_loaded', array( $this, 'init' ), 15 );
			add_filter( 'burst_notices', array( $this, 'notices' ) );
		}

		static function this() {
			return self::$_this;
		}

		/**
		 * Add list of notices with conditions
		 * @param $notices
		 *
		 * @return array[]
		 */
		public function notices( $notices ) {
			$new_notices = [
				'pretty-permalinks-error' => [
					'success_conditions'  => [
						'get_option_permalink_structure', // If pretty permalinks are enabled
					],
					'plus_one' => true,
					'urgent' => __( "You are using 'plain' permalinks on your site. This is not supported.", 'burst-statistics' ) . '&nbsp; <a href="'.admin_url('options-permalink.php').'">'.__("Change the setting", "burst-statistics").'</a>',
					'include_in_progress' => true,
					'dismissible' => false,
				],
				'tracking-error' => [
					'warning_condition' => 'burst_tracking_status_error', // true if rest and beacon don't work
					'plus_one' => true,
					'urgent' => __( "Due to your server or website configuration it is not possible to track statistics.", 'burst-statistics' ) . burst_read_more("https://burst-statistics.com/troubleshoot-tracking/"),
					'include_in_progress' => false,
					'dismissible' => false,
				],
//				'tracking-with-rest-api' => [
//					'warning_condition' => 'burst_tracking_status_rest_api', // true if rest api is enabled and beacon does not work
//					'plus_one' => false,
//					'open' => __( "Due to your server or website configuration the fastest tracking method is not available, the fallback is used instead.", 'burst-statistics' ) . burst_read_more('https://burst-statistics.com/troubleshoot-tracking/'),
//					'include_in_progress' => false,
//					'dismissible' => false,
//				],
				'new-feature-turbo-mode' => [
					'success_conditions'  => [
						'false',
					],
					'plus_one' => true,
					'new' => __( "We have improved tracking and added Turbo mode! Tracking has become faster and more accurate. Want an even faster page load? Enable Turbo Mode.", 'burst-statistics' ) . burst_read_more('https://burst-statistics.com/new-feature-tracking-with-endpoint', '%s' . __('Learn more', 'burst-statistics') .'%s'),
					'include_in_progress' => true,
					'dismissible' => true,
				],
				'bf_2022' => [
					'warning_condition'  => 'burst_is_bf', // If pretty permalinks are enabled
					'plus_one' => true,
					'open' => __( "Black Friday sale! Get 40% Off Burst Pro.", 'burst-statistics' ) . burst_read_more('https://burst-statistics.com/pro', '%s' . __('Learn more', 'burst-statistics') .'%s'),
					'include_in_progress' => true,
					'dismissible' => true,
				],
				'leave_feedback' => [
					'status' => 'completed',
					'success_conditions'  => [
						'false',
					],
					'plus_one' => false,
					'dismissible' => false,
					'open'    =>  burst_sprintf(
						__( 'If you have any suggestions to improve our plugin, feel free to %sopen a support thread%s.', 'burst-statistics' ),
						'<a href="https://wordpress.org/support/plugin/burst-statistics/" target="_blank">',
						'</a>'
					),
				]
			];
			return $notices + $new_notices;
		}


		public function get_section_by_id( $id ) {

			$steps = $this->steps['wizard'];
			foreach ( $steps as $step ) {
				if ( ! isset( $step['sections'] ) ) {
					continue;
				}
				$sections = $step['sections'];

				//because the step arrays start with one instead of 0, we increase with one
				return array_search( $id, array_column( $sections, 'id' ) ) + 1;
			}

		}

		public function get_step_by_id( $id ) {
			$steps = $this->steps['wizard'];

			//because the step arrays start with one instead of 0, we increase with one
			return array_search( $id, array_column( $steps, 'id' ) ) + 1;
		}


		public function fields(
			$page = false, $step = false, $section = false,
			$get_by_fieldname = false
		) {

			$output = array();
			$fields = $this->fields;
			if ( $page ) {
				$fields = burst_array_filter_multidimensional( $this->fields,
					'source', $page );
			}

			foreach ( $fields as $fieldname => $field ) {
				if ( $get_by_fieldname && $fieldname !== $get_by_fieldname ) {
					continue;
				}

				if ( $step ) {
					if ( $section && isset( $field['section'] ) ) {
						if ( ( $field['step'] == $step
						       || ( is_array( $field['step'] )
						            && in_array( $step, $field['step'] ) ) )
						     && ( $field['section'] == $section )
						) {
							$output[ $fieldname ] = $field;
						}
					} else {
						if ( ( $field['step'] == $step )
						     || ( is_array( $field['step'] )
						          && in_array( $step, $field['step'] ) )
						) {
							$output[ $fieldname ] = $field;
						}
					}
				}
				if ( ! $step ) {
					$output[ $fieldname ] = $field;
				}

			}

			return $output;
		}

		public function preload_init(){
			$this->fields = apply_filters( 'burst_fields_load_types', $this->fields );
		}

		public function init() {
			$this->steps = apply_filters('burst_steps', $this->steps );
			$this->fields = apply_filters('burst_fields', $this->fields );
		}

		public function has_sections( $page, $step ) {
			if ( isset( $this->steps[ $page ][ $step ]["sections"] ) ) {
				return true;
			}

			return false;
		}

	}

} //class closure
