<?php
class Michael_Import_Model_Resource_Booking_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('michael_import/booking');
    }
}