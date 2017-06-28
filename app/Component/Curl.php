<?php
namespace App\Component;

class Curl
{
    private static $_instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	public function curl($url, $method, $params = [], $headers = [])
	{
		$ret = [];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Buit-in WEB API');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

		switch ($method) {
		case 'POST':
			curl_setopt($ch, CURLOPT_POST, TRUE);
			if (!empty($params)) {
				if(is_array($params)) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
				}else {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				}
			}
			break;
		case 'PUT':
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			if (! empty($params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			}
			break;
		case 'DELETE':
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			if (! empty($params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			}
			break;
		default:
			curl_setopt($ch, CURLOPT_POST, FALSE);
			if (! empty($params)) {
				$url = $url . "?" . http_build_query($params);
			}
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		$body = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

        if($httpcode != 200) {
            return [];
        }

        return json_decode($body, true);
	}

}
