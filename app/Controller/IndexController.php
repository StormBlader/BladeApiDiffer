<?php
namespace App\Controller;
use App\Model\EnvConfig;
use App\Component\Curl;
use App\Lib\AsyncCurl\Task;
use App\Lib\AsyncCurl\Async;

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

        $_SESSION['uri']          = $uri;
        $_SESSION['method']       = $method;

        $params = [];
        for($i = 0; $i < count($param_keys); $i++) {
            $params[$param_keys[$i]] = isset($param_values[$i]) ? $param_values[$i] : '';
        }
        if(!empty($params)) {
            $_SESSION['params']   = json_encode($params);
        }

        $config = EnvConfig::first();
        if(is_null($config)) {
            return $this->errorResponse();
        }

        /**
        $master_header = empty($config['master_host']) ? [] : ['Host: '. $config['master_host']];
        $master_url = sprintf("%s://%s:%d%s", strtolower($config['master_protocol']), $config['master_ip'], $config['master_port'], $uri);
        $master_before_time = $this->_getNowMillisecond();
        $master_ret = Curl::getInstance()->curl($master_url, $method, $params, $master_header);
        $master_after_time = $this->_getNowMillisecond();

        $test_header = empty($config['test_host']) ? [] : ['Host: '. $config['test_host']];
        $test_url = sprintf("%s://%s:%d%s", strtolower($config['test_protocol']), $config['test_ip'], $config['test_port'], $uri);
        $test_before_time = $this->_getNowMillisecond();
        $test_ret = Curl::getInstance()->curl($test_url, $method, $params, $test_header);
        $test_after_time = $this->_getNowMillisecond();


        $data = [
        ];
        **/

        $master_count = $this->getRequest('master_count', 1);
        $master_async = new Async();
        $master_header = empty($config['master_host']) ? [] : ['Host: '. $config['master_host']];
        $master_url = sprintf("%s://%s:%d%s", strtolower($config['master_protocol']), $config['master_ip'], $config['master_port'], $uri);

        for($i = 0 ;$i < $master_count; $i ++) {
            $master_task = new Task($method, $master_url, $params, $master_header);
            $master_async->attach($master_task, "master$i");
        }
        $master_ret = $master_async->execute(true);

        $master_total_consume = 0;
        $master_response = [];
        for($i = 0; $i < $master_count; $i ++) {
            $master_total_consume += $master_ret["master$i"]['info']['total_time'];
            if(empty($master_ret["master$i"]['error'])) {
                $master_response = $master_ret["master$i"]['content'];
            }
        }
        $master_avg_consume = $master_total_consume/$master_count;

        $test_count = $this->getRequest('test_count', 1);
        $test_async = new Async();
        $test_header = empty($config['test_host']) ? [] : ['Host: '. $config['test_host']];
        $test_url = sprintf("%s://%s:%d%s", strtolower($config['test_protocol']), $config['test_ip'], $config['test_port'], $uri);

        for($i = 0; $i < $test_count; $i ++) {
            $test_task   = new Task($method, $test_url, $params, $test_header);
            $test_async->attach($test_task, "test$i");
        }
        $test_ret = $test_async->execute(true);
        $test_total_consume = 0;
        $test_response = [];
        for($i = 0; $i < $test_count; $i ++) {
            $test_total_consume += $test_ret["test$i"]['info']['total_time'];
            if(empty($test_ret["test$i"]['error'])) {
                $test_response = $test_ret["test$i"]['content'];
            }
        }
        $test_avg_consume = $test_total_consume/$test_count;

        $data = [
            'test_ret'             => json_decode($test_response, true),
            'test_total_consume'   => $test_total_consume,
            'test_avg_consume'     => $test_avg_consume,
            'master_ret'           => json_decode($master_response, true),
            'master_total_consume' => $master_total_consume,
            'master_avg_consume'   => $master_avg_consume,
        ];

        return $this->successResponse($data);
    }
}
?>
