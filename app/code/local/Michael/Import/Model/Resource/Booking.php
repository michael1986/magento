<?php

class Michael_Import_Model_Resource_Booking extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('michael_import/booking', 'booking_id');
    }
}