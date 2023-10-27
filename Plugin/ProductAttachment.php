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

namespace MageINIC\ProductAttachment\Plugin;

use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface as AttachmentRepository ;
use MageINIC\ProductAttachment\Helper\Data;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder as SearchCriteria;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * ProductAttachment Plugin Class
 */
class ProductAttachment
{
    /**
     * Media path
     */
    public const MEDIA_FOLDER = 'mageINIC/product_attachments/';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var SearchCriteria
     */
    private SearchCriteria $searchCriteriaBuilder;

    /**
     * @var AttachmentRepository
     */
    private AttachmentRepository $attachmentRepository;

    /**
     * @var Data
     */
    private Data $data;

    /**
     * @var File
     */
    private File $file;

    /**
     * @var Repository
     */
    private Repository $assetRepo;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * ProductAttachment Constructor
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteria $searchCriteriaBuilder
     * @param AttachmentRepository $attachmentRepository
     * @param Data $data
     * @param File $file
     * @param Repository $assetRepo
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SearchCriteria        $searchCriteriaBuilder,
        AttachmentRepository  $attachmentRepository,
        Data                  $data,
        File                  $file,
        Repository            $assetRepo,
        SortOrderBuilder      $sortOrderBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->data = $data;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attachmentRepository = $attachmentRepository;
        $this->file = $file;
        $this->assetRepo = $assetRepo;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * After Get Sku
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @return ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGet(ProductRepositoryInterface $subject, ProductInterface $result): ProductInterface
    {
        $isEnable = $this->data->isModuleEnable();
        $attachment = [];
        $baseUrl = $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_FOLDER;
        $storeId = $this->storeManager->getStore()->getId();
        if ($isEnable) {
            $sortOrder = $this->sortOrderBuilder
                ->setField('sort_order')
                ->setDirection('ASC')
                ->create();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('store_id', $storeId)
                ->addFilter('product_id', $result->getId())
                ->addFilter('active', true)
                ->addSortOrder($sortOrder)
                ->create();
            $attachmentDetails = $this->attachmentRepository->getList($searchCriteria);
            foreach ($attachmentDetails->getItems() as $item) {
                $attachment['items'][] = [
                    'title' => $item->getName(),
                    'description' => $item->getDescription(),
                    'icon' => $this->getFileIcon($item),
                    'url' => $baseUrl . $item->getUploadedFile()
                ];
            }
        }
        $extensionAttributes = $result->getExtensionAttributes();
        $extensionAttributes->setProductAttachment($attachment);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    /**
     * Get File Icon
     *
     * @param ProductAttachmentInterface $item
     * @return string
     */
    public function getFileIcon(ProductAttachmentInterface $item): string
    {
        $file = $item->getUploadedFile();
        $fileInfo = $this->file->getPathInfo($file);
        $type = $fileInfo['extension'];
        $area = ['area' => Area::AREA_FRONTEND];
        $fileTypes = [
            'csv' => 'MageINIC_ProductAttachment/images/csv.svg',
            'pdf' => 'MageINIC_ProductAttachment/images/pdf.svg',
            'pptx' => 'MageINIC_ProductAttachment/images/ppt.svg',
            'txt' => 'MageINIC_ProductAttachment/images/txt.svg',
            'doc' => 'MageINIC_ProductAttachment/images/word.svg'
        ];

        return $this->assetRepo->getUrlWithParams($fileTypes[$type] ?? '', $area);
    }
}

