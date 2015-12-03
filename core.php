<?php

/**
 * Class CheckUrls
 */
class CheckUrls /*extends Thread*/
{

    public $url;

    /**
     * Timeout open page (in MS)
     * @var int
     */
    public $timeout = 1000;

    protected $cleanUrl;
    protected $insideUrls = array();
    protected $checkedUrls = array();

    public function __construct($url)
    {
        echo "url; status; location\r\n";
        $this->url = $url;
        $this->cleanUrl = trim($this->url, '/');
    }

    public function run()
    {
        $this->checkUrl($this->url);
    }

    /**
     * Проверить URL и все внутренние ссылки
     * @param $url
     */
    public function checkUrl($url, $findInsideUrls = true)
    {
        if (!empty($this->checkedUrls[$url])) {
            return;
        }

        $this->checkedUrls[$url] = 1;
        $content = $this->getPageData($url);
        echo "$url; $content[status]; $content[location]\r\n";

        //Если есть редирект
        if ($content['location']) {
            $this->checkUrl($content['location'], false);
        } //Поиск внутренних ссылок
        elseif ($content['status'] < 300 && $findInsideUrls) {
            $insideUrls = $this->getPageInsideUrls($url, $content['body']);
            foreach ($insideUrls as $insideUrl) {
                $this->checkUrl($insideUrl);
            }
        }
    }

    /**
     * Получить данные страницы
     * @param $url
     * @return array (body, status, location)
     */
    public function getPageData($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36'
        curl_setopt($ch, CURLOPT_USERAGENT, 'CheckUrl Scanner');
        if ($this->timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        }

        $result = array(
            'body'     => @curl_exec($ch),
            'status'   => '',
            'location' => '',
        );

        if ($result['body']) {
            //Status
            preg_match('#\AHTTP/[0-9].[0-9] ([0-9]+)\s#Uim', $result['body'], $match);
            $result['status'] = !empty($match[1]) ? (int)$match[1] : '';

            //Location
            preg_match('#^Location:\s(.*)$#Umi', $result['body'], $match);
            $result['location'] = !empty($match[1]) ? trim($match[1]) : '';
        }

        return $result;
    }

    /**
     * Получить список внутренних урлов на странице
     * @param $pageUrl
     * @param $content
     * @return array
     */
    public function getPageInsideUrls($pageUrl, $content)
    {
        $result = array();
        preg_match_all('#<a(?:[^>]*)href=("|\')((?!\#)[a-z/].*)(?!\\\)\\1#Umi', $content, $match);
        if (!empty($match[2])) {
            //Validate urls
            foreach ($match[2] as $item) {
                if ($item[0] === '/') {
                    $item = $this->cleanUrl . $item;
                } elseif (strpos($item, ':') === false) {
                    $item = $pageUrl . $item;
                } elseif (strpos($item, $this->cleanUrl) === false) {
                    continue;
                }

                $result[] = $item;
            }
        }

        return $result;
    }
}

function d($d, $e = 1)
{
    print_r($d);
    if ($e) {
        exit;
    }
}