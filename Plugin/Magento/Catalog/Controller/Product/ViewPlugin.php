<?php
/**
 *
 */
declare(strict_types=1);

namespace FishPig\ForceSeoUrls\Plugin\Magento\Catalog\Controller\Product;

use Magento\Catalog\Controller\Product\View;

class ViewPlugin
{
    /**
     *
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Catalog\Model\ProductRepository $productRepo
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->productRepo = $productRepo;
    }
    
    /**
     * @param  View $subject
     * @param  \Closure $calback
     */
    public function aroundExecute(View $subject, \Closure $callback)
    {
        $find = '/catalog/product/view/';
        
        if (strpos($this->request->getOriginalPathInfo(), $find) !== 0) {
            return $callback();
        }
        
        try {
            $product = $this->productRepo->getById(
                (int)$this->request->getParam('id')
            );
            
            $productUrl = $product->getProductUrl();
            
            if (strpos($productUrl, $find) === 0) {
                throw new \Exception('Unable to get SEO URL for product.');
            }
            
            return $this->resultFactory->create(
                $this->resultFactory::TYPE_REDIRECT
            )->setUrl(
                $productUrl
            )->setHttpResponseCode(
                301
            );
        } catch (\Exception $e) {
            return $callback();
        }
    }
}
