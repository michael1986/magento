<?php
// TODO: comment

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'abstract.php';

// TODO: comment
class Michael_Shell_ImportBookings extends Mage_Shell_Abstract
{
    // TODO: comments
    protected $_basePath = 'supplier/bookings';
    protected $_baseUrl = 'https://sandbox-api.regiondo.com/v1/';
    protected $_acceptLanguage = 'de-DE';
    protected $_publicKey;
    protected $_privateKey;
    protected $_data;

    /**
     * Additional initialize instruction.
     * Set default timezone.
     *
     * @return Mage_Shell_Abstract
     */
    protected function _construct()
    {
        date_default_timezone_set('UTC');

        return parent::_construct();
    }

    /**
     * Run script.
     *
     * @return void
     */
    public function run()
    {
        $this->_setArguments();
        $time = time();
        $message = $this->_buildMessage($time, $this->_publicKey, $this->_data);
        $hash = hash_hmac('sha256', $message, $this->_privateKey);
        $headers = [
            'X-API-ID: ' .   $this->_publicKey,
            'X-API-TIME: ' . $time,
            'X-API-HASH: ' . $hash,
            'Accept-Language: ' . $this->_acceptLanguage,
        ];
        $response = $this->_curlExec($this->_data, $headers);
        if (!$this->_isJson($response)) {
            die('Response body is not in JSON format');
        }
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse['code'])) {
            die('Server responded with error: ' . $decodedResponse['message'] . ' (' .$decodedResponse['code']. ')');
        }

        Mage::getModel('michael_import/booking')->saveItems($decodedResponse['data']);
    }

    protected function _setArguments()
    {
        $this->_publicKey = $this->getArg('publicKey');
        $this->_privateKey = $this->getArg('privateKey');
        if ($this->getArg('baseUrl')) {
            $this->_baseUrl = $this->getArg('baseUrl');
        }
        if ($this->getArg('action')) {
            $this->_basePath = $this->getArg('action');
        }
        if ($this->getArg('lang')) {
            $this->_acceptLanguage = $this->getArg('lang');
        }
        $this->_data = $this->_getData($this->getArg('data'));
    }

    /**
     * Validate arguments
     * todo
     */
    protected function _validate()
    {
        parent::_validate();

        $this->_showHelp();
        if (!$this->getArg('publicKey') || !$this->getArg('privateKey')) {
            echo "Public and Private keys are required\n\n";
            echo $this->usageHelp();
            die();
        }
    }

    protected function _curlExec($data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $this->_baseUrl . $this->_basePath . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === false) {
            die("Curl Error: " . curl_error($ch));
        }

        return $result;
    }

    /**
     * Check that content is JSON.
     *
     * @param $content
     *
     * @return bool
     */
    protected function _isJson($content)
    {
        json_decode($content);

        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function _buildMessage($time, $publicKey, $data)
    {
        return $time . $publicKey . http_build_query($data);
    }

    /**
     * Retrieve Usage Help message.
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f import_bookings.php -- [options]
        php import_bookings.php --publicKey YOUR_PUBLIC_KEY --privateKey YOUR_PRIVATE_KEY
        php import_bookings.php --publicKey YOUR_PUBLIC_KEY --privateKey YOUR_PRIVATE_KEY --lang de-DE --baseUrl https://sandbox-api.regiondo.com/v1/ --data limit=10#offset=100

  --publicKey <KEY>          Public key
  --privateKey <KEY>         Protected (secure) key
  --lang                     Accepted language, e.g. de-DE, en-US, de-AT, fr-FR etc.
  --baseUrl                  Base API URL, e.g. https://api.regiondo.de/v1/
  --data                     Any GET parameters which you want to send, e.g. limit=10#offset=100
    
  help                   This help

USAGE;
    }

    // TODO
    protected function _getData($argValue)
    {
        $res = [];
        if (!empty($argValue)) {
            $tmp = explode('#', $argValue);
            foreach ($tmp as $param) {
                list($k, $v) = explode('=', $param);
                $res[ $k ] = $v;
            }
        }

        return $res;
    }
}

$shell = new Michael_Shell_ImportBookings();
$shell->run();