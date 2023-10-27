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

namespace MageINIC\ProductAttachment\ViewModel;

use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface as AttachmentRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Product Attachment ViewModel
 */
class ProductAttachment implements ArgumentInterface
{
    public const MEDIA_FOLDER = 'mageINIC/product_attachments/';

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var SearchCriteria
     */
    private SearchCriteria $searchCriteria;

    /**
     * @var AttachmentRepository
     */
    private AttachmentRepository $attachmentRepository;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var File
     */
    private File $file;

    /**
     * @var Repository
     */
    protected Repository $moduleAssetDir;

    /** ProductAttachment Constructor
     *
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteria $searchCriteria
     * @param AttachmentRepository $attachmentRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param File $file
     * @param Registry $registry
     * @param Repository $moduleAssetDir
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SearchCriteria        $searchCriteria,
        AttachmentRepository  $attachmentRepository,
        SortOrderBuilder      $sortOrderBuilder,
        File                  $file,
        Registry              $registry,
        Repository            $moduleAssetDir,
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->searchCriteria = $searchCriteria;
        $this->attachmentRepository = $attachmentRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->file = $file;
        $this->moduleAssetDir = $moduleAssetDir;
    }

    /**
     * Get Current Product
     *
     * @return mixed|null
     */
    public function getCurrentProduct(): mixed
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Product Attachment Collection
     *
     * @param Product $product
     * @return ProductAttachmentInterface[]
     * @throws LocalizedException
     */
    public function getProductAttachmentCollection(Product $product): array
    {
        $storeId = $this->storeManager->getStore()->getId();
        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection('ASC')
            ->create();
        $searchCriteria = $this->searchCriteria
            ->addFilter('store_id', $storeId)
            ->addFilter('active', 1)
            ->addFilter('product_id', $product->getId(), 'in')
            ->addSortOrder($sortOrder)
            ->create();
        $attachmentDetails = $this->attachmentRepository->getList($searchCriteria);

        return $attachmentDetails->getItems();
    }

    /**
     * Get Attachment Full Path
     *
     * @param string $attachment
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttchmentFullPath(string $attachment): string
    {
        $media = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $media . self::MEDIA_FOLDER . $attachment;
    }

    /**
     * Get path info
     *
     * @param string $path
     * @return mixed
     */
    public function getPathInfo(string $path): mixed
    {
        return $this->file->getPathInfo($path);
    }
}

