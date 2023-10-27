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

namespace MageINIC\ProductAttachment\Api\Data;

/**
 * MageINIC ProductAttachment interface.
 * @api
 */
interface ProductAttachmentInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const PRODUCT_ATTACHMENT_ID = 'attachment_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const UPLOADED_FILE = 'uploaded_file';
    public const PRODUCT_IDS = 'product_ids';
    public const ACTIVE = 'active';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const SORT_ORDER = 'sort_order';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getAttachmentId(): ?int;

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Get uploaded File
     *
     * @return string|null
     */
    public function getUploadedFile(): ?string;

    /**
     * Get product ids
     *
     * @return string[]
     */
    public function getProductIds();

    /**
     * Get active
     *
     * @return string|null
     */
    public function getActive(): ?string;

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Get sort order
     *
     * @return string|null
     */
    public function getSortOrder(): ?string;

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setAttachmentId($id): ProductAttachmentInterface;

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): ProductAttachmentInterface;

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): ProductAttachmentInterface;

    /**
     * Set uploaded file
     *
     * @param string $uploadedFile
     * @return $this
     */
    public function setUploadedFile(string $uploadedFile): ProductAttachmentInterface;

    /**
     * Set product ids
     *
     * @param int[] $productIds
     * @return $this
     */
    public function setProductIds(array $productIds): ProductAttachmentInterface;

    /**
     * Set active
     *
     * @param string $active
     * @return $this
     */
    public function setActive(string $active): ProductAttachmentInterface;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): ProductAttachmentInterface;

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): ProductAttachmentInterface;

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder(string $sortOrder): ProductAttachmentInterface;
}
