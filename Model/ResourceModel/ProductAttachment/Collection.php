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

namespace MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment;

use Exception;
use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use MageINIC\ProductAttachment\Model\ResourceModel\AbstractCollection;
use MageINIC\ProductAttachment\Model\ProductAttachment as model;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment as resourceModel;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

/**
 * ProductAttachment Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attachment_id';

    /**
     * @var bool
     *
     */
    protected bool $_previewFlag;

    /**
     * @var string
     */
    protected $_eventPrefix = 'mageinic_product_attachment_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'mageinic_product_attachment_grid_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            model::class,
            resourceModel::class
        );
        $this->_map['fields']['attachment_id'] = 'main_table.attachment_id';
        $this->_map['fields']['store'] = 'mageinic_attachment_store.store_id';
        $this->_map['fields']['products'] = 'mageinic_attachment_store.attachment_id';
    }

    /**
     * @inheritDoc
     */
    public function addRelationFilter($product): Collection
    {
        if (!$this->getFlag('relation_filter_added')) {
            $this->performAddRelationFilter($product);
            $this->setFlag('relation_filter_added', true);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addStoreFilter($store, bool $withAdmin = true): AbstractCollection
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
            $this->setFlag('store_filter_added', true);
        }

        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     * @throws NoSuchEntityException
     * @throws Exception
     */
    protected function _afterLoad(): Collection
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $this->performAfterLoad('mageinic_product_attachment_relation', $entityMetadata->getLinkField());
        $this->storeperformAfterLoad('mageinic_attachment_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     * @throws Exception
     */
    protected function _renderFiltersBefore(): void
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $this->joinAttachmentRelationTable('mageinic_product_attachment_relation', $entityMetadata->getLinkField());
        $this->joinStoreRelationTable('mageinic_attachment_store', $entityMetadata->getLinkField());
    }
}
