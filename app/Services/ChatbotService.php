<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductCatalogue;

class ChatbotService
{
    public function answer(string $text)
    {
        $lower = mb_strtolower(trim($text));


        if (preg_match('/^$/', $lower)) {
            return "Chào bạn! Mình là trợ lý nội bộ của website. Bạn muốn hỏi gì về sản phẩm hoặc danh mục?";
        }


        if (preg_match('/bao nhiêu sản phẩm|tổng sản phẩm|hiện có bao nhiêu/i', $lower)) {

            // Cache 30 giây cho nhẹ DB
            $total = Cache::remember('dashboard_total_products', 30, function () {
                return Product::where('publish', 1)->count();
            });

            return "Hiện có tổng cộng $total sản phẩm đang được bán.";
        }

        if (preg_match('/bao nhiêu danh mục|tổng danh mục|có bao nhiêu loại sản phẩm/i', $lower)) {
            $totalCat = Cache::remember('dashboard_total_categories', 30, function () {
                return ProductCatalogue::where('publish', 1)->count();
            });
            return "Hiện có tổng cộng $totalCat danh mục sản phẩm.";
        }

        if (preg_match('/các danh mục sản phẩm|tên danh mục|danh sách danh mục/i', $lower)) {

            $languageId = 5;

            $categories = ProductCatalogue::where('publish', 1)
                ->with(['product_catalogue_language' => function ($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->get()
                ->pluck('product_catalogue_language.*.name') // lấy tên
                ->flatten()
                ->filter()   // bỏ null
                ->unique();  // loại trùng

            if ($categories->isEmpty()) return "Hiện tại chưa có danh mục nào.";

            $list = $categories->implode(', ');
            return "Các danh mục sản phẩm hiện có: $list.";
        }


        if (preg_match('/công nghệ gồm các loại sản phẩm|công nghệ có những loại gì/i', $lower)) {

            $languageId = 5; // tiếng Việt

            // Tìm id danh mục cha theo tên
            $parent = ProductCatalogue::whereHas('product_catalogue_language', function ($q) use ($languageId) {
                $q->where('name', 'Công nghệ')->where('language_id', $languageId);
            })->first();

            if ($parent) {
                $subCategories = ProductCatalogue::where('parent_id', $parent->id)
                    ->with(['product_catalogue_language' => function ($q) use ($languageId) {
                        $q->where('language_id', $languageId);
                    }])
                    ->get()
                    ->pluck('product_catalogue_language.*.name')
                    ->flatten()
                    ->filter()
                    ->unique();

                $list = $subCategories->implode(', ');
                return "Các loại sản phẩm trong danh mục Công nghệ: $list";
            }
            return "Không tìm thấy danh mục Công nghệ.";
        }
        // Sản phẩm đang được giảm giá
        if (preg_match('/sản phẩm nào.*giảm giá|đang được giảm giá|có khuyến mãi/i', $lower)) {

            $products = \App\Models\Product::whereHas('promotions', function ($q) {
                $q->where('publish', 1)
                    ->where(function ($qq) {
                        $qq->whereNull('endDate')
                            ->orWhere('endDate', '>=', now());
                    })
                    ->where(function ($qq) {
                        $qq->whereNull('startDate')
                            ->orWhere('startDate', '<=', now());
                    });
            })
                ->with(['languages' => function ($q) {
                    $q->where('language_id', 5);
                }])
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return "Hiện tại chưa có sản phẩm nào đang được giảm giá.";
            }

            $names = $products->map(function ($p) {
                return "- " . ($p->languages->first()->pivot->name ?? 'Không tên');
            })->implode("\n");

            return "Các sản phẩm đang được giảm giá:\n" . $names;
        }

        if (preg_match('/các sản phẩm (.+) (đang sale|đang giảm giá|giảm giá|khuyến mãi)/i', $lower, $m)) {

            $categoryName = trim($m[1]); // laptop / máy giặt / ...
            $languageId = 5;


            $category = ProductCatalogue::whereHas('product_catalogue_language', function ($q) use ($categoryName, $languageId) {
                $q->where('language_id', $languageId)
                    ->where('name', 'like', '%' . $categoryName . '%');
            })->first();

            if (!$category) {
                return "Mình không tìm thấy danh mục <b>{$categoryName}</b>.";
            }

            $products = Product::where('publish', 1)
                ->whereHas('product_catalogues', function ($q) use ($category) {
                    $q->where('product_catalogues.id', $category->id);
                })
                ->whereHas('promotions', function ($q) {
                    $q->where('publish', 1)
                        ->where(function ($qq) {
                            $qq->whereNull('startDate')
                                ->orWhere('startDate', '<=', now());
                        })
                        ->where(function ($qq) {
                            $qq->whereNull('endDate')
                                ->orWhere('endDate', '>=', now());
                        });
                })
                ->with(['languages' => function ($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return "Danh mục <b>{$categoryName}</b> hiện không có sản phẩm nào đang giảm giá.";
            }


            $list = $products->map(function ($p) {
                $lang = $p->languages->first();
                if (!$lang) return null;

                $name = $lang->pivot->name;
                $canonical = $lang->pivot->canonical;

                return '- <a href="' . url($canonical . '.html') . '" target="_blank">'
                    . e($name)
                    . '</a>';
            })->filter()->implode('<br>');

            return "Các sản phẩm <b>{$categoryName}</b> đang giảm giá:<br>{$list}";
        }



        if (preg_match('/các sản phẩm (.+)/i', $lower, $m)) {

            $categoryName = trim($m[1]);   // laptop / máy giặt / ...
            $languageId = 5;


            $category = ProductCatalogue::whereHas('product_catalogue_language', function ($q) use ($categoryName, $languageId) {
                $q->where('language_id', $languageId)
                    ->where('name', 'like', '%' . $categoryName . '%');
            })->first();

            if (!$category) {
                return "Mình không tìm thấy danh mục \"$categoryName\".";
            }


            $products = Product::where('publish', 1)
                ->whereHas('product_catalogues', function ($q) use ($category) {
                    $q->where('product_catalogues.id', $category->id);
                })
                ->with(['languages' => function ($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return "Danh mục \"$categoryName\" hiện chưa có sản phẩm nào.";
            }

            $list = $products->map(function ($p) {
                $lang = $p->languages->first();
                if (!$lang) return null;
                $name = $lang->pivot->name;
                $canonical = $lang->pivot->canonical;
                return '- <a href="' . url($canonical . '.html') . '" target="_blank">'
                    . e($name)
                    . '</a>';
            })->filter()->implode('<br>');

            return "Các sản phẩm thuộc danh mục <b>{$categoryName}<br>" . $list;
        }




        return "Xin lỗi, Hiện tại mình chưa được cấu hình đầy đủ.";
    }
}
