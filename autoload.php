<?php
require_once "vendor/autoload.php";
require_once "extends/guzz/guzz.php";
require_once "extends/phpQuery/phpQuery.php";

use QL\QueryList;
use QL\Ext\PhantomJs;

function dd($data)
{
    var_dump($data);
    if (!empty($GLOBALS['driver'])) {
        $GLOBALS['driver']->quit();
    }
    die;
}

function runguzz($uel)
{
    $ql = QueryList::getInstance();
    $ql->use(PhantomJs::class, 'F:\wwwroot\Demo\caiji17sucai\extends\phantomjs-2.1.1-windows\phantomjs-2.1.1-windows\bin\phantomjs.exe');
    $html = $ql->browser($uel, true, ['--cookies-file' => 'cookie.txt'])->getHtml();
    return $html;
}

function getlist($list)
{
    foreach ($list as $k => $y) {
        $info[$k]['url'] = pq($y)->find('.huyouddinfo')->find('a')->attr('href');
        $info[$k]['title'] = pq($y)->find('.huyouddinfo')->find('a')->attr('title');
        $info[$k]['img'] = pq($y)->find('.huyouddimg')->find('a')->find('img')->attr('src');
        return $info;
    }
//    return $info;

}