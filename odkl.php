<?php
/*
Plugin Name: Share Button "KLASS"
Plugin URI: http://artlosk.com/2010/10/odnoklassniki-i-knopka-klass/
Description: The plugin implements the API function socials networks that adds the link share button.
Author: Loskutnikov Artem
Version: 1.0
Author URI: http://artlosk.com/
License: GPL2
*/

/*  Copyright 2010 Loskutnikov Artem (artlosk) (email: artlosk at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

?>
<?php
if (!defined('TT_KLASS_INIT')) define('TT_KLASS_INIT', 1);
else return;

$tt_klass_btntypes = array('button_count', 'simple_button', 'small_button');

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

function klass_get_wp_version() {
    return (float)substr(get_bloginfo('version'),0,3);
}


function tt_register_klass_settings() {
    register_setting('tt_klass', 'tt_klass_width');
    register_setting('tt_klass', 'tt_klass_height');
    register_setting('tt_klass', 'tt_klass_btntype');
    register_setting('tt_klass', 'tt_klass_show_at_top');
    register_setting('tt_klass', 'tt_klass_show_at_bottom');
    register_setting('tt_klass', 'tt_klass_show_on_page');
    register_setting('tt_klass', 'tt_klass_show_on_post');
    register_setting('tt_klass', 'tt_klass_margin_top');
    register_setting('tt_klass', 'tt_klass_margin_bottom');
    register_setting('tt_klass', 'tt_klass_margin_left');
    register_setting('tt_klass', 'tt_klass_margin_right');

}

function tt_klass_init() {
    global $tt_klass_settings;

    if (klass_get_wp_version() >= 2.7) {
        if ( is_admin() ) {
            add_action( 'admin_init', 'tt_register_klass_settings' );
        }
    }
    add_filter('the_content', 'tt_klass_widget');
    add_filter('admin_menu', 'tt_klass_admin_menu');

    add_option('tt_klass_width', '80');
    add_option('tt_klass_height', '30');
    add_option('tt_klass_btntype', 'button_count');
    add_option('tt_klass_show_at_top', 'false');
    add_option('tt_klass_show_at_bottom', 'true');
    add_option('tt_klass_show_on_page', 'true');
    add_option('tt_klass_show_on_post', 'true');
    add_option('tt_klass_margin_top', '2');
    add_option('tt_klass_margin_bottom', '2');
    add_option('tt_klass_margin_left', '0');
    add_option('tt_klass_margin_right', '0');




    $tt_klass_settings['width'] = get_option('tt_klass_width');
    $tt_klass_settings['height'] = get_option('tt_klass_height');
    $tt_klass_settings['btntype'] = get_option('tt_klass_btntype');
    $tt_klass_settings['showattop'] = get_option('tt_klass_show_at_top') === 'true';
    $tt_klass_settings['showatbottom'] = get_option('tt_klass_show_at_bottom') === 'true';
    $tt_klass_settings['showonpage'] = get_option('tt_klass_show_on_page') === 'true';
    $tt_klass_settings['showonpost'] = get_option('tt_klass_show_on_post') === 'true';
    $tt_klass_settings['margin_top'] = get_option('tt_klass_margin_top');
    $tt_klass_settings['margin_bottom'] = get_option('tt_klass_margin_bottom');
    $tt_klass_settings['margin_left'] = get_option('tt_klass_margin_left');
    $tt_klass_settings['margin_right'] = get_option('tt_klass_margin_right');


    add_action('wp_head', 'tt_klass_widget_header_meta');
    load_plugin_textdomain( 'tt_klass_trans_domain', '', $plugin_path );


}

function tt_klass_widget_header_meta() {
?>
	<link href="http://stg.odnoklassniki.ru/share/odkl_share.css" rel="stylesheet">
	<script src="http://stg.odnoklassniki.ru/share/odkl_share.js" type="text/javascript" ></script>
<?php
}

function tt_klass_widget($content, $sidebar = false) {

	global $tt_klass_settings;

	if(is_single() && !$tt_klass_settings['showonpost'])
		return $content;

	if(is_page() && !$tt_klass_settings['showonpage'])
		return $content;


	$button_count  =  '<div class="button" style="width:'.$tt_klass_settings['width'].'px; height: '.$tt_klass_settings['height'].'px; 
			  margin: '.$tt_klass_settings['margin_top'].'px '.$tt_klass_settings['margin_bottom'].'px '.$tt_klass_settings['margin_left'].'px '.$tt_klass_settings['margin_right'].'px">
			  <a class="odkl-klass-stat" href="" onclick="ODKL.Share(this);return false;" ><span>0</span></a>
			  </div>
			  <div style="clear:both;"></div>';
	$simple_button =  '<div class="button" style="width:'.$tt_klass_settings['width'].'px; height: '.$tt_klass_settings['height'].'px; 
			  margin: '.$tt_klass_settings['margin_top'].'px '.$tt_klass_settings['margin_bottom'].'px '.$tt_klass_settings['margin_left'].'px '.$tt_klass_settings['margin_right'].'px">
			  <a class="odkl-klass" href="" onclick="ODKL.Share(this);return false;" >Класс!</a>
			  </div>
			  <div style="clear:both;"></div>';
	$small_button  =  '<div class="button" style="width:'.$tt_klass_settings['width'].'px; height: '.$tt_klass_settings['height'].'px; 
			  margin: '.$tt_klass_settings['margin_top'].'px '.$tt_klass_settings['margin_bottom'].'px '.$tt_klass_settings['margin_left'].'px '.$tt_klass_settings['margin_right'].'px">
			  <a class="odkl-klass-s" href="" onclick="ODKL.Share(this);return false;" ></a>
			  </div>
			  <div style="clear:both;"></div>';
	if($tt_klass_settings['btntype']=='button_count') {
		$button=$button_count;
	} else if($tt_klass_settings['btntype']=='simple_button') {
		$button=$simple_button;
	} else if($tt_klass_settings['btntype']=='small_button') {
		$button=$small_button;
	}

	if($tt_klass_settings['showattop']=='true')
		$content = $button.$content;

	if($tt_klass_settings['showatbottom']=='true')
		$content .= $button;


	return $content;

}

function tt_klass_admin_menu() {
    add_options_page('Klass Button Plugin Options', 'Klass Button', 8, __FILE__, 'tt_klass_plugin_options');
}
function tt_klass_plugin_options() {
    global $tt_klass_btntypes;
    global $tt_klass_settings;
?>
    <table>
    <tr>
    <td>

    <div class="wrap">
    <h2>Klass Share Button for social network Odnoklassniki.ru</h2>
    <form method="post" action="options.php">
    <?php
        if (klass_get_wp_version() < 2.7) {
            wp_nonce_field('update-options');
        } else {
            settings_fields('tt_klass');
        }
    ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><h3><?php _e("Appearance", 'tt_klass_trans_domain' ); ?></h3></th>
	</tr>
        <tr valign="top">
            <th scope="row"><?php _e("Width:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="text" name="tt_klass_width" value="<?php echo get_option('tt_klass_width'); ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Height:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="text" name="tt_klass_height" value="<?php echo get_option('tt_klass_height'); ?>" /></td>
        </tr>
<?php
                    $curmenutype = get_option('tt_klass_btntype');
?>
        <tr valign="top">
            <th scope="row"><?php _e("Button with count:", 'tt_klass_trans_domain' ); ?></th>
            <td><div style="float:left;"><input type="radio" name="tt_klass_btntype" value="button_count" <?php if($tt_klass_btntypes[0]==$curmenutype) { echo "checked='checked'";} else { echo ''; } ?> /></div>
            <div style="float:left;padding-left:3px;"><img src="<?php echo WP_PLUGIN_URL;?>/klass/sample_img/btncount.png" /></div></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Simple Button:", 'tt_klass_trans_domain' ); ?></th>
            <td><div style="float:left;"><input type="radio" name="tt_klass_btntype" value="simple_button" <?php if($tt_klass_btntypes[1]==$curmenutype) { echo "checked='checked'";} else { echo ''; } ?> /></div>
            <div style="float:left;padding-left:3px;"><img src="<?php echo WP_PLUGIN_URL;?>/klass/sample_img/btnsimple.png" /></div></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Small Button:", 'tt_klass_trans_domain' ); ?></th>
            <td><div style="float:left;"><input type="radio" name="tt_klass_btntype"  value="small_button" <?php if($tt_klass_btntypes[2]==$curmenutype) { echo "checked='checked'";} else { echo ''; } ?> /></div>
            <div style="float:left;padding-left:3px;"><img src="<?php echo WP_PLUGIN_URL;?>/klass/sample_img/btnsmall.gif" /></div></td>
        </tr>

        <tr valign="top">
            <th scope="row"><h3><?php _e("Position", 'tt_klass_trans_domain' ); ?></h3></th>
	</tr>
        <tr>
            <th scope="row"><?php _e("Show at Top:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="checkbox" name="tt_klass_show_at_top" value="true" <?php echo (get_option('tt_klass_show_at_top') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show at Bottom:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="checkbox" name="tt_klass_show_at_bottom" value="true" <?php echo (get_option('tt_klass_show_at_bottom') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show on Page:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="checkbox" name="tt_klass_show_on_page" value="true" <?php echo (get_option('tt_klass_show_on_page') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Show on Post:", 'tt_klass_trans_domain' ); ?></th>
            <td><input type="checkbox" name="tt_klass_show_on_post" value="true" <?php echo (get_option('tt_klass_show_on_post') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Margin Top:", 'tt_klass_trans_domain' ); ?></th>
            <td><input size="5" type="text" name="tt_klass_margin_top" value="<?php echo get_option('tt_klass_margin_top'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Margin Bottom:", 'tt_klass_trans_domain' ); ?></th>
            <td><input size="5" type="text" name="tt_klass_margin_bottom" value="<?php echo get_option('tt_klass_margin_bottom'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Margin Left:", 'tt_klass_trans_domain' ); ?></th>
            <td><input size="5" type="text" name="tt_klass_margin_left" value="<?php echo get_option('tt_klass_margin_left'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Margin Right:", 'tt_klass_trans_domain' ); ?></th>
            <td><input size="5" type="text" name="tt_klass_margin_right" value="<?php echo get_option('tt_klass_margin_right'); ?>" />px</td>
        </tr>

    </table>
    <?php if (klass_get_wp_version() < 2.7) : ?>
    	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="tt_klass_width, tt_klass_height, tt_klass_btntype, button_count, simple_button, small_button, tt_klass_show_at_top, tt_klass_show_at_bottom, tt_klass_show_on_page, tt_klass_show_on_post, tt_klass_margin_top, tt_klass_margin_bottom, tt_klass_margin_left, tt_klass_margin_right" />
    <?php endif; ?>

    <p class="submit">
    <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
    </p>

    </form>
    </div>
    </td>
    </tr>
    </table>
<?php
}
tt_klass_init();
?>