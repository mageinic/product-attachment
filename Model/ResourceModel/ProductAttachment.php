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

namespace MageINIC\ProductAttachment\Model\ResourceModel;

use Exception;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageINIC\ProductAttachment\Model\ProductAttachment as ProductAttachmentModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Product Attachment ResourceModel
 */
class ProductAttachment extends AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attachment_id';

    /**
     * @var DateTime
     */
    protected DateTime $_date;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * @var MetadataPool
     */
    private MetadataPool $metadataPool;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $_storeManager;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * ProductAttachment constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param DateTime $date
     * @param ManagerInterface $messageManager
     * @param MetadataPool $metadataPool
     * @param mixed|null $resourcePrefix
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        EntityManager         $entityManager,
        DateTime              $date,
        ManagerInterface      $messageManager,
        MetadataPool          $metadataPool,
        $resourcePrefix = null
    ) {
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null): ProductAttachment
    {
        $blockId = $this->getStoresId($object, $value, $field);
        $this->getProductsId($object, $value, $field);
        if ($blockId) {
            $this->entityManager->load($object, $blockId);
        }
        return $this;
    }

    /**
     * Get block id.
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    private function getStoresId(AbstractModel $object, mixed $value, string $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'title';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param ProductAttachmentModel|AbstractModel $object
     * @return Select
     * @throws LocalizedException
     * @throws Exception
     */
    protected function _getLoadSelect($field, $value, $object): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

            $select->join(
                ['cbs' => $this->getTable('mageinic_attachment_store')],
                $this->getMainTable() . '.' . $linkField . ' = cbs.' . $linkField,
                ['store_id']
            )
                ->where('is_active = ?', 1)
                ->where('cbs.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Get block id.
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    private function getProductsId(AbstractModel $object, mixed $value, string $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'title';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getProductId()) {
            $select = $this->_getLoadProductSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param ProductAttachmentModel|AbstractModel $object
     * @return Select
     * @throws LocalizedException
     * @throws Exception
     */
    protected function _getLoadProductSelect(string $field, mixed $value, $object): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getId()) {
            $stores = [(int)$object->getId(), ProductAttachmentInterface::PRODUCT_ATTACHMENT_ID];

            $select->join(
                ['cbs' => $this->getTable('mageinic_product_attachment_relation')],
                $this->getMainTable() . '.' . $linkField . ' = cbs.' . $linkField,
                ['product_id']
            )
                ->where('cbs.product_id in (?)', $stores)
                ->order('product_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    public function lookupStoreIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cbs' => $this->getTable('mageinic_attachment_store')], 'store_id')
            ->join(
                ['cb' => $this->getMainTable()],
                'cbs.' . $linkField . ' = cb.' . $linkField,
                []
            )
            ->where('cb.' . $entityMetadata->getIdentifierField() . ' = :attachment_id');

        return $connection->fetchCol($select, ['attachment_id' => (int)$id]);
    }

    /**
     * Save ProductAttachment Relation
     *
     * @param array $attachmentIds
     * @param int $attachmentProductId
     * @return $this
     * @throws LocalizedException
     */
    public function saveProductAttachmentRelation(array $attachmentIds, int $attachmentProductId): ProductAttachment
    {
        $oldProducts = $this->lookupProductAttachmentsIds($attachmentProductId);
        $newProducts = $attachmentIds;

        if (isset($newProducts)) {
            $table = $this->getTable('mageinic_product_attachment_relation');
            $insert = array_diff((array)$newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);
            if ($delete) {
                $where = [
                    'product_id = ?' => (int)$attachmentProductId,
                    'attachment_id IN (?)' => $delete
                ];
                $this->getConnection()->delete($table, $where);
            }
            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    $data[] = [
                        'attachment_id' => (int)$productId,
                        'product_id' => (int)$attachmentProductId
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }
        return $this;
    }

    /**
     * ProductAttachment Ids
     *
     * @param int $productId
     * @return array
     */
    public function lookupProductAttachmentsIds(int $productId): array
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from($this->getTable(
            'mageinic_product_attachment_relation'
        ), 'attachment_id')
            ->where('product_id = ?', (int)$productId);
        return $adapter->fetchCol($select);
    }

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('mageinic_product_attachment', 'attachment_id');
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object): ProductAttachment
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            throw new LocalizedException(
                __('A block identifier with the same properties already exists in the selected store.')
            );
        }
        return $this;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @throws LocalizedException
     * @throws Exception
     */
    public function getIsUniqueBlockToStores(AbstractModel $object): bool
    {
        $entityMetadata = $this->metadataPool->getMetadata(ProductAttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $stores = $this->_storeManager->isSingleStoreMode()
            ? [Store::DEFAULT_STORE_ID]
            : (array)$object->getData('store_id');

        $select = $this->getConnection()->select()
            ->from(['cb' => $this->getMainTable()])
            ->join(
                ['cbs' => $this->getTable('mageinic_attachment_store')],
                'cb.' . $linkField . ' = cbs.' . $linkField,
                []
            )
            ->where('cb.name = ?  ', $object->getData('name'))
            ->where('cbs.store_id IN (?)', $stores);
        if ($object->getId()) {
            $select->where('cb.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ProductAttachmentInterface::class)->getEntityConnection();
    }

    /**
     * Save Product Relation
     *
     * @param ProductAttachmentModel $productattachment
     * @return $this
     */
    protected function saveProductRelation(ProductAttachmentModel $productattachment): ProductAttachment
    {
        $oldProducts = $this->lookupProductIds($productattachment->getId());
        if (!count($oldProducts)) {
            $oldProducts = [];
        }
        $newProducts = $productattachment->getProductIds();
        $newProducts = $newProducts ? explode('&', $newProducts ?? '') : [];
        $table = $this->getTable('mageinic_product_attachment_relation');
        if (!empty($newProducts)) {
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);
            if ($delete) {
                $where = [
                    'attachment_id = ?' => (int)$productattachment->getId(),
                    'product_id IN (?)' => $delete
                ];
                $this->getConnection()->delete($table, $where);
            }
            if ($insert) {
                $data = [];
                foreach ($insert as $productId) {
                    if ((int)$productId > 0) {
                        $data[] = [
                            'attachment_id' => (int)$productattachment->getId(),
                            'product_id' => (int)$productId
                        ];
                    }
                }
                if (!empty($data)) {
                    $this->getConnection()->insertMultiple($table, $data);
                }
            }
        } else {
            $where = [
                'attachment_id = ?' => (int)$productattachment->getId(),
                'product_id IN (?)' => $oldProducts
            ];
            $this->getConnection()->delete($table, $where);
        }

        return $this;
    }

    /**
     * Product Ids
     *
     * @param int $productattachmentId
     * @return array
     */
    public function lookupProductIds(int $productattachmentId): array
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getTable('mageinic_product_attachment_relation'), 'product_id')
            ->where('attachment_id = ?', (int)$productattachmentId);

        return $adapter->fetchCol($select);
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws Exception
     */
    public function save(AbstractModel $object)
    {
            $this->entityManager->save($object);
            $this->_afterSave($object);

        return $this;
    }

    /**
     * After save logic
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object): ProductAttachment
    {
        $this->saveProductRelation($object);

        return parent::_afterSave($object);
    }
}
