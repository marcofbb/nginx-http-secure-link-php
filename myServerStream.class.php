<?php 

class myServerStream {
	
	private static $ttl = 86400;
	private static $secret = 'secret';
	
	public static function get_real_ip(){
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public static function expire_time(){
		return time() + self::$ttl;
	}
	
	public static function base64url_encode($data) { 
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	}
	
	public static function get_hash($time_expire, $path){

		$hash_item = array();
		$hash_item[] = $time_expire;
		$hash_item[] = $path;
		$hash_item[] = self::get_real_ip();
		$hash_item[] = ' ' . self::$secret;
		
		
		$hash = self::base64url_encode(md5( implode($hash_item), true ));
		return $hash;
	}

	public static function get_path($path){
		$expire = self::expire_time();
		$hash = self::get_hash($expire, $path);
		return $path.'?st='.$hash.'&e='.$expire;
	}
	
	public static function apply_security($url){
		$data_link = parse_url($url);
		$path = myServerStream::get_path($data_link['path']);
		$url = $data_link['scheme'].'://'.$data_link['host'].$path;
		return $url;
	}
	
}

$my_video = 'https://myServerStream.com/video.mp4'
echo myServerStream::apply_security($my_video);
