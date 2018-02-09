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
 * Import data Interface.
 *
 * @category   Michael
 * @package    Michael_Import
 * @author     Michael Talashov <michael.talashov@gmail.com>
 * @copyright 2018 Michael Talashov
 */
interface Michael_Import_Model_Import_Interface
{
    /**
     * Import items.
     *
     * @param array $items Items.
     *
     * @return void
     */
    public function import($items);
}