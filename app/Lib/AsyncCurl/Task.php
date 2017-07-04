<?php
namespace App\Lib\AsyncCurl;


class Task implements TaskInterface
{
    protected $method;

    protected $url;

    protected $header = [];

    protected $proxy_ip = null;

    protected $proxy_port = null;

    protected $timeout = 10;

    protected $transfer_timeout = 600;

    protected $params = null;

    protected $ch = null;

    const METHOD_POST   = "post";
    const METHOD_GET    = "get";
    const METHOD_PUT    = "put";
    const METHOD_DELETE = "delete";

    /**
     * @param string $method
     * @param $url
     * @param null $params
     * @param int $timeout
     * @param int $transfer_timeout
     */
    public function __construct($method = Task::METHOD_GET, $url, $params = null, $header = [], $timeout = 10, $transfer_timeout = 600)
    {
        $this->method  = $method;
        $this->url     = $url;
        $this->header  = $header;
        $this->params  = $params;
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param $host
     * @param $port
     */
    public function setProxy($host, $port)
    {
        $this->proxy_ip = $host;
        $this->proxy_port = $port;
    }

    /**
     * @param int $timeout
     * @param $transfer_timeout
     */
    public function setTimeout($timeout = 10, $transfer_timeout)
    {
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param int $transfer_timeout
     */
    public function setTransferTimeout($transfer_timeout = 600)
    {
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param null $params
     */
    public function setParams($params = null)
    {
        $this->params = $params;
    }

    /**
     * get curl resource
     * @return resource curl
     */
    public function getCurl()
    {
        return $this->ch;
    }

    public function createCurl()
    {
        $this->ch = curl_init();

        if ($this->ch === false) {
            throw new \RuntimeException("init curl failed");
        }

        if (!is_null($this->proxy_ip) && !is_null($this->proxy_port)) {
            $proxy = "http://{$this->proxy_ip}:{$this->proxy_port}";
            curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
        }

        $url = $this->url;
        switch($this->method) {
        case self::METHOD_POST :
            curl_setopt($this->ch, CURLOPT_POST, 1);
            if (is_array($this->params) || is_object($this->params)) {
                $post_field = http_build_query($this->params);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_field);
            } else {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->params);
            }
            break;
        case self::METHOD_PUT:
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			if (!empty($this->params)) {
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->params));
			}
            break;
        case self::METHOD_DELETE:
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			if (!empty($this->params)) {
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->params));
			}
            break;
        default:
			curl_setopt($this->ch, CURLOPT_POST, FALSE);
			if (! empty($this->params)) {
				$url = $url . "?" . http_build_query($this->params);
			}
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        if(!empty($this->header)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
        }
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->transfer_timeout);

        return $this->ch;
    }

}
