<?php

/**
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 * @since 2015-06-28
 * @version 1.2
 */

namespace ninthday\charlotte;

class GooglePlay
{

    private $gp_url = 'https://play.google.com/store/apps/details';
    private $spiderling;
    private $sdom;
    private $package_name;
    private $app_pofile = array();
    private $orig_app = false;
    private $html;

    function __construct($package_name)
    {
        if (!is_string($package_name)) {
            throw new \InvalidArgumentException('package name 一定需為字串！');
        }
        require_once _APP_PATH . 'classes/Spiderling.Class.php';
        require_once _APP_PATH . 'resources/simple_html_dom.php';
        $this->package_name = $package_name;

        $this->orig_app = $this->checkOrigApp($this->package_name);
        $this->initMe();
    }

    /**
     * 取得應用程式的基本資料
     * 包含應用程式名稱、類別、類型、開發公司名稱
     * 
     * @return array
     * @since version 1.0
     * @access public
     */
    public function getAppInformation()
    {
        if (!$this->orig_app) {
            if ($this->checkNotFound()) {
                $ary_category = array(
                    'type' => 'Unknow',
                    'main' => 'Unknow',
                    'google' => 'Unknow'
                );
                $ary_company = array(
                    'name' => 'Unknow',
                    'id' => 'Unknow'
                );
                $this->app_pofile['name'] = 'Unknow';
                $this->app_pofile['company'] = $ary_company;
                $this->app_pofile['category'] = $ary_category;
            } else {
                $this->getAppName();
                $this->getCompanyNameID();
                $this->getCategory();
            }
        }
        return $this->app_pofile;
    }

    /**
     * 取得應用程式名稱
     * 
     * @return string 應用程式名稱
     * @since version 1.0
     * @access public
     */
    public function getAppName()
    {
        if (!$this->orig_app) {
            $result = $this->sdom->find('h1.document-title');
            //如果取到的資料有問題，就重新再取一次
            while (!isset($result[0])) {
                sleep(rand(1, 3));
                $this->html = $this->getHTMLPage();
                $this->sdom->load($this->html);
                $result = $this->sdom->find('h1.document-title');
            }
            $this->app_pofile['name'] = $result[0]->children(0)->innertext;
        }
        return $this->app_pofile['name'];
    }

    /**
     * 取得開發公司名稱與ID
     * 
     * @return array 公司名稱、ID
     * @since version 1.0
     * @access public
     */
    public function getCompanyNameID()
    {
        if (!$this->orig_app) {
            $this->app_pofile['company'] = array();
            foreach ($this->sdom->find('a.document-subtitle') as $a_dom) {
                $ary_href = explode('/', $a_dom->href);
                if ($a_dom->children(0)->itemprop == 'name') {
                    $explode_companylink = explode('=', $ary_href[3]);
                    $this->app_pofile['company']['name'] = $a_dom->children(0)->innertext;
                    $this->app_pofile['company']['id'] = $explode_companylink[1];
                }
            }
        }
        return $this->app_pofile['company'];
    }

    /**
     * 取得應用程式的類別（APP or GAME）與類型
     * 
     * @return array 類別（type），類型（main），Google類別（google）
     * @since version 1.0
     * @access public
     */
    public function getCategory()
    {
        if (!$this->orig_app) {
            $this->app_pofile['category'] = array();
            foreach ($this->sdom->find('a.document-subtitle') as $a_dom) {
                $ary_href = explode('/', $a_dom->href);
                if ($a_dom->children(0)->itemprop == 'genre') {
                    if (preg_match('/^GAME./', $ary_href[4])) {
                        $this->app_pofile['category']['type'] = 'GAME';
                    } else {
                        $this->app_pofile['category']['type'] = 'APP';
                    }
                    $this->app_pofile['category']['main'] = $a_dom->children(0)->innertext;
                    $this->app_pofile['category']['google'] = $ary_href[4];
                }
            }
        }
        return $this->app_pofile['category'];
    }

    /**
     * 初始化設定，如果是原生程式則不引入爬蟲物件節省資源
     * 
     * @since version 1.0
     * @access private
     */
    private function initMe()
    {
        if ($this->orig_app) {
            $ary_category = array(
                'type' => 'APP',
                'main' => '系統原生',
                'google' => 'ANDROID_ORIG'
            );
            $ary_company = array(
                'name' => 'android',
                'id' => '0'
            );
            $this->app_pofile['name'] = str_replace('com.android.', '', $this->package_name);
            $this->app_pofile['company'] = $ary_company;
            $this->app_pofile['category'] = $ary_category;
        } else {
            $this->spiderling = new \ninthday\charlotte\Spiderling();
            $this->sdom = new \simple_html_dom();
            $this->html = $this->getHTMLPage();
            $this->sdom->load($this->html);
        }
    }

    /**
     * 檢查是否為 android 原生的應用程式
     * 
     * @param string $package_name
     * @return boolen
     * @since version 1.0
     * @access private
     */
    private function checkOrigApp($package_name)
    {
        return preg_match('/^com\.android\./', $package_name);
    }

    /**
     * 檢查是不是傳回 Not Found 頁面
     * (2015-12-04) 加入檢查 Unavailable in your country
     * 
     * @return boolen
     * @since version 1.0
     * @access private
     */
    private function checkNotFound()
    {
        $title = $this->sdom->find('title', 0);
        return ($title->innertext == "Not Found" || $title->innertext == "Unavailable in your country");
    }

    /**
     * 取得 Google Play 網頁的 HTML 原始碼
     * 
     * @return string Google Play 網頁的 HTML
     * @since version 1.0
     * @access private
     */
    private function getHTMLPage()
    {
        $this->spiderling->feedURL($this->gp_url);
        $paramfields = array(
            'id' => $this->package_name
        );
        $this->spiderling->spinWeb('get', $paramfields);
        $html = $this->spiderling->wrapPrey();
        return $html;
    }

    function __destruct()
    {
        $this->spiderling = null;
        $this->sdom = null;
        $this->html = null;
        unset($this->html);
        unset($this->sdom);
        unset($this->spiderling);
    }

}
