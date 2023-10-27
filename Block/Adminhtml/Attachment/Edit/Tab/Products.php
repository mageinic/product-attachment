<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_ProductAttachment
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\ProductAttachment\Block\Adminhtml\Attachment\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use MageINIC\ProductAttachment\Model\ProductAttachment;
use MageINIC\ProductAttachment\Model\ProductAttachmentFactory;
use Magento\Backend\Block\Widget\Grid\Extended;

/**
 * ProductAttachment Class Products
 */
class Products extends Extended
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;

    /**
     * @var ProductAttachment
     */
    private ProductAttachment $attachModel;

    /**
     * @var ProductAttachmentFactory
     */
    private ProductAttachmentFactory $contactFactory;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * Products constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param Registry $registry
     * @param ProductAttachmentFactory $contactFactory
     * @param CollectionFactory $productCollectionFactory
     * @param ProductAttachment $attachModel
     * @param array $data
     */
    public function __construct(
        Context                  $context,
        Data                     $backendHelper,
        Registry                 $registry,
        ProductAttachmentFactory $contactFactory,
        CollectionFactory        $productCollectionFactory,
        ProductAttachment        $attachModel,
        array                    $data = []
    ) {
        $this->contactFactory = $contactFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->attachModel = $attachModel;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     *
     * @return void
     * @throws LocalizedException
     */
    public function _construct(): void
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('attachment_id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    /**
     * Add column filtering conditions to collection
     *
     * @param Column $column
     * @return $this
     * @throws LocalizedException
     */
    public function _addColumnFilterToCollection($column): Products
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Get Select Products
     *
     * @return array
     */
    public function _getSelectedProducts(): array
    {
        $contact = $this->getContact();
        return $contact->getProducts($contact);
    }

    /**
     * Get Contact
     *
     * @return ProductAttachment
     */
    public function getContact(): ProductAttachment
    {
        $contactId = $this->getRequest()->getParam('attachment_id');
        $contact = $this->contactFactory->create();
        if ($contactId) {
            $contact->load($contactId);
        }
        return $contact;
    }

    /**
     * Prepare collection
     *
     * @return Products
     */
    public function _prepareCollection(): Products
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'price']);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Products
     * @throws Exception
     */
    public function _prepareColumns(): Products
    {
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'names',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get Row Url
     *
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row): string
    {
        return '';
    }

    /**
     * Get Selected Products
     *
     * @return array
     */
    public function getSelectedProducts(): array
    {
        $contact = $this->getContact();
        $selected = $contact->getProducts($contact);
        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden(): bool
    {
        return true;
    }
}
