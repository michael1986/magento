<?php

class Michael_Import_Model_Booking extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('michael_import/booking');
    }

    /**
     * Save items.
     *
     * @param $items
     *
     * @return void
     */
    public function saveItems($items)
    {
        $resource = \Mage::getSingleton('core/resource');
        $dbh = $resource->getConnection('default_write');
        $dbh->beginTransaction();

        foreach ($items as $item) {
            $this->setData($item);
            foreach ($item['contact_data'] as $contactKey => $contactValue) {
                $this->setData('contact_' . $contactKey, $contactValue);
            }
            $this->save();
            $this->unsetData();
        }

        $dbh->commit();
    }
}