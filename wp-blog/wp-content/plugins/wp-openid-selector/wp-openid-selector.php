<?php
/*
Plugin Name: OpenId Selector
Plugin URI: http://www.st2i.com/wordpress/wp-openid-selector/
Description: Integration of openid-selector (http://code.google.com/p/openid-selector/) in wordpress.
Author: SARL ST2I
Version: 1.0
Author URI: http://www.st2i.com/
*/

load_plugin_textdomain('wpois', 'wp-content/plugins/wp-openid-selector/localization');

add_action( 'login_head', 'wp_openid_selector_login_head');
add_action( 'login_form', 'wp_openid_selector_login_form', 2 );
add_action( 'register_form', 'wp_openid_selector_login_form', 2 );

function wp_openid_selector_login_head() { 

	$g_openid_selector_url = WP_PLUGIN_URL.'/wp-openid-selector';
	$g_openid_selector_local_style_path = $g_openid_selector_url.'/css';
	$g_openid_selector_local_script_path = $g_openid_selector_url.'/js';
		
	echo "<!-- Simple OpenID Selector -->";
	if ( !wp_style_is('openid-selector', 'registered') ) {
		wp_register_style('openid-selector', $g_openid_selector_local_style_path . '/openid.css');
	}

	if ( did_action('wp_print_styles') ) {
		wp_print_styles('openid-selector');
	} else {
		wp_enqueue_style('openid-selector');
	}
	
	wp_register_script('openid-jquery.js', $g_openid_selector_local_script_path . '/openid-jquery.js', array( 'jquery' ));
	wp_print_scripts('openid-jquery.js');
	?>

	<script type="text/javascript">
	
	/*
		Simple OpenID Plugin
		http://code.google.com/p/openid-selector/
		
		This code is licenced under the New BSD License.
	*/
	
	var providers_large = {
		google : {
			name : 'Google',
			url : 'https://www.google.com/accounts/o8/id'
		},
		yahoo : {
			name : 'Yahoo',
			url : 'http://me.yahoo.com/'
		},
		aol : {
			name : 'AOL',
			label : '<?php _e('Enter your AOL screenname.', 'wpois') ?>',
			url : 'http://openid.aol.com/{username}'
		},
		myopenid : {
			name : 'MyOpenID',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'MyOpenID'); ?>',
			url : 'http://{username}.myopenid.com/'
		}/*,
		openid : {
			name : 'OpenID',
			label : '<?php _e('Enter your OpenID.', 'wpois') ?>',
			url : null
		}*/
	};
	
	var providers_small = {
		livejournal : {
			name : 'LiveJournal',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'Livejournal'); ?>',
			url : 'http://{username}.livejournal.com/'
		},
		wordpress : {
			name : 'Wordpress',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'Wordpress.com'); ?>',
			url : 'http://{username}.wordpress.com/'
		},
		blogger : {
			name : 'Blogger',
			label : '<?php _e('Your Blogger account:', 'wpois') ?>',
			url : 'http://{username}.blogspot.com/'
		},
		verisign : {
			name : 'Verisign',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'Verisign'); ?>',
			url : 'http://{username}.pip.verisignlabs.com/'
		},
		claimid : {
			name : 'ClaimID',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'ClaimID'); ?>',
			url : 'http://claimid.com/{username}'
		},
		clickpass : {
			name : 'ClickPass',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'ClickPass'); ?>',
			url : 'http://clickpass.com/public/{username}'
		},
		google_profile : {
			name : 'Google Profile',
			label : '<?php printf( __( 'Enter your %1 username.', 'wpois' ), 'Google Profile'); ?>',
			url : 'http://www.google.com/profiles/{username}'
		}
	};
	
	openid.lang = 'en';
	openid.demo_text = '<?php _e('In client demo mode. Normally would have submitted OpenID:', 'wpois') ?>';
	openid.signin_text = '<?php _e('Sign-In', 'wpois') ?>';
	openid.image_title = '<?php _e('log in with {provider}', 'wpois') ?>';
	openid.img_path = '<?php echo $g_openid_selector_url; ?>/images/';
	
	jQuery(document).ready(function() {
		if (jQuery('#loginform').length > 0) {openid.form_id = '#loginform';}
		else if (jQuery('#registerform').length > 0) {openid.form_id = '#registerform';}
		openid.init('openid_identifier'); 
	});
	</script>
	<!-- /Simple OpenID Selector -->
<?php }


function wp_openid_selector_login_form() {?>
<!-- Simple OpenID Selector -->
<hr id="openid_split" style="clear: both; margin-bottom: 1.0em; border: 0; border-top: 1px solid #999; height: 1px;" />
<div id="openid_choice">
	<p><label><?php _e('Or click your account provider:', 'wpois') ?></label></p>
	<div id="openid_btns"></div>
</div>
<div id="openid_input_area"></div>
<!-- /Simple OpenID Selector -->
<?php } ?>