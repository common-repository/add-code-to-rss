<?php
/*
Plugin Name: Add Code to RSS
Plugin URI: http://pokrovskii.com/plagin-add-code-to-rss/
Description: Adds custom PHP code before and after evry entry in the feed.
Author: Махim Pokrovskii
Author URI: http://pokrovskii.com/
Version: 1.1
*/

function add_code_to_rss ($text) {
	if (!is_feed())
		return $text;

	$add_to_rss_code_before = stripslashes(get_option('add_to_rss_code_before'));
	$add_to_rss_code_after = stripslashes(get_option('add_to_rss_code_after'));
	ob_start();
	eval(" ?> {$add_to_rss_code_before} <?php ");
	$result_before = ob_get_clean();

	ob_start();
	eval(" ?> {$add_to_rss_code_after} <?php ");
	$result_after = ob_get_clean();

	return $result_before . $text . $result_after;
}


function add_code_to_rss_options_page() {
	global $title;
?>
<div class="wrap">
	<h2><?php echo esc_attr($title); ?></h2>
<?php
	if (sizeof($_POST)) :
		check_admin_referer('modify-addcodetorss_settings');
		update_option('add_to_rss_code_before', $_POST['add_to_rss_code_before']);
		update_option('add_to_rss_code_after', $_POST['add_to_rss_code_after']);
?>
	<div class="updated"><p><?php _e('Settings updated', 'addcodetorss'); ?></p></div>
<?php endif; ?>

	<form method="post" action="">
		<fieldset class="options">
        <?php
		$add_to_rss_code_before = get_option('add_to_rss_code_before');
		$add_to_rss_code_after = get_option('add_to_rss_code_after');
        ?>
			<p><?php _e('Code to execute before the entry', 'addcodetorss'); ?></p>
			<textarea name="add_to_rss_code_before" cols="40" rows="10" style="width:80%;"><?php echo esc_attr(stripslashes($add_to_rss_code_before)); ?></textarea>
			<p><?php _e('Code to execute after the entry', 'addcodetorss'); ?></p>
			<textarea name="add_to_rss_code_after" cols="40" rows="10" style="width:80%;"><?php echo esc_attr(stripslashes($add_to_rss_code_after)); ?></textarea>
			<p class="submit"><input type="submit" value="<?php _e('Save', 'addcodetorss'); ?>" class="button button-primary"/><?php wp_nonce_field('modify-addcodetorss_settings'); ?></p>
		</fieldset>
	</form>
</div>
<?php
}

function add_code_to_rss_admin_menu()
{
	add_options_page(__('AddCodeToRSS Options', 'addcodetorss'), 'AddCodeToRSS', 8, __FILE__, 'add_code_to_rss_options_page');
}

if (!function_exists('esc_attr')) {
	function esc_attr($s)
	{
		return attribute_escape($s);
	}
}

function add_code_to_rss_init()
{
	if (is_admin()) {
		add_action('admin_menu', 'add_code_to_rss_admin_menu');
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('addcodetorss', '', 'add-code-to-rss');
		}
	}

	add_filter('the_excerpt_rss', 'add_code_to_rss');
	add_filter('the_content', 'add_code_to_rss');
}

add_action('init', 'add_code_to_rss_init');
?>