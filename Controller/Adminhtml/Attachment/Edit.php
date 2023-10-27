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

namespace MageINIC\ProductAttachment\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageINIC\ProductAttachment\Model\ProductAttachmentFactory;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface as AttachmentRepositoryInterface;

/**
 * Class ProductAttachment Edit
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_ProductAttachment::edit';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var Registry
     */
    private Registry $coreRegistry;

    /**
     * @var AttachmentRepositoryInterface
     */
    private AttachmentRepositoryInterface $attachmentsRepository;

    /**
     * @var ProductAttachmentFactory
     */
    private ProductAttachmentFactory $productAttachmentFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ProductAttachmentFactory $productAttachmentFactory
     * @param AttachmentRepositoryInterface $attachmentsRepository
     */
    public function __construct(
        Context                       $context,
        Registry                      $coreRegistry,
        PageFactory                   $resultPageFactory,
        ProductAttachmentFactory      $productAttachmentFactory,
        AttachmentRepositoryInterface $attachmentsRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productAttachmentFactory= $productAttachmentFactory;
        $this->attachmentsRepository = $attachmentsRepository;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit Action
     *
     * @return Page|Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(): Redirect|Page
    {
        $id = $this->getRequest()->getParam('attachment_id');
        $model = $this->productAttachmentFactory->create();
        if ($id) {
            $model = $this->attachmentsRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Product Attachment no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('mageinic_product_attachment', $model);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Attachment') : __('New Attachment'),
            $id ? __('Edit Attachment') : __('New Attachment')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Attachment'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Attachment')
        );

        return $resultPage;
    }

    /**
     * Init actions
     *
     * @return Page
     */
    protected function _initAction(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageINIC_ProductAttachment::product_attachment')
            ->addBreadcrumb(__('ProductAttachment'), __('ProductAttachment'))
            ->addBreadcrumb(__('Manage ProductAttachment'), __('Manage ProductAttachment'));
        return $resultPage;
    }
}
