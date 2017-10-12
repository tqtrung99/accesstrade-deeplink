<?php
/*
Plugin Name: Nhymxu AccessTrade Deeplink shortcode
Plugin URI: http://dungnt.net/nhymxu-at-deeplink-wp
Description: Shortcode chuyển link sản phẩm thành deeplink
Author: Dũng Nguyễn (nhymxu)
Version: 0.1
Author URI: http://dungnt.net
*/

class nhymxu_at_deeplink {
	public function __construct() {
		add_shortcode( 'at', [$this,'shortcode_callback'] );
		add_action('admin_menu', [$this,'admin_page']);
	}

	function admin_page() {
		add_options_page('Nhymxu AT Deeplink', 'Nhymxu AT Deeplink', 'manage_options', 'nhymxu_at_deeplink', [$this,'admin_page_callback']);
	}
	function generate( $url ) {
		$option = get_option('nhymxu_at_deeplink', ['uid' => '', 'utmsource' => '']);
		
		if( $option['uid'] == '' ) {
			return $url;
		}
	
		$utm_source = '';
		if( $option['utmsource'] != '' ) {
			$utm_source = '&utm_source='. $option['utmsource'];
		}
	
		return 'https://pub.accesstrade.vn/deep_link/'. $option['uid'] .'?url=' . rawurlencode( $url ) . $utm_source;
	}
	
	function shortcode_callback( $atts, $content = '' ) {
		$a = shortcode_atts( ['url' => ''], $atts );
		
		if( $a['url'] == '' ) {
			return '<a href="'. $this->generate( $content ).'" target="_blank">' . $content . '</a>';		
		} else if( $content != '' ) {
			return '<a href="'. $this->generate( $a['url'] ).'" target="_blank">' . do_shortcode($content) . '</a>';
		}
	}
	
	function admin_page_callback() {
		if( isset( $_POST, $_POST['nhymxu_hidden'] ) && $_POST['nhymxu_hidden'] == 'deeplink' ) {
			$input = [
				'uid'	=> sanitize_text_field($_REQUEST['nhymxu_at_deeplink_uid']),
				'utmsource'	=> sanitize_text_field($_REQUEST['nhymxu_at_deeplink_utmsource'])
			];
	
			update_option('nhymxu_at_deeplink', $input);
			echo '<h1>Cập nhật thành công</h1><br>';
		}
		$option = get_option('nhymxu_at_deeplink', ['uid' => '', 'utmsource' => '']);
	?>
	
	<div>
		<h2>Nhymxu AT Deeplink</h2>
		<br>
		<form action="options-general.php?page=nhymxu_at_deeplink" method="post">
			<input type="hidden" name="nhymxu_hidden" value="deeplink">
			<table>
				<tr>
					<td>AccessTrade ID*:</td>
					<td><input type="text" name="nhymxu_at_deeplink_uid" value="<?=$option['uid'];?>"></td>
				</tr>
				<tr>
					<td></td>
					<td>Lấy ID tại <a href="https://pub.accesstrade.vn/tools/deep_link" target="_blank">đây</a></td>
				</tr>
				<tr>
					<td>UTM Source:</td>
					<td><input type="text" name="nhymxu_at_deeplink_utmsource" value="<?=$option['utmsource'];?>"></td>
				</tr>
			</table>
			<input name="Submit" type="submit" value="Lưu">
		</form>
	</div>
	<?php
	}
}

new nhymxu_at_deeplink();