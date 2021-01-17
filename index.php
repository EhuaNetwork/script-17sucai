<?php
header('content-type:text/html;charset=utf-8');
// 设置脚本超时
set_time_limit(0);
// 内存限制
ini_set('memory_limit', '2014M');
require_once "autoload.php";
use QL\QueryList;
use QL\Ext\PhantomJs;


$uel="http://www.htmlsucai.com/forum.php?mod=forumdisplay&fid=76&filter=typeid&typeid=9";

// 获取搜索结果
$html=runguzz($uel);
phpQuery::selectDocument(\phpQuery::newDocument($html));
dd(1);


define("DOMAIN",'http://www.htmlsucai.com/');
$type='导航菜单';
$list=pq('#waterfall')->find('li');
dd($list);
//获取列表
$info=getlist($list);

//循环获取内容
foreach ($info as $k=>$y){
    $html=runguzz($y['url']);
    file_put_contents('1.txt',$html);
    phpQuery::selectDocument(\phpQuery::newDocument($html));
//    $info[$k]['body']=pq('.t_fsz')->find('table')->html();
//    $info[$k]['demo-url']=DOMAIN.pq('#formyulan')->attr('href');
    $info[$k]['down-url']= pq("a:contains('附件下载')")->attr('href');
    dd($info);
}



//utf16to8





function runguzz($uel){
    $ql = QueryList::getInstance();
    $ql->use(PhantomJs::class,'F:\wwwroot\Demo\caiji17sucai\extends\phantomjs-2.1.1-windows\phantomjs-2.1.1-windows\bin\phantomjs.exe');
    $html = $ql->browser($uel,true,['--cookies-file'=>'cookie.txt'])->getHtml();
    return $html;
}

function getlist($list){
    foreach ($list as $k=>$y){
        $info[$k]['url']=pq($y)->find('.huyouddinfo')->find('a')->attr('href');
        $info[$k]['title']=pq($y)->find('.huyouddinfo')->find('a')->attr('title');
        $info[$k]['img']=pq($y)->find('.huyouddimg')->find('a')->find('img')->attr('src');
        return $info;

    }
//    return $info;

}




