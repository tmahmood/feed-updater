<?php

/**
 * Class JSON
 * @author Tarin Mahmood
 */
class JSON
{
	public static function json_decode_file($file_name, $as_object = false)
	{
		$json = file_get_contents($file_name);
		if ($as_object) {
			return json_decode($json);
		}
		return json_decode($json, 1);
	}

	public static function json_encode_to_file($file_name, $content)
	{
		file_put_contents($file_name, json_encode($content));
	}
}


