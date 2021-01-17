<?php

namespace Facebook\WebDriver;

use mysql_xdevapi\Exception;
use QL\QueryList;
use SimpleDB\DB;

require_once "autoload.php";
require_once "Selenium.php";


caiji::run($_GET['page']);

class caiji
{
    public static $data;//采集数组

    public static function run($page=1)
    {
echo '<pre>';
//初始化
        \Selenium::run();
//打开网页
        \Selenium::geturl("http://www.htmlsucai.com/forum-78-{$page}.html");
//点击登录按钮
        \Selenium::submit('className', 'huyoudlu', 'xxx');

//等待
        \Selenium::sleep(15);

//输入密码登录
        $name = [
            'type' => 'name',
            'type_name' => 'username',
            'value' => 'qq_江南_Qp2',
        ];
        $pass = [
            'type' => 'name',
            'type_name' => 'password',
            'value' => '150638',
        ];
        $submit = [
            'type' => 'name',
            'type_name' => 'loginsubmit',
            'value' => 'xxx',
        ];
        \Selenium::login($name, $pass, $submit);
        sleep(3);


//登录完成后操作

        for ($i = 2; $i < 42; $i++) {
            try {
                self::caijilist($i);
            }catch (\Exception $exception){
                var_dump($exception->getMessage());
            }
        }

        \Selenium::close();
        $page++;
        echo "<script>
        window.location.href=\"/Demo/caiji17sucai/caiji.php?page={$page}\";
</script>";

    }

    /**列表采集
     * @param $i
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 14:02
     */

    public static function caijilist($i)
    {
        $data = [];
        \Selenium::windowtoonly(\Selenium::windowinit()[0]);
//用JS获取对象进行操作
        $js = <<<js
       document.getElementsByClassName('z')[$i].click();
js;
        \Selenium::jsexec($js);
        \Selenium::sleep(30);

//----------------窗口2------------------------------------------------
        \Selenium::windowtoonly(\Selenium::windowinit()[1]);//切换窗口2  文章详情

        //获取标题

        $html = \phpQuery::newDocument(\Selenium::gethtml());
        $data['title'] = pq("#thread_subject")->text();
        preg_match("/\[.*?\]/", pq(".ts")->text(), $type);
        $data['type'] = @$type[0];

        //获取下载地址
        $obj = \Selenium::getobj('id', 'formdownload');
        $data['Down_url'] = $obj->getAttribute('href');

        //获取下载金额
        $obj = \Selenium::getobj('className', 'centerd');
        preg_match("/\d+/", $obj->getText(), $money);
        $data['money'] = @$money[0];


        $data['body'] = pq(".t_fsz")->find('tbody')->html();
        $data['img'] = pq(".t_fsz")->find('tbody')->find('img')->attr('src');

        \Selenium::submit('id', 'formyulan', 'xxx');//
        \Selenium::close();
        \Selenium::windowtoonly(\Selenium::windowinit()[1]);
//----------------窗口3-------------------------------------------
        \Selenium::sleep(30);

        $obj = \Selenium::getobj('id', 'iframe');
        $data['Demo_url'] = $obj->getAttribute('src');


        \Selenium::close();
        \Selenium::windowtoonly(\Selenium::windowinit()[0]);//切换窗口3  实例站点
//-----------------------------------------------------------------
//        self::$data[]=$data;

        $db = new DB();
        $db->setup();
        try {
            $res = $db->insert('data2', $data);
        }catch (\Exception $exception){
            var_dump($exception->getMessage());
        }
    }
}
