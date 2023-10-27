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

namespace MageINIC\ProductAttachment\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageINIC\ProductAttachment\Model\ResourceModel\ProductAttachment;
use Magento\Framework\Exception\LocalizedException;

/**
 * ProductAttachment Class ProductAttachmentUpdateObserver
 */
class ProductAttachmentUpdateObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ProductAttachment
     */
    private ProductAttachment $productAttachment;

    /**
     * ProductAttachmentUpdateObserver constructor.
     * @param RequestInterface $request
     * @param ProductAttachment $productAttachment
     */
    public function __construct(
        RequestInterface  $request,
        ProductAttachment $productAttachment
    ) {
        $this->request = $request;
        $this->productAttachment = $productAttachment;
    }

    /**
     * Execute Action
     *
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer):ProductAttachmentUpdateObserver
    {
        $productId = $observer->getEvent()->getProduct()->getId();
        $productparams['links']['pattach'] = '';
        $productparams = $this->request->getParams();
        $productAttachmentIds = [];
        if (!empty($productparams['links']['pattach'])) {
            $productAttachments = $productparams['links']['pattach'];

            foreach ($productAttachments as $attachment) {
                $productAttachmentIds[] = $attachment['id'];
            }
        }
        $this->productAttachment->saveProductAttachmentRelation((array)$productAttachmentIds, $productId);

        return $this;
    }
}
