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
use Magento\Backend\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\MediaStorage\Model\File\UploaderFactory;
use MageINIC\ProductAttachment\Model\ProductAttachmentRepository;
use MageINIC\ProductAttachment\Model\ProductAttachmentFactory;
use MageINIC\ProductAttachment\Helper\Data;

/**
 * ProductAttachment Class Save
 */
class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'MageINIC_ProductAttachment::save';

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ProductAttachmentFactory
     */
    private ProductAttachmentFactory $productAttachmentFactory;

    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var UploaderFactory
     */
    private UploaderFactory $fileUploaderFactory;

    /**
     * @var ProductAttachmentRepository
     */
    private ProductAttachmentRepository $productAttachmentRepository;

    /**
     * @var Data
     */
    private Data $data;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ProductAttachmentFactory $productAttachmentFactory
     * @param Filesystem $filesystem
     * @param Session $session
     * @param UploaderFactory $fileUploaderFactory
     * @param Request $request
     * @param ProductAttachmentRepository $productAttachmentRepository
     * @param Data $data
     */
    public function __construct(
        Action\Context              $context,
        DataPersistorInterface      $dataPersistor,
        ProductAttachmentFactory    $productAttachmentFactory,
        Filesystem                  $filesystem,
        Session                     $session,
        UploaderFactory             $fileUploaderFactory,
        Request                     $request,
        ProductAttachmentRepository $productAttachmentRepository,
        Data $data
    ) {
        $this->fileSystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->session = $session;
        $this->dataPersistor = $dataPersistor;
        $this->productAttachmentFactory = $productAttachmentFactory;
        $this->request = $request;
        $this->productAttachmentRepository = $productAttachmentRepository;
        parent::__construct($context);
        $this->data = $data;
    }

    /**
     * Save Execute Action
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute(): Redirect
    {
        $files = $this->request->getFiles()->toArray();
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $this->productAttachmentFactory->create();
            $id = $this->getRequest()->getParam('attachment_id');

            if ($id) {
                $this->productAttachmentRepository->getById($id);
            }
            if ($image = $this->uploadImage('uploaded_file')) {
                $data['uploaded_file'] = $image;
            }
            $model->setData($data);
            $this->_eventManager->dispatch(
                'mageINIC_productAttachments_prepare_save',
                ['productAttachment' => $model, 'request' => $this->getRequest()]
            );
            try {
                if (!isset($data['uploaded_file']) && !empty($files['uploaded_file']['name'])) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['attachment_id' => $model->getId(), '_current' => true]
                    );
                }
                $this->productAttachmentRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved this Product attachment.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['attachment_id' => $model->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager
                    ->addExceptionMessage($e, __('Something went wrong while saving the product attachment.'));
            }
            $this->_getSession()->setFormData($data);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Upload Image Action
     *
     * @param string $fieldId
     * @return bool
     * @throws Exception
     */
    public function uploadImage(string $fieldId = 'uploaded_file')
    {
        $files = $this->request->getFiles()->toArray();
        if (isset($files[$fieldId]) && $files[$fieldId]['name'] != '') {
            $uploader = $this->fileUploaderFactory->create(['fileId' => $fieldId]);
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'mageINIC/product_attachments/';
            try {
                $result = '';
                $uploader->setAllowedExtensions(['pdf', 'pptx', 'doc', 'csv', 'txt']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $config = $this->data->getUploadSize();
                $maxFileSize = $config*1024*1024;
                if ($files[$fieldId]['size'] > $maxFileSize) {
                    $this->messageManager
                        ->addErrorMessage(__('File size is too large. Maximum file size is ' . $config . ' MB.'));

                    return $result;
                }
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder));
                return $result['file'];
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return false;
            }
        }
    }
}
