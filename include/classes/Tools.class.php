<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tools {

    protected static $file_exists_cache = array();
    protected static $_forceCompile;
    protected static $_caching;

    /**
     * Random password generator
     *
     * @param integer $length Desired length (optional)
     * @param string $flag Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC)
     * @return string Password
     */
    public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC') {
        switch ($flag) {
            case 'NUMERIC':
                $str = '0123456789';
                break;
            case 'NO_NUMERIC':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        for ($i = 0, $passwd = ''; $i < $length; $i++)
            $passwd .= Tools::substr($str, mt_rand(0, Tools::strlen($str) - 1), 1);
        return $passwd;
    }

    public static function strReplaceFirst($search, $replace, $subject, $cur = 0) {
        return (strpos($subject, $search, $cur)) ? substr_replace($subject, $replace, (int) strpos($subject, $search, $cur), strlen($search)) : $subject;
    }

    /**
     * Redirect user to another page
     *
     * @param string $url Desired URL
     * @param string $baseUri Base URI (optional)
     * @param Link $link
     * @param string|array $headers A list of headers to send before redirection
     */
    public static function redirect($url, $base_uri = __PS_BASE_URI__, Link $link = null, $headers = null) {
        if (!$link)
            $link = Context::getContext()->link;

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link) {
            if (strpos($url, $base_uri) === 0)
                $url = substr($url, strlen($base_uri));
            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0) {
                $url = substr($url, strlen('index.php?controller='));
                if (Configuration::get('PS_REWRITING_SETTINGS'))
                    $url = Tools::strReplaceFirst('&', '?', $url);
            }

            $explode = explode('?', $url);
            // don't use ssl if url is home page
            // used when logout for example
            $use_ssl = !empty($url);
            $url = $link->getPageLink($explode[0], $use_ssl);
            if (isset($explode[1]))
                $url .= '?' . $explode[1];
        }

        // Send additional headers
        if ($headers) {
            if (!is_array($headers))
                $headers = array($headers);

            foreach ($headers as $header)
                header($header);
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect url wich allready PS_BASE_URI
     *
     * @param string $url Desired URL
     */
    public static function redirectLink($url) {
        if (!preg_match('@^https?://@i', $url)) {
            if (strpos($url, __PS_BASE_URI__) !== false && strpos($url, __PS_BASE_URI__) == 0)
                $url = substr($url, strlen(__PS_BASE_URI__));
            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0)
                $url = substr($url, strlen('index.php?controller='));
            $explode = explode('?', $url);
            $url = Context::getContext()->link->getPageLink($explode[0]);
            if (isset($explode[1]))
                $url .= '?' . $explode[1];
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect user to another admin page
     *
     * @param string $url Desired URL
     */
    public static function redirectAdmin($url) {
        header('Location: ' . $url);
        exit;
    }

    /**
     * getSiteProtocol return the available protocol for the current shop in use
     * SSL if Configuration is set on and available for the server
     * @static
     * @return String
     */
    public static function getSiteProtocol() {
        $protocol = ((!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
        return $protocol;
    }

    /**
     * getProtocol return the set protocol according to configuration (http[s])
     * @param bool $use_ssl true if require ssl
     * @return String (http|https)
     */
    public static function getProtocol($use_ssl = null) {
        return (!is_null($use_ssl) && $use_ssl ? 'https://' : 'http://');
    }

    /**
     * getHttpHost return the <b>current</b> host used, with the protocol (http or https) if $http is true
     * This function should not be used to choose http or https domain name.
     * Use Tools::getSiteDomain() or Tools::getSiteDomainSsl instead
     *
     * @param boolean $http
     * @param boolean $entities
     * @return string host
     */
    public static function getHttpHost($http = false, $entities = false, $ignore_port = false) {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($ignore_port && $pos = strpos($host, ':'))
            $host = substr($host, 0, $pos);
        if ($entities)
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        if ($http)
            $host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $host;
        return $host;
    }

    /**
     * getSiteDomain returns domain name according to configuration and ignoring ssl
     *
     * @param boolean $http if true, return domain name with protocol
     * @param boolean $entities if true,
     * @return string domain
     */
    public static function getSiteDomain($http = false, $entities = false) {
            $domain = Tools::getHttpHost();
        if ($entities)
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        if ($http)
            $domain = 'http://' . $domain;
        return $domain;
    }

    /**
     * getSiteDomainSsl returns domain name according to configuration and depending on ssl activation
     *
     * @param boolean $http if true, return domain name with protocol
     * @param boolean $entities if true,
     * @return string domain
     */
    public static function getSiteDomainSsl($http = false, $entities = false) {
            $domain = Tools::getHttpHost();
        if ($entities)
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        if ($http)
            $domain = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https://' : 'http://') . $domain;
        return $domain;
    }

    /**
     * Get the server variable SERVER_NAME
     *
     * @return string server name
     */
    public static function getServerName() {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy)
     *
     * @return string $remote_addr ip of client
     */
    public static function getRemoteAddr() {
        // This condition is necessary when using CDN, don't remove it.
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                return $ips[0];
            } else
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check if the current page use SSL connection on not
     *
     * @return bool uses SSL
     */
    public static function usingSecureMode() {
        if (isset($_SERVER['HTTPS']))
            return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
        // $_SERVER['SSL'] exists only in some specific configuration
        if (isset($_SERVER['SSL']))
            return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');

        return false;
    }

    /**
     * Get the current url prefix protocol (https/http)
     *
     * @return string protocol
     */
    public static function getCurrentUrlProtocolPrefix() {
        if (Tools::usingSecureMode())
            return 'https://';
        else
            return 'http://';
    }

    /**
     * Secure an URL referrer
     *
     * @param string $referrer URL referrer
     * @return string secured referrer
     */
    public static function secureReferrer($referrer) {
        if (preg_match('/^http[s]?:\/\/' . Tools::getServerName() . '(:' . _PS_SSL_PORT_ . ')?\/.*$/Ui', $referrer))
            return $referrer;
        return __PS_BASE_URI__;
    }

    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value
     *
     * @param string $key Value key
     * @param mixed $default_value (optional)
     * @return mixed Value
     */
    public static function getValue($key, $default_value = false) {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($ret) === true)
            $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
        return !is_string($ret) ? $ret : stripslashes($ret);
    }

    public static function getIsset($key) {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;
        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * Change language in cookie while clicking on a flag
     *
     * @return string iso code
     */
    public static function setCookieLanguage($cookie = null) {
        if (!$cookie)
            $cookie = Context::getContext()->cookie;
        /* If language does not exist or is disabled, erase it */
        if ($cookie->id_lang) {
            //echo $cookie->id_lang;exit;
            $lang = new Language((int) $cookie->id_lang);
            if (!Validate::isLoadedObject($lang) || !$lang->active || !$lang->isAssociatedToSite())
                $cookie->id_lang = null;
        }

        /* Automatically detect language if not already defined */
        if (!$cookie->id_lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $array = explode(',', Tools::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            if (Tools::strlen($array[0]) > 2) {
                $tab = explode('-', $array[0]);
                $string = $tab[0];
            } else
                $string = $array[0];
            if (Validate::isLanguageIsoCode($string)) {
                $lang = new Language(Language::getIdByIso($string));
                if (Validate::isLoadedObject($lang) && $lang->active)
                    $cookie->id_lang = (int) $lang->id;
            }
        }

        /* If language file not present, you must use default language file */
        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang))
            $cookie->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $iso = Language::getIsoById((int) $cookie->id_lang);
        @include_once(_PS_THEME_DIR_ . 'lang/' . $iso . '.php');

        return $iso;
    }

    /**
     * Set cookie id_lang
     */
    public static function switchLanguage(Context $context = null) {
        if (!$context)
            $context = Context::getContext();

        // Install call the dispatcher and so the switchLanguage
        // Stop this method by checking the cookie
        if (!isset($context->cookie))
            return;

        if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && ($id_lang = (int) Language::getIdByIso($iso)))
            $_GET['id_lang'] = $id_lang;

        // update language only if new id is different from old id
        // or if default language changed
        $cookie_id_lang = $context->cookie->id_lang;
        $configuration_id_lang = Configuration::get('PS_LANG_DEFAULT');
        if ((($id_lang = (int) Tools::getValue('id_lang')) && Validate::isUnsignedId($id_lang) && $cookie_id_lang != (int) $id_lang) || (($id_lang == $configuration_id_lang) && Validate::isUnsignedId($id_lang) && $id_lang != $cookie_id_lang)) {
            $context->cookie->id_lang = $id_lang;
            $language = new Language($id_lang);
            if (Validate::isLoadedObject($language))
                $context->language = $language;

            $params = $_GET;
            if (Configuration::get('PS_REWRITING_SETTINGS') || !Language::isMultiLanguageActivated())
                unset($params['id_lang']);
        }
    }

    /**
     * Display date regarding to language preferences
     *
     * @param array $params Date, format...
     * @param object $smarty Smarty object for language preferences
     * @return string Date
     */
    public static function dateFormat($params, &$smarty) {
        return Tools::displayDate($params['date'], Context::getContext()->language->id, (isset($params['full']) ? $params['full'] : false), (isset($params['separator']) ? $params['separator'] : '-'));
    }

    /**
     * Display date regarding to language preferences
     *
     * @param string $date Date to display format UNIX
     * @param integer $id_lang Language id
     * @param boolean $full With time or not (optional)
     * @param string $separator DEPRECATED
     * @return string Date
     */
    public static function displayDate($date, $id_lang, $full = false, $separator = '-') {
        if (!$date || !($time = strtotime($date)))
            return $date;

        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00')
            return '';

        if (!Validate::isDate($date) || !Validate::isBool($full))
            throw new Exception('Invalid date');

        $context = Context::getContext();
        $date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);
        /*
          if(!$full && $separator) {
          $date_format_new = explode( "/" , $date_format);
          if(count($date_format_new)==3)
          $date_format = $date_format_new[0].$separator.$date_format_new[1].$separator.$date_format_new[2];
          }
         * */
        return date($date_format, $time);
    }

    /**
     * Sanitize a string
     *
     * @param string $string String to sanitize
     * @param boolean $full String contains HTML or not (optional)
     * @return string Sanitized string
     */
    public static function safeOutput($string, $html = false) {
        if (!$html)
            $string = strip_tags($string);
        return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
    }

    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES) {
        if (is_array($string))
            return array_map(array('Tools', 'htmlentitiesUTF8'), $string);
        return htmlentities($string, $type, 'utf-8');
    }

    public static function htmlentitiesDecodeUTF8($string) {
        if (is_array($string))
            return array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $string);
        return html_entity_decode($string, ENT_QUOTES, 'utf-8');
    }

    public static function safePostVars() {
        if (!is_array($_POST))
            return array();
        $_POST = array_map(array('Tools', 'htmlentitiesUTF8'), $_POST);
    }

    /**
     * Delete directory and subdirectories
     *
     * @param string $dirname Directory name
     */
    public static function deleteDirectory($dirname, $delete_self = true) {
        $dirname = rtrim($dirname, '/') . '/';
        if ($files = scandir($dirname)) {
            foreach ($files as $file)
                if ($file != '.' && $file != '..' && $file != '.svn') {
                    if (is_dir($dirname . $file))
                        Tools::deleteDirectory($dirname . $file, true);
                    elseif (file_exists($dirname . $file))
                        unlink($dirname . $file);
                }
            if ($delete_self)
                rmdir($dirname);
        }
    }

    /**
     * Display an error according to an error code
     *
     * @param string $string Error message
     * @param boolean $htmlentities By default at true for parsing error message with htmlentities
     */
    public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null) {
        global $_ERRORS;

        if (defined('_MODE_DEV_') && _PS_MODE_DEV_ && $string == 'Fatal error')
            return ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
        if (!is_array($_ERRORS))
            return str_replace('"', '&quot;', $string);
        $key = md5(str_replace('\'', '\\\'', $string));
        $str = (isset($_ERRORS) && is_array($_ERRORS) && array_key_exists($key, $_ERRORS)) ? ($htmlentities ? htmlentities($_ERRORS[$key], ENT_COMPAT, 'UTF-8') : $_ERRORS[$key]) : $string;
        return str_replace('"', '&quot;', stripslashes($str));
    }

    /**
     * Display an error with detailed object
     *
     * @param mixed $object
     * @param boolean $kill
     * @return $object if $kill = false;
     */
    public static function dieObject($object, $kill = true) {
        echo '<xmp style="text-align: left;">';
        print_r($object);
        echo '</xmp><br />';
        if ($kill)
            die('END');
        return $object;
    }

    /**
     * Display a var dump in firebug console
     *
     * @param object $object Object to display
     */
    public static function fd($object) {
        echo '
			<script type="text/javascript">
				console.log(' . json_encode($object) . ');
			</script>
		';
    }

    /**
     * ALIAS OF dieObject() - Display an error with detailed object
     *
     * @param object $object Object to display
     */
    public static function d($object, $kill = true) {
        return (Tools::dieObject($object, $kill));
    }

    /**
     * ALIAS OF dieObject() - Display an error with detailed object but don't stop the execution
     *
     * @param object $object Object to display
     */
    public static function p($object) {
        return (Tools::dieObject($object, false));
    }

    /**
     * Check if submit has been posted
     *
     * @param string $submit submit name
     */
    public static function isSubmit($submit) {
        return (
                isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y']) || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y'])
                );
    }

    public static function encrypt($data, $key) {
        $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
        $key = substr(hash('sha256', $salt . $key . $salt), 0, 32);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
        return $encrypted;
    }

    public static function decrypt($data, $key) {
        $salt = 'cH!swe!retReGu7W6bEDRup7usuDUh9THeD2CHeGE*ewr4n39=E@rAsp7c-Ph@pH';
        $key = substr(hash('sha256', $salt . $key . $salt), 0, 32);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    }

    /**
     * Return the friendly url from the provided string
     *
     * @param string $str
     * @param bool $utf8_decode => needs to be marked as deprecated
     * @return string
     */
    public static function link_rewrite($str, $utf8_decode = false) {
        return Tools::str2url($str);
    }

    /**
     * Return a friendly url made from the provided string
     * If the mbstring library is available, the output is the same as the js function of the same name
     *
     * @param string $str
     * @return string
     */
    public static function str2url($str) {
        if (function_exists('mb_strtolower'))
            $str = mb_strtolower($str, 'utf-8');

        $str = trim($str);
        if (!function_exists('mb_strtolower') || !Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'))
            $str = Tools::replaceAccentedChars($str);

        // Remove all non-whitelist chars.
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'))
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-\pL]/u', '', $str);
        else
            $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-]/', '', $str);

        $str = preg_replace('/[\s\'\:\/\[\]-]+/', ' ', $str);
        $str = str_replace(array(' ', '/'), '-', $str);

        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.
        if (!function_exists('mb_strtolower'))
            $str = strtolower($str);

        return $str;
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     * @return string
     */
    public static function replaceAccentedChars($str) {
        $patterns = array(
            /* Lowercase */
            '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
            '/[\x{00E7}\x{010D}\x{0107}]/u',
            '/[\x{010F}]/u',
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
            '/[\x{0142}\x{013E}\x{013A}]/u',
            '/[\x{00F1}\x{0148}]/u',
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
            '/[\x{0159}\x{0155}]/u',
            '/[\x{015B}\x{0161}]/u',
            '/[\x{00DF}]/u',
            '/[\x{0165}]/u',
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
            '/[\x{00FD}\x{00FF}]/u',
            '/[\x{017C}\x{017A}\x{017E}]/u',
            '/[\x{00E6}]/u',
            '/[\x{0153}]/u',
            /* Uppercase */
            '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
            '/[\x{00C7}\x{010C}\x{0106}]/u',
            '/[\x{010E}]/u',
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
            '/[\x{0141}\x{013D}\x{0139}]/u',
            '/[\x{00D1}\x{0147}]/u',
            '/[\x{00D3}]/u',
            '/[\x{0158}\x{0154}]/u',
            '/[\x{015A}\x{0160}]/u',
            '/[\x{0164}]/u',
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
            '/[\x{017B}\x{0179}\x{017D}]/u',
            '/[\x{00C6}]/u',
            '/[\x{0152}]/u');

        $replacements = array(
            'a', 'c', 'd', 'e', 'i', 'l', 'n', 'o', 'r', 's', 'ss', 't', 'u', 'y', 'z', 'ae', 'oe',
            'A', 'C', 'D', 'E', 'L', 'N', 'O', 'R', 'S', 'T', 'U', 'Z', 'AE', 'OE'
        );

        return preg_replace($patterns, $replacements, $str);
    }

    /**
     * Truncate strings
     *
     * @param string $str
     * @param integer $max_length Max length
     * @param string $suffix Suffix optional
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
     * * For other purposes use the smarty function instead */
    public static function truncate($str, $max_length, $suffix = '...') {
        if (Tools::strlen($str) <= $max_length)
            return $str;
        $str = utf8_decode($str);
        return (utf8_encode(substr($str, 0, $max_length - Tools::strlen($suffix)) . $suffix));
    }

    /**
     * Generate date form
     *
     * @param integer $year Year to select
     * @param integer $month Month to select
     * @param integer $day Day to select
     * @return array $tab html data with 3 cells :['days'], ['months'], ['years']
     *
     */
    public static function dateYears() {
        $tab = array();
        for ($i = date('Y'); $i >= 1900; $i--)
            $tab[] = $i;
        return $tab;
    }

    public static function dateDays() {
        $tab = array();
        for ($i = 1; $i != 32; $i++)
            $tab[] = $i;
        return $tab;
    }

    public static function dateMonths() {
        $tab = array();
        for ($i = 1; $i != 13; $i++)
            $tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
        return $tab;
    }

    public static function hourGenerate($hours, $minutes, $seconds) {
        return implode(':', array($hours, $minutes, $seconds));
    }

    public static function dateFrom($date) {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . Tools::hourGenerate(0, 0, 0);
        return $date;
    }

    public static function dateTo($date) {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . Tools::hourGenerate(23, 59, 59);
        return $date;
    }

    public static function strtolower($str) {
        if (is_array($str))
            return false;
        if (function_exists('mb_strtolower'))
            return mb_strtolower($str, 'utf-8');
        return strtolower($str);
    }

    public static function strlen($str, $encoding = 'UTF-8') {
        if (is_array($str))
            return false;
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen'))
            return mb_strlen($str, $encoding);
        return strlen($str);
    }

    public static function stripslashes($string) {
        if (_MAGIC_QUOTES_GPC_)
            $string = stripslashes($string);
        return $string;
    }

    public static function strtoupper($str) {
        if (is_array($str))
            return false;
        if (function_exists('mb_strtoupper'))
            return mb_strtoupper($str, 'utf-8');
        return strtoupper($str);
    }

    public static function substr($str, $start, $length = false, $encoding = 'utf-8') {
        if (is_array($str))
            return false;
        if (function_exists('mb_substr'))
            return mb_substr($str, (int) $start, ($length === false ? Tools::strlen($str) : (int) $length), $encoding);
        return substr($str, $start, ($length === false ? Tools::strlen($str) : (int) $length));
    }

    public static function ucfirst($str) {
        return Tools::strtoupper(Tools::substr($str, 0, 1)) . Tools::substr($str, 1);
    }

    public static function iconv($from, $to, $string) {
        if (function_exists('iconv'))
            return iconv($from, $to . '//TRANSLIT', str_replace('¥', '&yen;', str_replace('£', '&pound;', str_replace('€', '&euro;', $string))));
        return html_entity_decode(htmlentities($string, ENT_NOQUOTES, $from), ENT_NOQUOTES, $to);
    }

    public static function isEmpty($field) {
        return ($field === '' || $field === null);
    }

   
    /**
     * returns the rounded value down of $value to specified precision
     *
     * @param float $value
     * @param int $precision
     * @return float
     */
    public static function ceilf($value, $precision = 0) {
        $precision_factor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false)
            return ($value);
        if ($tmp2[strlen($tmp2) - 1] == 0)
            return $value;
        return ceil($tmp) / $precision_factor;
    }

    /**
     * returns the rounded value up of $value to specified precision
     *
     * @param float $value
     * @param int $precision
     * @return float
     */
    public static function floorf($value, $precision = 0) {
        $precision_factor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false)
            return ($value);
        if ($tmp2[strlen($tmp2) - 1] == 0)
            return $value;
        return floor($tmp) / $precision_factor;
    }

    /**
     * file_exists() wrapper with cache to speedup performance
     *
     * @param string $filename File name
     * @return boolean Cached result of file_exists($filename)
     */
    public static function file_exists_cache($filename) {
        if (!isset(self::$file_exists_cache[$filename]))
            self::$file_exists_cache[$filename] = file_exists($filename);
        return self::$file_exists_cache[$filename];
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 5) {
        if ($stream_context == null)
            $stream_context = @stream_context_create(array('http' => array('timeout' => 5)));

        if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')))
            return @file_get_contents($url, $use_include_path, $stream_context);
        elseif (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } else
            return false;
    }

    public static function simplexml_load_file($url, $class_name = null) {
        if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')))
            return @simplexml_load_string(Tools::file_get_contents($url), $class_name);
        else
            return false;
    }

    public static $a = 0;

    /**
     * @deprecated as of 1.5 use Media::minifyHTML()
     */
    public static function minifyHTML($html_content) {
        Tools::displayAsDeprecated();
        return Media::minifyHTML($html_content);
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName)
     * @prototype string public static function toCamelCase(string $str[, bool $catapitalise_first_char = false])
     */
    public static function toCamelCase($str, $catapitalise_first_char = false) {
        $str = strtolower($str);
        if ($catapitalise_first_char)
            $str = ucfirst($str);
        return preg_replace_callback('/_+([a-z])/', create_function('$c', 'return strtoupper($c[1]);'), $str);
    }

    /**
     * Transform a CamelCase string to underscore_case string
     *
     * @param string $string
     * @return string
     */
    public static function toUnderscoreCase($string) {
        // 'CMSCategories' => 'cms_categories'
        // 'RangePrice' => 'range_price'
        return strtolower(trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '_'));
    }

    public static function getBrightness($hex) {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }

    /**
     * @deprecated as of 1.5 use Media::minifyHTMLpregCallback()
     */
    public static function minifyHTMLpregCallback($preg_matches) {
        Tools::displayAsDeprecated();
        return Media::minifyHTMLpregCallback($preg_matches);
    }

    /**
     * @deprecated as of 1.5 use Media::packJSinHTML()
     */
    public static function packJSinHTML($html_content) {
        Tools::displayAsDeprecated();
        return Media::packJSinHTML($html_content);
    }

    /**
     * @deprecated as of 1.5 use Media::packJSinHTMLpregCallback()
     */
    public static function packJSinHTMLpregCallback($preg_matches) {
        Tools::displayAsDeprecated();
        return Media::packJSinHTMLpregCallback($preg_matches);
    }

    /**
     * @deprecated as of 1.5 use Media::packJS()
     */
    public static function packJS($js_content) {
        Tools::displayAsDeprecated();
        return Media::packJS($js_content);
    }

    public static function parserSQL($sql) {
        if (strlen($sql) > 0) {
            require_once(_PS_TOOL_DIR_ . 'parser_sql/php-sql-parser.php');
            $parser = new parserSql($sql);
            return $parser->parsed;
        }
        return false;
    }

    /**
     * @deprecated as of 1.5 use Media::minifyCSS()
     */
    public static function minifyCSS($css_content, $fileuri = false) {
        Tools::displayAsDeprecated();
        return Media::minifyCSS($css_content, $fileuri);
    }

    public static function replaceByAbsoluteURL($matches) {
        global $current_css_file;

        $protocol_link = Tools::getCurrentUrlProtocolPrefix();

        if (array_key_exists(1, $matches)) {
            $tmp = dirname($current_css_file) . '/' . $matches[1];
            return 'url(\'' . $protocol_link . Tools::getMediaServer($tmp) . $tmp . '\')';
        }
        return false;
    }

    protected static $_cache_nb_media_servers = null;

    public static function getMediaServer($filename) {
        if (self::$_cache_nb_media_servers === null) {
            if (_MEDIA_SERVER_1_ == '')
                self::$_cache_nb_media_servers = 0;
            elseif (_MEDIA_SERVER_2_ == '')
                self::$_cache_nb_media_servers = 1;
            elseif (_MEDIA_SERVER_3_ == '')
                self::$_cache_nb_media_servers = 2;
            else
                self::$_cache_nb_media_servers = 3;
        }

        if (self::$_cache_nb_media_servers && ($id_media_server = (abs(crc32($filename)) % self::$_cache_nb_media_servers + 1)))
            return constant('_MEDIA_SERVER_' . $id_media_server . '_');
        return Tools::getHttpHost();
    }


    public static function isCallable($function) {
        $disabled = explode(',', ini_get('disable_functions'));
        return (!in_array($function, $disabled) && is_callable($function));
    }

    public static function pRegexp($s, $delim) {
        $s = str_replace($delim, '\\' . $delim, $s);
        foreach (array('?', '[', ']', '(', ')', '{', '}', '-', '.', '+', '*', '^', '$', '`', '"', '%') as $char)
            $s = str_replace($char, '\\' . $char, $s);
        return $s;
    }

    public static function str_replace_once($needle, $replace, $haystack) {
        $pos = strpos($haystack, $needle);
        if ($pos === false)
            return $haystack;
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * Function property_exists does not exist in PHP < 5.1
     *
     * @deprecated since 1.5.0 (PHP 5.1 required, so property_exists() is now natively supported)
     * @param object or class $class
     * @param string $property
     * @return boolean
     */
    public static function property_exists($class, $property) {
        Tools::displayAsDeprecated();

        if (function_exists('property_exists'))
            return property_exists($class, $property);

        if (is_object($class))
            $vars = get_object_vars($class);
        else
            $vars = get_class_vars($class);

        return array_key_exists($property, $vars);
    }

    /**
     * @desc identify the version of php
     * @return string
     */
    public static function checkPhpVersion() {
        $version = null;

        if (defined('PHP_VERSION'))
            $version = PHP_VERSION;
        else
            $version = phpversion('');

        //Case management system of ubuntu, php version return 5.2.4-2ubuntu5.2
        if (strpos($version, '-') !== false)
            $version = substr($version, 0, strpos($version, '-'));

        return $version;
    }

    /**
     * @desc try to open a zip file in order to check if it's valid
     * @return bool success
     */
    public static function ZipTest($from_file) {
        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            return ($zip->open($from_file, ZIPARCHIVE::CHECKCONS) === true);
        } else {
            require_once(dirname(__FILE__) . '/../tools/pclzip/pclzip.lib.php');
            $zip = new PclZip($from_file);
            return ($zip->privCheckFormat() === true);
        }
    }

    /**
     * @desc extract a zip file to the given directory
     * @return bool success
     */
    public static function ZipExtract($from_file, $to_dir) {
        if (!file_exists($to_dir))
            mkdir($to_dir, 0777);
        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            if ($zip->open($from_file) === true && $zip->extractTo($to_dir) && $zip->close())
                return true;
            return false;
        }
        else {
            require_once(dirname(__FILE__) . '/../tools/pclzip/pclzip.lib.php');
            $zip = new PclZip($from_file);
            $list = $zip->extract(PCLZIP_OPT_PATH, $to_dir);
            foreach ($list as $file)
                if ($file['status'] != 'ok' && $file['status'] != 'already_a_directory')
                    return false;
            return true;
        }
    }

   
    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     * @param string $value value to convert
     * @return int
     */
    public static function convertBytes($value) {
        if (is_numeric($value))
            return $value;
        else {
            $value_length = strlen($value);
            $qty = (int) substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }

    public static function display404Error() {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        include(dirname(__FILE__) . '/../404.php');
        die;
    }

    /**
     * Concat $begin and $end, add ? or & between strings
     *
     * @since 1.5.0
     * @param string $begin
     * @param string $end
     * @return string
     */
    public static function url($begin, $end) {
        return $begin . ((strpos($begin, '?') !== false) ? '&' : '?') . $end;
    }

    /**
     * Display error and dies or silently log the error.
     *
     * @param string $msg
     * @param bool $die
     * @return bool success of logging
     */
    public static function dieOrLog($msg, $die = true) {
        if ($die || (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_))
            die($msg);
        return Logger::addLog($msg);
    }

    /**
     * Convert \n and \r\n and \r to <br />
     *
     * @param string $string String to transform
     * @return string New string
     */
    public static function nl2br($str) {
        return str_replace(array("\r\n", "\r", "\n"), '<br />', $str);
    }

    /**
     * Clear cache for Smarty
     *
     * @param Smarty $smarty
     */
    public static function clearCache($smarty, $tpl = false, $cache_id = null, $compile_id = null) {
        if (is_null($smarty))
            $smarty = Context::getContext()->smarty;

        if (!$tpl && $cache_id === null && $compile_id === null)
            return $smarty->clearAllCache();

        return $smarty->clearCache($tpl, $cache_id, $compile_id);
    }

    /**
     * getMemoryLimit allow to get the memory limit in octet
     *
     * @since 1.4.5.0
     * @return int the memory limit value in octet
     */
    public static function getMemoryLimit() {
        $memory_limit = @ini_get('memory_limit');

        return Tools::getOctets($memory_limit);
    }

    /**
     * getOctet allow to gets the value of a configuration option in octet
     *
     * @since 1.5.0
     * @return int the value of a configuration option in octet
     */
    public static function getOctets($option) {
        if (preg_match('/[0-9]+k/i', $option))
            return 1024 * (int) $option;

        if (preg_match('/[0-9]+m/i', $option))
            return 1024 * 1024 * (int) $option;

        if (preg_match('/[0-9]+g/i', $option))
            return 1024 * 1024 * 1024 * (int) $option;

        return $option;
    }

    /**
     *
     * @return bool true if the server use 64bit arch
     */
    public static function isX86_64arch() {
        return (PHP_INT_MAX == '9223372036854775807');
    }

    /**
     * Get max file upload size considering server settings and optional max value
     *
     * @param int $max_size optional max file size
     * @return int max file size in bytes
     */
    public static function getMaxUploadSize($max_size = 0) {
        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));
        $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));
        if ($max_size > 0)
            $result = min($post_max_size, $upload_max_filesize, $max_size);
        else
            $result = min($post_max_size, $upload_max_filesize);
        return $result;
    }

    /**
     * apacheModExists return true if the apache module $name is loaded
     * @TODO move this method in class Information (when it will exist)
     *
     * Notes: This method requires either apache_get_modules or phpinfo()
     * to be available. With CGI mod, we cannot get php modules
     *
     * @param string $name module name
     * @return boolean true if exists
     * @since 1.4.5.0
     */
    public static function apacheModExists($name) {
        if (function_exists('apache_get_modules')) {
            static $apache_module_list = null;

            if (!is_array($apache_module_list))
                $apache_module_list = apache_get_modules();

            // we need strpos (example, evasive can be evasive20)
            foreach ($apache_module_list as $module) {
                if (strpos($module, $name) !== false)
                    return true;
            }
        }
        return false;
    }

    /**
     * @params string $path Path to scan
     * @params string $ext Extention to filter files
     * @params string $dir Add this to prefix output for example /path/dir/*
     *
     * @return array List of file found
     * @since 1.5.0
     */
    public static function scandir($path, $ext = 'php', $dir = '', $recursive = false) {
        $path = rtrim(rtrim($path, '\\'), '/') . '/';
        $real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
        $files = scandir($real_path);
        if (!$files)
            return array();

        $filtered_files = array();

        $real_ext = '';
        if (!empty($ext))
            $real_ext = '.' . $ext;
        $real_ext_length = strlen($real_ext);

        $subdir = ($dir) ? $dir . '/' : '';
        foreach ($files as $file) {
            if (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length))
                $filtered_files[] = $subdir . $file;

            if ($recursive && $file[0] != '.' && is_dir($real_path . $file))
                foreach (Tools::scandir($path, $ext, $subdir . $file, $recursive) as $subfile)
                    $filtered_files[] = $subfile;
        }
        return $filtered_files;
    }

    /**
     * Align version sent and use internal function
     *
     * @static
     * @param $v1
     * @param $v2
     * @param string $operator
     * @return mixed
     */
    public static function version_compare($v1, $v2, $operator = '<') {
        Tools::alignVersionNumber($v1, $v2);
        return version_compare($v1, $v2, $operator);
    }

    /**
     * Align 2 version with the same number of sub version
     * version_compare will work better for its comparison :)
     * (Means: '1.8' to '1.9.3' will change '1.8' to '1.8.0')
     * @static
     * @param $v1
     * @param $v2
     */
    public static function alignVersionNumber(&$v1, &$v2) {
        $len1 = count(explode('.', trim($v1, '.')));
        $len2 = count(explode('.', trim($v2, '.')));
        $len = 0;
        $str = '';

        if ($len1 > $len2) {
            $len = $len1 - $len2;
            $str = &$v2;
        } else if ($len2 > $len1) {
            $len = $len2 - $len1;
            $str = &$v1;
        }

        for ($len; $len > 0; $len--)
            $str .= '.0';
    }

    public static function modRewriteActive() {
        if (Tools::apacheModExists('mod_rewrite'))
            return true;
        if ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || strtolower(getenv('HTTP_MOD_REWRITE')) == 'on')
            return true;
        return false;
    }

    public static function unSerialize($serialized, $object = false) {
        if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) && !$object || $object)
            return @unserialize($serialized);

        return false;
    }

    /**
     * Reproduce array_unique working before php version 5.2.9 
     * @param array $array
     * @return array
     */
    public static function arrayUnique($array) {
        if (version_compare(phpversion(), '5.2.9', '<'))
            return array_unique($array);
        else
            return array_unique($array, SORT_REGULAR);
    }

    /**
     * Delete unicode class from regular expression patterns
     * @param string $pattern
     * @return pattern
     */
    public static function cleanNonUnicodeSupport($pattern) {
        if (!defined('PREG_BAD_UTF8_OFFSET'))
            return $pattern;
        return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
    }

}