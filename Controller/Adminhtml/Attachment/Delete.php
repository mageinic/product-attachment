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

use Exception;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface as AttachmentRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;

/**
 * Class ProductAttachment Delete
 */
class Delete extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_ProductAttachment::delete';

    /**
     * @var AttachmentRepositoryInterface
     */
    private AttachmentRepositoryInterface $attachmentRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AttachmentRepositoryInterface $attachmentRepository
     */
    public function __construct(
        Context                               $context,
        Registry                              $coreRegistry,
        AttachmentRepositoryInterface $attachmentRepository
    ) {
        parent::__construct($context);
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('attachment_id');

        if ($id) {
            try {
                $this->attachmentRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Product attachment has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['attachment_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Product Attachment to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
