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

namespace MageINIC\ProductAttachment\Api;

use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * MageINIC ProductAttachment interface.
 * @api
 */
interface ProductAttachmentRepositoryInterface
{
    /**
     * Save page.
     *
     * @param  ProductAttachmentInterface $attachment
     * @return ProductAttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ProductAttachmentInterface $attachment): ProductAttachmentInterface;

    /**
     * Retrieve attachment.
     *
     * @param int $attachmentId
     * @return \MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $attachmentId): ProductAttachmentInterface;

    /**
     * Delete attachment.
     *
     * @param  ProductAttachmentInterface $attachment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ProductAttachmentInterface $attachment): bool;

    /**
     * Delete attachment by ID.
     *
     * @param int $attachmentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $attachmentId): bool;

    /**
     * Retrieve category matching the specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\ProductAttachment\Api\Data\ProductAttachmentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
