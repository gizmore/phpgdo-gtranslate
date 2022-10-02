<?php
namespace GDO\GTranslate;

use GDO\Net\HTTP;
use GDO\User\GDO_User;

/**
 * Google translation call without API key. Oops.
 * 
 * @author noother, gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class GT
{
	
	const AUTO = 'auto';
	
	public static function t(string $text, string $from=null, string $to=null, string &$error='') : string
	{
		$to = $to ? $to : GDO_User::current()->getLangISO();
		$from = $from ? $from : self::AUTO;
		$headers = [
			'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:14.0) Gecko/20100101 Firefox/14.0.1',
		];
		$url = "https://translate.googleapis.com/translate_a/single";
		$data = [
			'client' => 'gtx',
			'sl' => $from,
			'tl' => $to,
			'dt' => 't',
			'q' => $text,
		];
		$result = HTTP::post($url, $data, false, $headers, false, $error);
		return $result === false ? $text : $result;
	}

	public static function composeTranslation(string $result) : string
	{
		$s = '';
		$t = json_decode($result, true);
		foreach ($t[0] as $tr)
		{
			$s .= $tr[0];
		}
		return $s;
	}
	
}
