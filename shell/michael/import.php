<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Michael
 * @package   Michael_Import
 * @author    Michael Talashov <michael.talashov@gmail.com>
 * @copyright 2018 Michael Talashov
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'abstract.php';

/**
 * Import data fetched from external API.
 *
 * @category   Michael
 * @package    Michael_Import
 * @author     Michael Talashov <michael.talashov@gmail.com>
 * @copyright  2018 Michael Talashov
 */
class Michael_Shell_Import extends Mage_Shell_Abstract
{
    /**
     * @var string Properties with passed arguments.
     */
    protected $_basePath = 'supplier/bookings',
        $_baseUrl = 'https://sandbox-api.regiondo.com/v1/',
        $_acceptLanguage = 'de-DE',
        $_publicKey,
        $_privateKey,
        $_data;

    /**
     * @var array Mapping Actions to Import models.
     */
    protected $_mapActionToImportModel = [
        'supplier/bookings' => 'michael_import/import_booking'
    ];

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
        // Send request and handle response.
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
            die('Server responded with error: ' . $decodedResponse['message'] . ' (' .$decodedResponse['code']. ")\n");
        }

        // Import data.
        $importModel = Mage::getModel($this->_mapActionToImportModel[$this->_basePath]);
        $importModel->import($decodedResponse['data']);

        // Output errors.
        if (count($importModel->getErrors()) > 0) {
            $textMessage = "The following items weren't imported because of errors:\n";
            foreach ($importModel->getErrors() as $errorData) {
                if (array_key_exists('id', $errorData) && array_key_exists('message', $errorData)) {
                    $textMessage .= "Item #" . $errorData['id'] . ": " . $errorData['message'] . "\n";
                } else {
                    $textMessage .= $errorData . "\n";
                }
            }
            print_r($textMessage);
        }

        die("Import completed\n");
    }

    /**
     * Set passed arguments to class properties.
     *
     * @return void
     */
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
        $this->_data = $this->_parseData($this->getArg('data'));
    }

    /**
     * Validate arguments.
     *
     * @return void
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
        if ($this->getArg('action')
            && !in_array($this->getArg('action'), $this->_mapActionToImportModel)
        ) {
            echo "There is no Import Model for provided action\n\n";
            echo $this->usageHelp();
            die();
        }
    }

    /**
     * Execute request via CURL.
     *
     * @param array $data Request parameters.
     * @param array $headers Headers.
     *
     * @return string
     */
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
     * Parse Data argument.
     *
     * @param string $data Data argument.
     *
     * @return array
     */
    protected function _parseData($data)
    {
        $res = [];
        if (!empty($data)) {
            $tmp = explode('#', $data);
            foreach ($tmp as $param) {
                list($k, $v) = explode('=', $param);
                $res[ $k ] = $v;
            }
        }

        return $res;
    }

    /**
     * Retrieve Usage Help message.
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f import.php -- [options]
        php import.php --publicKey YOUR_PUBLIC_KEY --privateKey YOUR_PRIVATE_KEY
        php import.php --publicKey YOUR_PUBLIC_KEY --privateKey YOUR_PRIVATE_KEY --action supplier/bookings --lang de-DE --baseUrl https://sandbox-api.regiondo.com/v1/ --data limit=10#offset=100

  --publicKey <KEY>          Public key
  --privateKey <KEY>         Protected (secure) key
  --action                   Url action part, allowed values: supplier/bookings (by default) 
  --lang                     Accepted language, e.g. de-DE, en-US, de-AT, fr-FR etc.
  --baseUrl                  Base API URL, e.g. https://api.regiondo.de/v1/
  --data                     Any GET parameters which you want to send, e.g. limit=10#offset=100
    
  help                       This help

USAGE;
    }
}

$shell = new Michael_Shell_Import();
$shell->run();