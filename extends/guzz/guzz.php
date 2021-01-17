<?php

namespace guzz;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class  guzz
{
    /**
     * @param null $url
     * @param array $field
     * @param string $mothod
     * @param array $header
     * @param null $proxy
     * @param int $outtime
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Ehua(ehua999@163.com)
     * @date 2020/9/21 12:34
     */
    static function guzz($url = null, $field = null, $mothod = 'get', $header = null, $proxy = null, $outtime = 15)
    {
        if (is_array($field)) {
            $type = 'form_params';
        } else {
            $field = json_decode($field);
            $type = 'json';
        }

        $client = new \GuzzleHttp\Client(); //初始化客户端
        try {
            if ($mothod == 'get' || $mothod == 'GET') {
                $response = $client->get($url, [
                    'query' => $field,//设置参数
                    'header' => $header,//设置请求头
                    'timeout' => $outtime, //设置请求超时时间
                    'verify' => false,//不验证ssl
                    'proxy' => $proxy,//代理ip
                ]);
            } else {
                $response = $client->request('POST', $url, [
                    $type => $field,
                    'header' => $header,
                    'timeout' => $outtime,
                    'verify' => false,
                    'proxy' => $proxy,
                ]);
            }

        } catch (ClientException $e) {
            throw new Exception('请求异常');
        } catch (RequestException $e) {
            throw new Exception('请求异常');
        }

        $body = $response->getBody(); //获取响应体，对象
        $bodyStr = (string)$body; //对象转字串,这就是请求返回的结果
        return $bodyStr;
    }


    /**获取 post 参数; 在 content_type 为 application/json 时，自动解析 json
     * @return array
     * @author Ehua(ehua999@163.com)
     * @date 2020/9/23 18:01
     */
    function initPostData()
    {
        if (empty($_POST) && false !== strpos(@$_SERVER["CONTENT_TYPE"], 'application/json')) {
            $content = file_get_contents('php://input');
            $post = (array)json_decode($content, true);
        } else {
            $post = $_POST;
        }
        return $post;
    }


}