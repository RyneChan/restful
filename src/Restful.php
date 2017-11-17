<?php

namespace Quguo;
use think\Request;
use think\Config;
use RestAuth\Base;
use Firebase\JWT\JWT;
class Restful extends Base
{
    
    protected $_allowMethods = [];

    protected $config = [
        'app_id' => 'app_id',
        'app_secret' => 'app_secret',
        'expires' => 7200,
        'origin' => '*', // 跨域域名(*所有)
        'methods' => 'POST,GET,OPTIONS,PUT,DELETE',
        'headers' => 'Origin, X-Requested-With, Content-Type, Accept'
    ];
    
    public function __construct()
    {
        parent::__construct();
        if (Config::get('resetful')) {
            $this->config = array_merge($this->config, Config::get('resetful'));
        };

        $this->init();
    }

    public function init()
    {
        $this->setOrigin();
        $request = Request::instance();
        $this->request = $request;
        $this->params = $this->request->param();
    }

    /**
     * 设置相关header
     */
    public function setOrigin()
    {
        $origin = $this->config['origin'];
        $methods = $this->config['methods'];
        $headers = $this->config['headers'];

        header("Access-Control-Allow-Origin:$origin");
        header("Access-Control-Allow-Methods:$methods");
        header("Access-Control-Allow-Headers:$headers");
    }

    /**
     * 验证
     */
    public function validateRule()
    {
        if (!\in_array($this->request->action(), $this->_allowMethods)) {
            $Authorization = $this->request->header('authorization');
            try {
                $data = JWT::decode($Authorization, $this->config['app_secret'], ['HS256']);
                // 注入路由
                request()->user = $data;
            } catch(\Exception $e) {
                $response = $this->sendError('验证失败');
                exit($response->send());
            }
        }
    }

    /**
     * 允许通过
     */
    public function setAllowMethods($method = [])
    {
        $this->_allowMethods = $method;
    }

    /**
     * 获取签名
     */
    public function makeSign($data = [])
    {
        if (empty($data)) {
            throw new \Exception("params can not be empty");
        }
        
        $exp = $this->config['expires'];
        $data['iat'] = time();        // 发布时间
        $data['exp'] = time() + $exp; // 过期时间
        return Jwt::encode($data, $this->config['app_secret']);     
    }

}