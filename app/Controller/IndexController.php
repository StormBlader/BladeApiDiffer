<?php
namespace App\Controller;
use App\Model\EnvConfig;

class IndexController extends BaseController 
{
    public function index() 
    {
        $config = EnvConfig::first();
		$this->assign('config', $config);
		$this->display('View/index.php');
	}

    public function saveConfig()
    {
        $master_protocol = $this->getRequest('master_protocol', 'http');
        $master_ip       = $this->getRequest('master_ip');
        $master_host     = $this->getRequest('master_host', '');

        $test_protocol   = $this->getRequest('test_protocol', 'http');
        $test_ip         = $this->getRequest('test_ip');
        $test_host       = $this->getRequest('test_host', '');

        if(empty($master_ip) || empty($test_ip)) {
            return $this->errorResponse('不能为空');
        }

        list($ret_master_ip, $ret_master_port) = $this->_getIpAndPort($master_ip);
        list($ret_test_ip, $ret_test_port) = $this->_getIpAndPort($test_ip);

        $config = EnvConfig::first();
        if(is_null($config)) {
            $config = new EnvConfig();
        }
        $config->master_protocol = $master_protocol;
        $config->master_host     = $master_host;
        $config->master_ip       = $ret_master_ip;
        $config->master_port     = $ret_master_port;
        $config->test_protocol   = $test_protocol;
        $config->test_host       = $test_host;
        $config->test_ip         = $ret_test_ip;
        $config->test_port       = $ret_test_port;
        $ret = $config->save();

        if($ret) {
            return $this->successResponse();
        }

        return $this->errorResponse();
    }

    private function _getIpAndPort($ip)
    {
        $ret_ip = $ip;
        $ret_port = 80;

        if(strpos($ip, ':') !== false) {
            $arr = explode(':', $ip);
            $ret_ip   = $arr[0];
            $ret_port = $arr[1];
        }

        return [
            $ret_ip,
            $ret_port,
        ];
    }

    public function requestUri()
    {
        $method       = $this->getRequest('requestMethod', 'GET');
        $uri          = $this->getRequest('requestUri');
        $param_keys   = $this->getRequest('paramKeys');
        $param_values = $this->getRequest('paramValues');

        $params = [];
        for($i = 0; $i < count($param_keys); $i++) {
            $params[$param_keys[$i]] = isset($param_values[$i]) ? $param_values[$i] : '';
        }

    }

}
?>
