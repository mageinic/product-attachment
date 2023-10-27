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

namespace MageINIC\ProductAttachment\Block\Adminhtml\Attachment\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\UrlInterface;

/**
 *  ProductAttachment Main class
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected Store $systemStore;

    /**
     * Main constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function _prepareForm():Main
    {
        $id = $this->getRequest()->getParam('attachment_id');
        $model = $this->_coreRegistry->registry('mageinic_product_attachment');

        if ($this->_isAllowedAction('MageINIC_ProductAttachment::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('attachment_main_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Attachment Information')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'attachment_id',
                'hidden',
                ['name' => 'attachment_id']
            );
        }

        $file = '';
        if ($model->getUploadedFile() != '') {
            $file = $this->_storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $model->getUploadedFile();
        }

        $fieldset->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Active'),
                'title' => __('Active'),
                'value' => 1,
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Attachment Name'),
                'title' => __('Attachment Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled
            ]
        );

        if (empty($id)) {
            $fieldset->addField(
                'files',
                'file',
                [
                    'name' => 'uploaded_file',
                    'label' => __('File'),
                    'title' => __('File'),
                    'required' => true,
                    'value' => $file,
                    'note' => 'Accept Only Pdf,Txt,Csv,Doc,Pptx file type.'
                ]
            );
        } else {
            $fieldset->addField(
                'uploaded_file',
                'file',
                [
                    'name' => 'uploaded_file',
                    'label' => __('Uploaded File'),
                    'title' => __('Uploaded File'),
                    'value' => $model->getUploadedFile(),
                    'note' => $model->getUploadedFile()
                ]
            );
        }

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                Element::class
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_attachment_edit_tab_main_prepare_form', ['form' => $form]);
        if ($model->getId()) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction(string $resourceId): bool
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel(): string
    {
        return __('Attachment Information');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle(): string
    {
        return __('Attachment Information');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden(): bool
    {
        return false;
    }
}
