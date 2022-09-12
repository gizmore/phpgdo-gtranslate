<?php
namespace GDO\GTranslate;

use GDO\Net\HTTP;
use GDO\User\GDO_User;

final class GT
{
	public static function t(string $text, string $from=null, string $to=null)
	{
		$to = $to ? $to : GDO_User::current()->getLangISO();
		$from = $from ? $from : 'auto';
		$headers = [
			'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:14.0) Gecko/20100101 Firefox/14.0.1',
		];
		$url = "https://translate.googleapis.com/translate_a/single?";
		$data = [
			'client' => 'gtx',
			'sl' => $from,
			'tl' => $to,
			'dt' => 't',
			'q' => $text,
		];
		$url .= http_build_query($data);
		$result = HTTP::getFromURL($url, false, false, $headers);
		return $result;
	}

}
