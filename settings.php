<?php
/**
 * @author Igal Alkon <igal.alkon@gmail.com>
 * @version 0.1
 *
 * a Class to save application settings (as settings.json)
 *
 * @todo if needed make this handles multi-files
 * 
*/

class Settings {
	public function set($name, $value) {
		$settings_file = file_get_contents('settings.json');
		$settings_array = (array)json_decode($settings_file);
		$settings_array[$name] = $value;
		$settings_file = json_encode($settings_array);
		file_put_contents('settings.json', $settings_file);
	}

	public function load() {
		$arr = (array)json_decode(file_get_contents('settings.json'));
		echo "loading config file...";
		return $arr;
	}
}

?>
