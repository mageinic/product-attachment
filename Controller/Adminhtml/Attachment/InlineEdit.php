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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterfaceFactory as AttachmentFactory;

/**
 * Controller for InlineEdit Product Attachment.
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_ProductAttachment::mass_action';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var AttachmentFactory
     */
    private AttachmentFactory $repositoryFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param AttachmentFactory $repositoryFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context      $context,
        AttachmentFactory $repositoryFactory,
        JsonFactory  $jsonFactory
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelId) {
                    $model = $this->repositoryFactory->create()->getById($modelId);
                    try {
                        $data = array_replace($model->getData(), $postItems[$modelId]);
                        $data = array_filter(
                            $data,
                            function ($value) {
                                return $value !== '';
                            }
                        );
                        $model->setData($data);
                        $this->repositoryFactory->create()->save($model);
                    } catch (Exception $e) {
                        $messages[] = "[Attachment ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
