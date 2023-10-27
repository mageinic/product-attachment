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

namespace MageINIC\ProductAttachment\Helper;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * ProductAttachment Data helper
 */
class Data extends AbstractHelper
{
    /**
     * ProductAttachment config path
     */
    public const ENABLE_PATH = 'productattachment/general/enable';
    public const TAB_NAME_PATH = 'productattachment/general/tabname';
    public const FILE_UPLOAD_VALIDATION_PATH = 'productattachment/general/file_size_validation';

    /**
     * @var AbstractProduct
     */
    protected AbstractProduct $product;

    /**
     * @var UrlInterface
     */
    private UrlInterface $backendUrl;

    /**
     * @param Context $context
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        Context         $context,
        UrlInterface    $backendUrl,
    ) {
        $this->backendUrl = $backendUrl;
        parent::__construct($context);
    }

    /**
     * Get Product Grid Url
     *
     * @return string
     */
    public function getProductsGridUrl(): string
    {
        return $this->backendUrl->getUrl(
            'pattach/attachment/products',
            ['_current' => true]
        );
    }

    /**
     * Get Tab Name
     *
     * @return mixed
     */
    public function getTabName(): mixed
    {
        if ($this->isModuleEnable()) {
            return $this->scopeConfig->getValue(
                self::TAB_NAME_PATH,
                ScopeInterface::SCOPE_STORE
            );
        }
    }

    /**
     * Module Enable Action
     *
     * @return mixed
     */
    public function isModuleEnable(): mixed
    {
        return $this->scopeConfig->getValue(
            self::ENABLE_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get File Size Validation
     *
     * @return mixed
     */
    public function getUploadSize(): mixed
    {
        return $this->scopeConfig->getValue(
            self::FILE_UPLOAD_VALIDATION_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Config
     *
     * @param mixed $config
     * @return mixed
     */
    public function getConfig(mixed $config): mixed
    {
        return $this->scopeConfig->getValue(
            $config,
            ScopeInterface::SCOPE_STORE
        );
    }
}
