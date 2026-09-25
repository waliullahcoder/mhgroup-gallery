<?php

namespace App\Services;

use App\HelperClass;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use App\Services\Utility\ProductUtility;

class ProductVariantService
{
    public function store(array $data, $product)
    {
        $collection = collect($data);

        ProductVariant::where('product_id', $product->id)->update(['status' => false]);
        // Step 1: Generate combinations and clear existing variant-related data
        $combinations = $this->getCombinations($collection);
        $this->clearOldVariants($product, $combinations);

        // Step 2: If combinations exist, update or create variants
        if (count($combinations[0]) > 0) {
            $product->save();
            foreach ($combinations as $combination) {
                $this->handleVariant($combination, $product, $collection);
            }
        } else {
            $variant = ProductVariant::where('product_id', $product->id)->first();
            $regular_price = request('regular_price');
            $sale_price = request('regular_price');
            if (request('discount_type') == 'percent' && request('discount') > 0) {
                $discount = (request('discount') / 100) * $regular_price;
                $sale_price = $regular_price - $discount;
            }
            if (request('discount_type') == 'amount' && request('discount') > 0) {
                $sale_price = $regular_price - request('discount');
            }

            if (!is_null($variant)) {
                $variant->update([
                    'variant' => null,
                    'sku' => request()['sku'],
                    'purchase_price' => request()['purchase_price'],
                    'regular_price' => $regular_price,
                    'discount_type' => request('discount_type'),
                    'discount' => request('discount'),
                    'sale_price' => $sale_price,
                    'status' => true,
                ]);
            } else {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant' => null,
                    'sku' => request()['sku'],
                    'purchase_price' => request()['purchase_price'],
                    'regular_price' => $regular_price,
                    'discount_type' => request('discount_type'),
                    'discount' => request('discount'),
                    'sale_price' => $sale_price,
                    'status' => true,
                ]);
            }
        }
    }

    private function getCombinations($collection)
    {
        $options = ProductUtility::get_attribute_options($collection);
        return HelperClass::makeCombinations($options);
    }

    private function clearOldVariants($product, $combinations)
    {
        $images = $product->variants->pluck('image')->toArray();

        // Clear old variant data
        ProductVariant::where('product_id', $product->id)->update(['status' => false]);
        ProductVariantValue::where('product_id', $product->id)->delete();

        // Delete images if no combinations
        if (empty($combinations[0])) {
            foreach ($images as $item) {
                if (file_exists($item)) {
                    unlink($item);
                }
            }
        }
    }

    private function handleVariant($combination, $product, $collection)
    {
        $str = ProductUtility::get_combination_string($combination, $collection);
        $variant = ProductVariant::where('product_id', $product->id)->where('variant', $str)->first();

        // Calculate regular and sale price
        $regular_price = $this->calculatePrice($str, 'regular_price');
        $sale_price = $this->calculateSalePrice($regular_price);

        // Upsert variant
        $variant = $variant ? $this->updateVariant($variant, $regular_price, $sale_price, $str)
            : $this->createVariant($product, $regular_price, $sale_price, $str);

        // Handle variant attributes and images
        $this->handleAttributes($combination, $variant, $product, $collection);
    }

    private function calculatePrice($str, $type)
    {
        return request()->get($type . '_' . str_replace('.', '_', $str), 0);
    }

    private function calculateSalePrice($regular_price)
    {
        $sale_price = $regular_price;
        $discount_type = request('discount_type');
        $discount = request('discount');

        if ($discount_type == 'percent' && $discount > 0) {
            $sale_price = $regular_price - ($discount / 100) * $regular_price;
        } elseif ($discount_type == 'amount' && $discount > 0) {
            $sale_price = $regular_price - $discount;
        }

        return $sale_price;
    }

    private function updateVariant($variant, $regular_price, $sale_price, $str)
    {
        $imagePath = $this->getImagePath($str);

        // Update the variant with new data
        return $variant->update([
            'purchase_price' => request('purchase_price_' . str_replace('.', '_', $str)),
            'regular_price' => $regular_price,
            'discount_type' => request('discount_type'),
            'discount' => request('discount'),
            'sale_price' => $sale_price,
            'image' => $imagePath,
            'status' => true,
        ]) ? $variant : null;
    }

    private function createVariant($product, $regular_price, $sale_price, $str)
    {
        $imagePath = $this->getImagePath($str);

        // Create a new variant if it doesn't exist
        return ProductVariant::create([
            'product_id' => $product->id,
            'variant' => $str,
            'sku' => $str,
            'purchase_price' => request('purchase_price_' . str_replace('.', '_', $str)),
            'regular_price' => $regular_price,
            'discount_type' => request('discount_type'),
            'discount' => request('discount'),
            'sale_price' => $sale_price,
            'image' => $imagePath,
            'status' => true,
        ]);
    }

    private function getImagePath($str)
    {
        return isset(request()['variant_img_' . str_replace('.', '_', $str)])
            ? HelperClass::saveImage(request()['variant_img_' . str_replace('.', '_', $str)], 800, 'media/product', request()['old_variant_img_' . str_replace('.', '_', $str)])
            : request()['old_variant_img_' . str_replace('.', '_', $str)];
    }

    private function handleAttributes($combination, $variant, $product, $collection)
    {
        foreach ($combination as $key => $attr) {
            $attribute_value = AttributeValue::findOrFail($attr);
            ProductVariantValue::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'attribute_id' => $attribute_value->attribute_id,
                'attribute_value_id' => $attr
            ]);
        }
    }
}
