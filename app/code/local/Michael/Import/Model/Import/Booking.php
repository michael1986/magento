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

/**
 * Import booking data.
 * TODO: validate data.
 *
 * @category   Michael
 * @package    Michael_Import
 * @author     Michael Talashov <michael.talashov@gmail.com>
 * @copyright  2018 Michael Talashov
 */
class Michael_Import_Model_Import_Booking extends Mage_Core_Model_Abstract
    implements Michael_Import_Model_Import_Interface
{
    /**
     * @var array Item data mapping.
     */
    protected $_dataMapping = [
        'booking_key' => 'booking_key',
        'order_number' => 'order_number',
        'order_id' => 'order_id',
        'product_id' => 'product_id',
        'option_id' => 'option_id',
        'product_supplier_id' => 'product_supplier_id',
        'event_date_time' => 'event_date_time',
        'date_applied_for' => 'date_applied_for',
        'type' => 'type',
        'status' => 'status',
        'created_at' => 'created_at',
        'ticket_name' => 'ticket_name',
        'product_name' => 'product_name',
        'option_name' => 'option_name',
        'variation_id' => 'variation_id',
        'variation_name' => 'variation_name',
        'qty' => 'qty',
        'qty_cancelled' => 'qty_cancelled',
        'external_id' => 'external_id',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email',
        'phone_number' => 'phone_number',
        'attendees' => 'attendees',
        'duration_type' => 'duration_type',
        'duration_value' => 'duration_type'
    ];

    /**
     * @var array Contact data mapping.
     */
    protected $_contactDataMapping = [
        'firstname' => 'contact_firstname',
        'lastname' => 'contact_lastname',
        'email' => 'contact_email',
        'telephone' => 'contact_telephone'
    ];

    /**
     * @var array List of errors.
     */
    protected $_errors = [];

    /**
     * Import items.
     *
     * @param array $items Items.
     *
     * @return void
     *
     * @throws Exception
     */
    public function import($items)
    {
        foreach ($items as $item) {
            $model = Mage::getModel('michael_import/booking');
            $bookingKey = isset($item['booking_key']) ? $item['booking_key'] : '';
            if ($bookingKey !== '') {
                $model->load($bookingKey, 'booking_key');
            }

            $bookingId = $model->getId();
            $mappedData = $this->_mapData($item);
            $model->setData($mappedData);
            $model->setId($bookingId);

            try {
                $model->save();
            } catch (Exception $e) {
                $this->_errors[] = ['id' => $bookingKey, 'message' => $e->getMessage()];
            }
            $model->unsetData();
        }
    }

    /**
     * Get errors caused during Import.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Prepare data for saving.
     *
     * @param array $itemData Raw Item data.
     *
     * @return array
     */
    protected function _mapData($itemData)
    {
        $result = [];
        foreach ($itemData as $key => $value) {
            if (array_key_exists($key, $this->_dataMapping)) {
                $result[$this->_dataMapping[$key]] = $value;
            }
        }

        $contactData = empty($itemData['contact_data']) ? [] : $itemData['contact_data'];
        foreach ($contactData as $key => $value) {
            if (array_key_exists($key, $this->_contactDataMapping)) {
                $result[$this->_contactDataMapping[$key]] = $value;
            }
        }

        return $result;
    }
}