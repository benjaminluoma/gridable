<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pixelgrade.com
 * @since      1.0.0
 *
 * @package    Gridable
 * @subpackage Gridable/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gridable
 * @subpackage Gridable/admin
 * @author     Pixelgrade <contact@pixelgrade.com>
 */
class Gridable_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $gridable The ID of this plugin.
	 */
	private $gridable;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $gridable The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $gridable, $version ) {
		$this->gridable = $gridable;
		$this->version  = $version;
	}

	function add_media_button( $editor_id ) {
		// Setup the icon - currently using a dashicon
		$icon = '<span class="wp-media-buttons-icon dashicons dashicons-layout" style="font-size:16px;margin-top:-2px;"></span>';

		printf( '<a href="#" class="button gridable-insert-row-button" id="%s" data-editor="%s" title="%s">%s %s</a>',
			'gridable-add-row-button-' . $editor_id,
			esc_attr( $editor_id ),
			esc_attr__( 'Add Row', 'gridable' ),
			$icon,
			__( 'Add Row', 'gridable' )
		);

		/**
		 * Enqueue the editor script only when there is an editor on page.
		 * We ditch `admin_enqueue_scripts` intentionally since the editor can appear on non-edit pages like theme options
		 */
		wp_register_script( 'select2-full', plugin_dir_url( __FILE__ ) . 'js/select2.full.js', array( 'jquery' ), $this->version );

		wp_enqueue_script( 'gridable-editor', plugin_dir_url( __FILE__ ) . 'js/admin-editor.js', array( 'jquery', 'wp-color-picker', 'select2-full' ), $this->version, true );

		wp_register_style( 'select2-full', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version );

		wp_enqueue_style( 'gridable-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin-style.css', array( 'wp-color-picker', 'select2-full' ), $this->version, false );

		wp_localize_script( 'gridable-editor', 'gridable_editor_params', array(
			'new_column_content' => esc_html__( 'Content', 'gridable' )
		) );

		wp_localize_script( 'gridable-editor', 'GridableCallbacks', null );

		wp_localize_script( 'gridable-editor', 'gridable_row_options', apply_filters('gridable_row_options', array(
			'cols_nr' => array(
				'default' => 2
			)
		) ) );

		wp_localize_script( 'gridable-editor', 'gridable_column_options', apply_filters('gridable_column_options', array(
			'size' => array(
				'default' => 6
			)
		) ) );

		global $editor_styles;
		if ( ! empty( $editor_styles ) && is_array($editor_styles) ) {
			$editor_styles = array_merge( $editor_styles, array(
				plugin_dir_url( __FILE__ ) . 'css/editor-style.css'
			) );
		} else {
			$editor_styles = array(
				plugin_dir_url( __FILE__ ) . 'css/editor-style.css'
			);
		}
	}

	function wp_print_grider_tinymce_templates() {
		$row_classes = array(
			'gridable',
			'gridable--grid',
			'grid'
		);
		$col_classes = array(
			'grid__item'
		); ?>
<script type="text/html" id="tmpl-gridable-grider-row"><div contenteditable="false" class="{{data.classes}} <?php echo join( ' ', apply_filters( 'gridable_mce_sh_row_classes', $row_classes ) ); ?>" {{{data.atts}}} data-gridable-row="1" data-mce-resize="false" data-mce-placeholder="1">{{{data.content}}}</div></script>
<script type="text/html" id="tmpl-gridable-grider-col"><div contenteditable="true" class="{{data.classes}} <?php echo join( ' ', apply_filters( 'gridable_mce_sh_col_classes', $col_classes ) ); ?>" {{{data.atts}}} data-mce-resize="false" data-mce-placeholder="1"><div class="gridable__handle"></div>{{{data.content}}}</div></script>
	<?php
		do_action( 'gridable_print_row_options_templates' );
		do_action( 'gridable_print_column_options_templates' );
	}

	function add_grider_tinymce_plugin( $plugin_array ) {
		$plugin_array['gridable'] = plugin_dir_url( __FILE__ ) . 'js/gridable.js';

		return $plugin_array;
	}

	function my_add_styles_admin() {
		global $current_screen;

		if ( is_admin() ) { ?>
			<script type="text/javascript">
				var gridable_params = {
					sh_col_classes: JSON.parse('<?php echo json_encode( apply_filters( 'gridable_sh_col_attr_size', array() ) ) ?>'),
					l10n: JSON.parse('<?php echo json_encode( apply_filters( 'gridable_editor_l10n_labels', array(
						'remove_row'         => esc_html__( 'Remove Row', 'gridable' ),
						'remove_column'      => esc_html__( 'Remove Column', 'gridable' ),
						'edit_row'           => esc_html__( 'Edit Row Options', 'gridable' ),
						'edit_column'           => esc_html__( 'Edit Column Options', 'gridable' ),
						'add_column'         => esc_html__( 'Add Column', 'gridable' ),
						'new_column_content' => esc_html__( 'Content', 'gridable' ),
						'column'         => esc_html__( 'Column', 'gridable' ),
						'row'         => esc_html__( 'Row', 'gridable' ),
					) ) ) ?>')
				};
			</script>
			<?php
		}
	}
}
