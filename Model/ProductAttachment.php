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

namespace MageINIC\ProductAttachment\Model;

use Magento\Framework\Model\AbstractModel;
use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use MageINIC\ProductAttachment\Model\ProductAttachment as productAttachmentModel;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment as resourceModel;

/**
 * ProductAttachment Class ProductAttachment
 */
class ProductAttachment extends AbstractModel implements ProductAttachmentInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFile(): ?string
    {
        return $this->getData(self::UPLOADED_FILE);
    }

    /**
     * @inheritdoc
     */
    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @inheritdoc
     */
    public function getActive(): ?string
    {
        return $this->getData(self::ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): ?string
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setAttachmentId($id): ProductAttachmentInterface
    {
        return $this->setData(self::PRODUCT_ATTACHMENT_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function setName($name): ProductAttachmentInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description): ProductAttachmentInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function setUploadedFile($uploadedFile): ProductAttachmentInterface
    {
        return $this->setData(self::UPLOADED_FILE, $uploadedFile);
    }

    /**
     * @inheritdoc
     */
    public function setProductIds($productIds): ProductAttachmentInterface
    {
        return $this->setData(self::PRODUCT_IDS, $productIds);
    }

    /**
     * @inheritdoc
     */
    public function setActive($active): ProductAttachmentInterface
    {
        return $this->setData(self::ACTIVE, $active);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt): ProductAttachmentInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt): ProductAttachmentInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder): ProductAttachmentInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get Products
     *
     * @param ProductAttachment $object
     * @return array
     */
    public function getProducts(productAttachmentModel $object): array
    {
        $tbl = $this->getResource()->getTable("mageinic_product_attachment_relation");
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
            ->where(
                'attachment_id = ?',
                (int)$object->getId()
            );

        $products = $this->getResource()->getConnection()->fetchCol($select);

        return $products;
    }

    /**
     * @inheritdoc
     */
    public function getAttachmentId(): ?int
    {
        return $this->getData(self::PRODUCT_ATTACHMENT_ID);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(resourceModel::class);
    }
}
