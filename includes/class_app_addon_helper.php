<?php

class App_AddonHandler {
	
	private function __construct () {
		define('APP_PLUGIN_ADDONS_DIR', APP_PLUGIN_DIR . '/includes/addons', true);
	}
	
	public static function serve () {
		$me = new App_AddonHandler;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {}

	public static function get_active_plugins () {
		$active = get_option('app_activated_plugins');
		$active = $active ? $active : array();

		return $active;
	}
	
	public static function is_plugin_active ($plugin) {
		$active = self::get_active_plugins();
		return in_array($plugin, $active);
	}

	public static function get_all_plugins () {
		$all = glob(APP_PLUGIN_ADDONS_DIR . '/*.php');
		$all = $all ? $all : array();
		$ret = array();
		foreach ($all as $path) {
			$ret[] = pathinfo($path, PATHINFO_FILENAME);
		}
		return $ret;
	}

	public static function plugin_to_path ($plugin) {
		$plugin = str_replace('/', '_', $plugin);
		return APP_PLUGIN_ADDONS_DIR . '/' . "{$plugin}.php";
	}

	public static function get_plugin_info ($plugin) {
		$path = self::plugin_to_path($plugin);
		$default_headers = array(
			'Name' => 'Plugin Name',
			'Author' => 'Author',
			'Description' => 'Description',
			'Plugin URI' => 'Plugin URI',
			'Version' => 'Version',
			'Requires' => 'Requires',
			'Detail' => 'Detail'
		);
		return get_file_data($path, $default_headers, 'plugin');
	}

	private function _activate_plugin ($plugin) {}

	private function _deactivate_plugin ($plugin) {}

	private static function to_plugin_requirements ($plugin, $req_string) {
		$requirements = array_map('trim', explode(',', $req_string));
		return $requirements;
	}
	
	public static function create_addon_settings () {
		$all = self::get_all_plugins();
		$active = self::get_active_plugins();
		$sections = array('thead');

		echo "<table class='widefat' id='app_addons_hub'>";
		echo '<thead>';
		echo '<tr>';
		echo '<th width="30%">' . __('Name', 'appointments') . '</th>';
		echo '<th>' . __('Description', 'appointments') . '</th>';
		echo '</tr>';
		echo '<thead>';
		echo "<tbody>";
		foreach ($all as $plugin) {
			$plugin_data = self::get_plugin_info($plugin);
			if (!@$plugin_data['Name']) continue; // Require the name
			$is_active = in_array($plugin, $active);
			$is_beta = false;
			if (!empty($plugin_data['Version']) && preg_match('/BETA/i', $plugin_data['Version'])) {
				$plugin_data['Version'] = '<span class="app-beta-version">' . $plugin_data['Version'] . '</span>';
				$is_beta = true;
			}
			echo "<tr>";
			echo "<td width='30%'>";
			echo '<b id="' . esc_attr($plugin) . '">' . $plugin_data['Name'] . '</b>';
			echo "<br />";
			echo ($is_active
				?
				'<a href="http://premium.wpmudev.org/project/appointments-plus/" class="app_deactivate_plugin" app:plugin_id="' . esc_attr($plugin) . '">' . __('Upgrade to Appointments+ to deactivate', 'appointments') . '</a>'
				:
				'<a href="http://premium.wpmudev.org/project/appointments-plus/" class="app_activate_plugin ' . ($is_beta ? "app-beta" : '') . '" app:plugin_id="' . esc_attr($plugin) . '">' . __('Upgrade to Appointments+ to activate', 'appointments') . '</a>'
			);
			echo "</td>";
			echo '<td>' .
				$plugin_data['Description'] .
			'';
			/*
			if ( $plugin_data['Detail'] )
				echo '&nbsp;' . $tips->add_tip( $plugin_data['Detail'] );
			*/
			if (!empty($plugin_data['Requires'])) {
				echo '<div class="app-addon-requires">' . __('Requires:', 'appointments') . ' ';
				$requirements = self::to_plugin_requirements($plugin, $plugin_data['Requires']);
				echo join(', ', $requirements);
				echo '</div>';
			}
			echo '</td>';
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
