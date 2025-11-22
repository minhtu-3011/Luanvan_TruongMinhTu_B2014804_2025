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

        // 1) Hỏi tổng số sản phẩm
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

            $languageId = 5; // ID ngôn ngữ muốn hiển thị

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








        // Chưa dùng LLM thì trả câu đơn giản
        return "Xin lỗi, Hiện tại mình chưa được cấu hình đầy đủ.";
    }
}
