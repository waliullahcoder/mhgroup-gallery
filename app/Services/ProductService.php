<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Product;
use App\Services\Utility\ProductUtility;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function store(array $data)
    {
        $collection = collect($data);

        $slug = HelperClass::generateUniqueSlug(Product::class, 'slug', $collection['name']);

        $discount_start_date = null;
        $discount_end_date   = null;
        if ($collection['date_range'] != null) {
            $date_var            = explode(" to ", $collection['date_range']);
            $discount_start_date = date('Y-m-d', strtotime($date_var[0]));
            $discount_end_date   = date('Y-m-d', strtotime($date_var[1]));
        }
        unset($collection['date_range']);

        if ($collection['meta_title'] == null) {
            $collection['meta_title'] = $collection['name'];
        }
        if ($collection['meta_description'] == null) {
            $collection['meta_description'] = strip_tags($collection['description']);
        }

        $file = isset($collection['file']) ? HelperClass::saveImage($collection['file'], 800, 'media/product') : null;
        unset($collection['file']);

        $thumbnail = isset($collection['thumbnail']) ? HelperClass::saveImage($collection['thumbnail'], 800, 'media/product') : null;
        unset($collection['thumbnail']);

        $meta_image = isset($collection['meta_image']) ? HelperClass::saveImage($collection['meta_image'], 800, 'media/product') : $thumbnail;
        unset($collection['meta_image']);

        $options = ProductUtility::get_attribute_options($collection);

        $combinations = HelperClass::makeCombinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);

                unset($collection['price_' . str_replace('.', '_', $str)]);
                unset($collection['sku_' . str_replace('.', '_', $str)]);
                unset($collection['img_' . str_replace('.', '_', $str)]);
            }
        }

        $regular_price = request('regular_price');
        $product_type = request('product_type');
        $sale_price = request('regular_price');
        if (request('discount_type') == 'percent' && request('discount') > 0) {
            $discount = (request('discount') / 100) * $regular_price;
            $sale_price = $regular_price - $discount;
        }
        if (request('discount_type') == 'amount' && request('discount') > 0) {
            $sale_price = $regular_price - request('discount');
        }
        unset($collection['sale_price']);

        $custom_barcode = isset($collection['custom_barcode']) ? true : false;
        unset($collection['custom_barcode']);
        $favorite = isset($collection['favorite']) ? true : false;
        unset($collection['favorite']);
        $new_arrival = isset($collection['new_arrival']) ? true : false;
        unset($collection['new_arrival']);
        $best_seller = isset($collection['best_seller']) ? true : false;
        unset($collection['best_seller']);
        $trending = isset($collection['trending']) ? true : false;
        unset($collection['trending']);

        $created_by = Auth::id();

        $data = $collection->merge(compact(
            'regular_price',
            'sale_price',
            'discount_start_date',
            'discount_end_date',
            'product_type',
            'slug',
            'file',
            'thumbnail',
            'meta_image',
            'custom_barcode',
            'favorite',
            'new_arrival',
            'best_seller',
            'trending',
            'created_by'
        ))->toArray();

        return Product::create($data);
    }

    public function update(array $data, Product $product)
    {
        $collection = collect($data);

        $slug = HelperClass::generateUniqueSlug(Product::class, 'slug', $collection['name'], $product->id);

        $discount_start_date = null;
        $discount_end_date   = null;
        if ($collection['date_range'] != null) {
            $date_var            = explode(" to ", $collection['date_range']);
            $discount_start_date = date('Y-m-d', strtotime($date_var[0]));
            $discount_end_date   = date('Y-m-d', strtotime($date_var[1]));
        }
        unset($collection['date_range']);

        if ($collection['meta_title'] == null) {
            $collection['meta_title'] = $collection['name'];
        }
        if ($collection['meta_description'] == null) {
            $collection['meta_description'] = strip_tags($collection['description']);
        }

        $file = isset($collection['file']) ? HelperClass::saveImage($collection['file'], 800, 'media/product', $product->file) : $product->file;
        unset($collection['file']);

        $thumbnail = isset($collection['thumbnail']) ? HelperClass::saveImage($collection['thumbnail'], 800, 'media/product', $product->thumbnail) : $product->thumbnail;
        unset($collection['thumbnail']);

        if ($product->thumbnail == $product->meta_image && !isset($collection['meta_image'])) {
            $meta_image = $thumbnail;
        } else {
            $meta_image = isset($collection['meta_image']) ? HelperClass::saveImage($collection['meta_image'], 800, 'media/product', $product->meta_image) : $product->meta_image;
        }
        unset($collection['meta_image']);

        $options = ProductUtility::get_attribute_options($collection);

        $combinations = HelperClass::makeCombinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);

                unset($collection['price_' . str_replace('.', '_', $str)]);
                unset($collection['sku_' . str_replace('.', '_', $str)]);
                unset($collection['qty_' . str_replace('.', '_', $str)]);
                unset($collection['img_' . str_replace('.', '_', $str)]);
            }
        }

        $regular_price = request('regular_price');
        $product_type = request('product_type');
        $sale_price = request('regular_price');
        if (request('discount_type') == 'percent' && request('discount') > 0) {
            $discount = (request('discount') / 100) * $regular_price;
            $sale_price = $regular_price - $discount;
        }
        if (request('discount_type') == 'amount' && request('discount') > 0) {
            $sale_price = $regular_price - request('discount');
        }
        unset($collection['sale_price']);

        $custom_barcode = isset($collection['custom_barcode']) ? true : false;
        unset($collection['custom_barcode']);
        $favorite = isset($collection['favorite']) ? true : false;
        unset($collection['favorite']);
        $new_arrival = isset($collection['new_arrival']) ? true : false;
        unset($collection['new_arrival']);
        $best_seller = isset($collection['best_seller']) ? true : false;
        unset($collection['best_seller']);
        $trending = isset($collection['trending']) ? true : false;
        unset($collection['trending']);

        $updated_by = Auth::id();

        $data = $collection->merge(compact(
            'regular_price',
            'sale_price',
            'discount_start_date',
            'discount_end_date',
            'product_type',
            'slug',
            'file',
            'thumbnail',
            'meta_image',
            'custom_barcode',
            'favorite',
            'new_arrival',
            'best_seller',
            'trending',
            'updated_by'
        ))->toArray();

        $product->update($data);

        return $product;
    }
}
