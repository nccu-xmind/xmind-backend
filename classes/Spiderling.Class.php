<?php

/**
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 * @since 2015-06-26
 * @version v.1.0
 */
namespace ninthday\charlotte;
class Spiderling
{

    private $url;
    private $useragent = "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0";
    private $ch;
    private $timeout = 60;

    function __construct()
    {
        $this->ch = curl_init();
    }

    public function feedURL($url)
    {
        if (preg_match('/^(http|https|ftp|sftp)\:\/\/[a-zA-Z0-9\-\.]+\.\w{2,3}(\/\w*)?/', $url)) {
            $this->url = $url;
        } else {
            throw new \InvalidArgumentException('輸入的參數不是網址字串！');
        }
    }

    public function spinWeb($method, array $fields)
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException('method 參數一定需為字串！');
        }
        if ($method == 'post') {
            curl_setopt($this->ch, CURLOPT_POST, true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($this->ch, CURLOPT_URL, $this->url);
        } elseif ($method == 'get') {
            $ary_params = array();
            foreach ($fields as $key=>$value){
                array_push($ary_params, $key.'='.$value);
            }
            $params = implode('&', $ary_params);
            curl_setopt($this->ch, CURLOPT_URL, $this->url . '?' . $params);
        }
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
    }

    public function wrapPrey()
    {
        $html = curl_exec($this->ch);
        if (curl_errno($this->ch)){
            throw new \RuntimeException('Curl error: ' . curl_error($this->ch));
        }
        return $html;
    }

    public function callWait($timeout)
    {
        if (is_numeric($timeout)) {
            $this->timeout = $timeout;
        } else {
            throw new \InvalidArgumentException('時間參數必須是數字！');
        }
    }

    function __destruct()
    {
        curl_close($this->ch);
    }

}
