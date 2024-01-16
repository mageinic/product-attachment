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

namespace MageINIC\ProductAttachment\Block;

use MageINIC\ProductAttachment\ViewModel\ProductAttachment;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class CustomTab extends Template
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var ProductAttachment
     */
    private ProductAttachment $productAttachment;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param ProductAttachment $productAttachment
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        ProductAttachment $productAttachment,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->productAttachment = $productAttachment;
    }

    /**
     * Get to Html
     *
     * @return string|void
     * @throws LocalizedException
     */
    public function toHtml()
    {
        $collection = $this->productAttachment->getProductAttachmentCollection($this->getProduct());
        if ($collection) {
            return parent::toHtml();
        }
    }

    /**
     * Get Current Product
     *
     * @return mixed|null
     */
    public function getProduct(): mixed
    {
        return $this->registry->registry('current_product');
    }
}

