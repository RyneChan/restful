<?php

namespace Quguo;
use think\Controller;
use think\Request;
use think\Response;
use think\Config;
class Base extends Controller
{

    protected $request;
    protected $params;

    public function _initialize()
    {
        parent::_initialize();
    }

    public function __construct()
    {
        parent::__construct();
        $this->route = Request::instance();
		$this->params = $this->route->param();
    }

    public function sendSuccess($data = [], $msg = '操作成功', $code = 0)
    {
        $data = [
            'status' => 1,
            'data'   => $data,
            'msg'    => $msg,
            'code'   => $code
        ];

        return $this->response($data);
    }

    public function sendError($data = [], $msg = '操作失败',  $code = 0)
    {
        $data = [
            'status' => 0,
            'data'   => $data,
            'msg'    => $msg,
            'code'   => $code
        ];

        return $this->response($data);
    }

    /**
     * 输出返回数据
     * @access protected
     * @param mixed     $data 要返回的数据
     * @param String    $type 返回类型 JSON XML
     * @param integer   $code HTTP状态码
     * @return Response
     */
    protected function response($data, $type = 'json', $code = 200, $header = [], $options = [])
    {
        return Response::create($data, $type, $code = 200, $header, $options)->code($code);
    }

}