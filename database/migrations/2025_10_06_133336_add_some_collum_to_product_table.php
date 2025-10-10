<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Thêm 3 cột mới
            $table->decimal('price', 15, 2)->nullable()->after('product_catalogue_id'); // Giá sản phẩm
            $table->string('made_in')->nullable()->after('price'); // Nơi sản xuất
            $table->string('code')->nullable()->after('made_in'); // Mã sản phẩm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Xóa 3 cột khi rollback
            $table->dropColumn(['price', 'made_in', 'code']);
        });
    }
};
