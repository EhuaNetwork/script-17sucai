<?php

/**
 * getAttribute 获取对象的attr属性
 */

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

//header设置
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class Selenium
{
    static $driver;

    /*
     * 初始化
     * */
    public static function run()
    {
// start Firefox with 5 second timeout
        $waitSeconds = 5;  //需等待加载的时间，一般加载时间在0-15秒，如果超过15秒，报错。
        $host = 'http://localhost:4444/wd/hub'; // this is the default
//这里使用的是chrome浏览器进行测试，需到http://www.seleniumhq.org/download/上下载对应的浏览器测试插件
//我这里下载的是win32 Google Chrome Driver 2.25版：https://chromedriver.storage.googleapis.com/index.html?path=2.25/
        usleep(500000);

        $capabilities = DesiredCapabilities::chrome();

//header头
        $useragent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/53';
        $options = new ChromeOptions();
        $header = [
            "user-agent={$useragent}",
        ];

        $options->addArguments($header);

        //最大化浏览器
//        $options->addArguments(['--start-maximized']);
        //linux设置隐藏浏览器窗口
        //$options->addArguments(['--headless','--no-sandbox']);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        //浏览器设置不加载图片
//        $options = new ChromeOptions();
//        $value = ['profile.managed_default_content_settings.images' => 2];
//        $options->setExperimentalOption('prefs', $value);
//        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);


        //防检测
        $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $options->setExperimentalOption('useAutomationExtension', false);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

//        $capabilities->getCapability(ChromeOptions::CAPABILITY);

//$ip = '27.43.190.168:9999';   //设置代理IP
//$capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome',
//    WebDriverCapabilityType::PROXY => array('proxyType' => 'manual',
//        'httpProxy' => $ip, 'sslProxy' => $ip));


        $driver = RemoteWebDriver::create($host, $capabilities, 500000);
        $driver->manage()->timeouts()->implicitlyWait(2);    //隐性设置15秒
        self::$driver = $driver;
        return $driver;
    }


    /**
     * 请求页面
     * @param $url
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:16
     */
    public static function geturl($url)
    {
        self::$driver->get($url);
    }

    /**
     * 获取标题
     * @return mixed
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:17
     */
    public static function gettitle()
    {
        return self::$driver->getTitle();
    }

    /**
     * 获取html内容
     * @return mixed
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:17
     */
    public static function gethtml()
    {
        return self::$driver->getPageSource();
    }

    /**
     * 获取所有cookie
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:17
     */
    public static function getcookieall()
    {
        return self::$driver->manage()->getCookies();
    }

    /**
     * 获取指定cookie
     * @param $name
     * @return mixed
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:17
     */
    public static function getcookieonly($name)
    {
        return self::$driver->manage()->getCookieNamed($name);
    }

    /**
     * 设置对象
     * @param $driver
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:17
     */
    public static function setdiuver($driver)
    {
        self::$driver = $driver;
    }

    /**
     * 操作等待
     * @param int $time
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:18
     */
    public static function sleep($time = 15)
    {
        self::$driver->manage()->timeouts()->implicitlyWait($time);
    }

    /**
     * 初始窗口句柄
     * @return mixed
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:18
     */
    public static function windowinit()
    {
        //获取当前窗口句柄
        return $windowHandlesBefore = self::$driver->getWindowHandles();
    }

    /**
     * 切换到新窗口
     * @param $windowinit
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:18
     */
    public static function windowto($windowinit, $bool = false)
    {
        $windowHandlesAfter = self::$driver->getWindowHandles();
        $newWindowHandle = array_diff($windowHandlesAfter, $windowinit);
        if ($bool) {
            return $newWindowHandle;
        }
        //前往新句柄
        self::$driver->switchTo()->window(reset($newWindowHandle));
    }

    /**
     * 切换到指定窗口
     * @param $windowinit
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:18
     */
    public static function windowtoonly($newWindowHandle)
    {
//        self::$driver->switchTo()->window(reset($newWindowHandle));
        self::$driver->switchTo()->window($newWindowHandle);

    }


    //简易版登录
    /*  $name=[
            'type'=>'元素类型【id,name,className】',
            'type_name'=>'元素名称',
            'value'=>'输入的值',
        ]; */
    public static function login($name, $pass, $submit)
    {
        self::input($name['type'], $name['type_name'], $name['value']);
        self::input($pass['type'], $pass['type_name'], $pass['value']);
        self::submit($submit['type'], $submit['type_name'], $submit['value']);
    }

    /**
     * 简易登录 带延迟
     * @param $name
     * @param $pass
     * @param $submit
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:24
     */
    public static function login2($name, $pass, $submit)
    {
        $a = $name['value'];
        $b = $pass['value'];
        //分割字符串到数组ret
        for ($i = 0; $i < strlen($a); $i++) {
            usleep(500000);
            self::$driver->findElement(WebDriverBy::$name['type']($name['type_name']))->sendKeys($a[$i]);
        }

        for ($ii = 0; $ii < strlen($b); $ii++) {
            usleep(500000);
            self::$driver->findElement(WebDriverBy::$pass['type']($pass['type_name']))->sendKeys($b[$ii]);
        }
        self::submit($submit['type'], $submit['type_name'], $submit['value']);
    }


    /**
     * 找对象
     * @param $name
     * @param $type_name
     * @param $value
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:22
     */
    public static function input($type, $type_name, $value)
    {
        return self::checkobj($type, $type_name, $value);
    }

    public static function submit($type, $type_name, $value)
    {
        self::checkobj($type, $type_name, $value)->click();
    }


    /**
     * 执行js
     * @param $js
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:25
     */
    public static function jsexec($js)
    {
        self::sleep(20);
        self::$driver->executeScript($js);
    }


    /**
     * 辅助方法 找对象
     * @param $type
     * @param $name
     * @param $value
     * @return mixed
     * @author Ehua(ehua999@163.com)
     * @date 2020/12/25 12:25
     */
    private static function checkobj($type, $name, $value)
    {
        return self::getobj($type, $name)->sendKeys($value);
    }

    public static function getobj($type, $name)
    {
        self::sleep(5);
        switch ($type) {
            case 'name':
                return self::$driver->findElement(WebDriverBy::name($name));
                break;
            case 'id':
                return self::$driver->findElement(WebDriverBy::id($name));
                break;
            case 'className':
                return self::$driver->findElement(WebDriverBy::className($name));
                break;
            case 'linkText':
                return self::$driver->findElement(WebDriverBy::linkText($name));
                break;
            case 'cssSelector':
                return self::$driver->findElement(WebDriverBy::cssSelector($name));
                break;
            case 'tagName':
                return self::$driver->findElement(WebDriverBy::tagName($name));
                break;
        }
    }
    public static function getText($type, $name)
    {
        self::sleep(5);
        switch ($type) {
            case 'name':
                return self::$driver->findElement(WebDriverBy::name($name))->getText();
                break;
            case 'id':
                return self::$driver->findElement(WebDriverBy::id($name))->getText();
                break;
            case 'className':
                return self::$driver->findElement(WebDriverBy::className($name))->getText();
                break;
            case 'linkText':
                return self::$driver->findElement(WebDriverBy::linkText($name))->getText();
                break;
            case 'cssSelector':
                return self::$driver->findElement(WebDriverBy::cssSelector($name))->getText();
                break;
            case 'tagName':
                return self::$driver->findElement(WebDriverBy::tagName($name))->getText();
                break;
        }
    }


    public static function close()
    {
        self::$driver->close();
    }

    public static function quit()
    {
        self::$driver->quit();
    }

    public static function screenshot($filename = '1.png')
    {
        self::$driver->get_screenshot_as_file($filename);
    }
}