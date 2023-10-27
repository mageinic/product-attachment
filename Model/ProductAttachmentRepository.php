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

use Exception;
use MageINIC\ProductAttachment\Api\Data\ProductAttachmentSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\ProductAttachment\Api\Data;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment as ResourcePage;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory
    as ProductAttachmentCollectionFactory;
use MageINIC\ProductAttachment\Api\Data\ProductAttachmentInterface;

/**
 * ProductAttachment Class ProductAttachmentRepository
 */
class ProductAttachmentRepository implements ProductAttachmentRepositoryInterface
{
    /**
     * @var ResourcePage
     */
    private ResourcePage $resource;

    /**
     * @var ProductAttachmentFactory
     */
    private ProductAttachmentFactory $productAttachmentFactory;

    /**
     * @var ProductAttachmentCollectionFactory
     */
    private ProductAttachmentCollectionFactory $productAttachmentCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ProductAttachmentSearchResultsInterfaceFactory
     */
    protected ProductAttachmentSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param ResourcePage $resource
     * @param ProductAttachmentFactory $productAttachmentFactory
     * @param ProductAttachmentCollectionFactory $productAttachmentCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param ProductAttachmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourcePage                       $resource,
        ProductAttachmentFactory           $productAttachmentFactory,
        ProductAttachmentCollectionFactory $productAttachmentCollectionFactory,
        DataObjectHelper                   $dataObjectHelper,
        DataObjectProcessor                $dataObjectProcessor,
        StoreManagerInterface              $storeManager,
        Data\ProductAttachmentSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->productAttachmentFactory = $productAttachmentFactory;
        $this->productAttachmentCollectionFactory = $productAttachmentCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(ProductAttachmentInterface $attachment): ProductAttachmentInterface
    {
        try {
            $this->resource->save($attachment);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the attachment: %1', $exception->getMessage()),
                $exception
            );
        }
        return $attachment;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $attachmentId): bool
    {
        return $this->delete($this->getById($attachmentId));
    }

    /**
     * @inheritdoc
     */
    public function delete(ProductAttachmentInterface $attachment): bool
    {
        try {
            $this->resource->delete($attachment);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the attachment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $attachmentId): ProductAttachmentInterface
    {
        $page = $this->productAttachmentFactory->create();
        $page->load($attachmentId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(
                __('Attachment with id "%1" does not exist.', $attachmentId)
            );
        }
        return $page;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->productAttachmentCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
