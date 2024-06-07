<?php
/**
 *
 */
namespace FishPig\ForceSeoUrls\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class CatalogProductObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     *
     */
    private $request = null;

    /**
     *
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->redirectSystemUrls($observer->getProduct());
    }

    /**
     *
     */
    private function redirectSystemUrls(?ProductInterface $product): void
    {
        if (!$product) {
            return;
        }

        // If this is not original path info, we get a redirect loop
        $pathInfo = $this->request->getOriginalPathInfo();

        if (strpos($pathInfo, '/catalog/product/view/') !== 0) {
            return;
        }

        $productUrl = $product->getProductUrl();

        if (strpos($productUrl, $pathInfo) === false) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $productUrl);
            exit;
        }
    }
}
