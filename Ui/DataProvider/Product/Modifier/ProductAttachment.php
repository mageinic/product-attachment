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

namespace MageINIC\ProductAttachment\Ui\DataProvider\Product\Modifier;

use MageINIC\ProductAttachment\Api\ProductAttachmentRepositoryInterface as AttachmentRepository;
use MageINIC\ProductAttachment\Model\ResourceModel\Product\CollectionFactory;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory
    as ProductAttachmentCollectionFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;

/**
 * Modify product listing ProductAttachment
 */
class ProductAttachment extends AbstractModifier
{
    public const DATA_SCOPE = '';
    public const DATA_SCOPE_PRODUCTATTACHMENT = 'pattach';
    public const GROUP_PRODUCTATTACHMENT = 'pattach';

    /**
     * @var string
     */
    private static string $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static int $sortOrder = 150;

    /**
     * @var LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var string
     */
    private string $scopeName;

    /**
     * @var ProductAttachmentCollectionFactory
     */
    private ProductAttachmentCollectionFactory $productAttachmentCollection;

    /**
     * @var string
     */
    private string $scopePrefix;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $_storeManager;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productAttachmentRelationCollection;

    /**
     * @var AttachmentRepository
     */
    private AttachmentRepository $attachmentRepository;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param AttachmentRepository $attachmentRepository
     * @param ProductAttachmentCollectionFactory $productAttachmentCollection
     * @param CollectionFactory $productAttachmentRelationCollection
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface      $locator,
        UrlInterface          $urlBuilder,
        StoreManagerInterface $storeManager,
        AttachmentRepository  $attachmentRepository,
        ProductAttachmentCollectionFactory $productAttachmentCollection,
        CollectionFactory                  $productAttachmentRelationCollection,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->productAttachmentCollection = $productAttachmentCollection;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->productAttachmentRelationCollection = $productAttachmentRelationCollection;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_PRODUCTATTACHMENT => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT => $this
                            ->getProductAttachmentFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Add Product Attachment'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $meta,
                                    self::$previousGroup,
                                    self::$sortOrder
                                )
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Get Product Attachment Fieldset
     *
     * @return array
     */
    public function getProductAttachmentFieldset(): array
    {
        $content = __('Product attachments used to provide product related documents.');
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Attachment'),
                    $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT
                ),
                'modal' => $this->getGenericModal(
                    __('Add Attachment'),
                    $this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT
                ),
                static::DATA_SCOPE_PRODUCTATTACHMENT => $this
                    ->getGrid($this->scopePrefix . static::DATA_SCOPE_PRODUCTATTACHMENT),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Product Attachments'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    public function getButtonSet(Phrase $content, Phrase $buttonTitle, string $scope): array
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_PRODUCTATTACHMENT . '.' . $scope . '.modal';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ]
                ]
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_attach_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    public function getGenericModal(Phrase $title, string $scope): array
    {
        $listingTarget = $scope . '_attach_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Attachments'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.'
                                    . $listingTarget . '.pattach_attach_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => ['imports' => false, 'exports' => true],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_attachment_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_attachment_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     */
    public function getGrid(string $scope): array
    {
        $dataProvider = $scope . '_attach_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'attachment_id',
                            'description' => 'description',
                            'file' => 'uploaded_file',
                            'name' => 'name',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false],
                        ],
                        'sortOrder' => 2,
                    ]
                ]
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ]
                        ]
                    ],
                    'children' => $this->fillMeta(),
                ]
            ]
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    public function fillMeta(): array
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'description' => $this->getTextColumn('description', false, __('Description'), 3),
            'file' => $this->getTextColumn('file', false, __('File'), 2),
            'name' => $this->getTextColumn('name', false, __('Name'), 1),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn(string $dataScope, bool $fit, Phrase $label, int $sortOrder): array
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ]
                ]
            ]
        ];

        return $column;
    }

    /**
     * @inheritdoc
     */
    public function modifyData($data): array
    {
        /** @var Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if (!$productId) {
            return $data;
        }

        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            $productAttachmentRelationCollection = $this->productAttachmentRelationCollection->create()
                ->addFieldToFilter('product_id', ['eq' => $productId]);
            foreach ($productAttachmentRelationCollection as $prodColl) {
                $data[$productId]['links'][$dataScope][] = $this->fillData($prodColl['attachment_id'], $productId);
                $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();
            }
        }
        return $data;
    }

    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    public function getDataScopes(): array
    {
        return [
            static::DATA_SCOPE_PRODUCTATTACHMENT,
        ];
    }

    /**
     * Fill data column
     *
     * @param string $productattachmentIds
     * @param int $product
     * @return array
     */
    public function fillData(string $productattachmentIds, int $product): array
    {
        $productAttachmentCollection = $this->productAttachmentCollection->create()
            ->addFieldToFilter('attachment_id', ['eq' => $productattachmentIds])
            ->getFirstItem();
        return [
            'id' => $productAttachmentCollection->getId(),
            'description' => $productAttachmentCollection->getDescription(),
            'file' => $productAttachmentCollection->getUploadedFile(),
            'name' => $productAttachmentCollection->getName(),
        ];
    }
}
