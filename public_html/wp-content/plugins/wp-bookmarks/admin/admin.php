<?php

class wpb_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'wpb';
		$this->subslug = 'wpb';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( wpb_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'settings' => __('Global Settings','wpb'),
			'bookmark-collections' => __('Bookmark Collections','wpb'),
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('wpb');
		if (!get_option('wpb')) {
			update_option('wpb', wpb_default_options() );
		}
		
	}
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->subslug;
		$icon = wpb_url . "admin/images/$slug-32.png";
		echo '<style type="text/css">';
			if (in_array( $screen->id, array( $slug ) ) || strstr($screen->id, $slug) ) {
				print "#icon-$slug {background: url('{$icon}') no-repeat left;}";
			}
		echo '</style>';
	}

	function add_styles(){
		
		wp_register_style('wpb_chosen', wpb_url . 'css/wpb-chosen.css' );
		wp_enqueue_style('wpb_chosen');
		
		wp_register_style('wpb_admin_css', wpb_url . 'admin/css/wpb-admin.css' );
		wp_enqueue_style('wpb_admin_css');
		
		wp_register_script('wpb_chosen', wpb_url . 'scripts/wpb-chosen.js', array('jquery') );
		wp_enqueue_script('wpb_chosen');
		
		wp_register_script( 'wpb_admin', wpb_url.'admin/scripts/admin.js', array( 
			'jquery',
		) );
		wp_enqueue_script( 'wpb_admin' );
		
	}
	
	function add_menu() {
		add_menu_page(  __('User Bookmarks','wpb'), __('User Bookmarks','wpb'), 'manage_options', $this->slug, array(&$this, 'admin_page'), wpb_url .'admin/images/'.$this->slug.'-16.png', '199.7870');
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once wpb_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
	
		$this->options['exclude_post_types'] = '';
		
		/* other post fields */
		foreach($_POST as $key => $value) {
			if ($key != 'submit') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = esc_attr($_POST[$key]);
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
		
		update_option('wpb', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','wpb').'</strong></p></div>';
	}

	function reset() {
		update_option('wpb', wpb_default_options() );
		$this->options = array_merge( $this->options, wpb_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','wpb').'</strong></p></div>';
	}

	function admin_page() {

		if (isset($_POST['submit'])) {
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		if (isset($_POST['rebuild-pages'])) {
			$this->rebuild_pages();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
			
			<div id="icon-<?php echo $this->subslug; ?>" class="icon32"><br /></div><h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>
		<?php if(!get_option('wpb_show_notifiation')){?>
		<div id="wpb-notification-bar">
			<span class="wpb-notification-bar-close">
				X
			</span>
			<div class="wpb-notification-bar-img">
				<img src="https://thumb-cc.s3.envato.com/files/218684979/userpro-thumbnail.jpg" />
			</div>
			<div class="wpb-notification-bar-text">
				<a href="https://codecanyon.net/user/deluxethemes/portfolio" target="_blank"><?php _e('Click here ','wpb');?></a> <?php _e('to explore more plugins from DeluxeThemes')?>
			</div>
			
		</div>
		<?php }?>
	<?php }

}

global $wpb_admin;
$wpb_admin = new wpb_admin();