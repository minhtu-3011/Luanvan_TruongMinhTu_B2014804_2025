<?php

namespace App\Rules\Promotion;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderAmountRangeRule implements ValidationRule
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $amountFrom = $this->data['amountFrom'] ?? [];
        $amountTo = $this->data['amountTo'] ?? [];
        $amountValue = $this->data['amountValue'] ?? [];

        // ✅ Kiểm tra dữ liệu đầu vào
        if (
            !is_array($amountFrom) || !is_array($amountTo) || !is_array($amountValue) ||
            count($amountFrom) == 0 ||
            ($amountFrom[0] ?? '') === ''
        ) {
            $fail('Bạn phải khởi tạo giá trị cho khoảng khuyến mại');
            return;
        }

        // ✅ Kiểm tra giá trị hợp lệ
        if (in_array(0, $amountValue, true) || in_array('', $amountValue, true)) {
            $fail('Cấu hình giá trị khuyến mãi không hợp lệ');
            return;
        }

        // ✅ Kiểm tra xung đột khoảng giá trị
        $conflict = false;
        for ($i = 0; $i < count($amountFrom); $i++) {
            $amountFrom1 = convert_price($amountFrom[$i]);
            $amountTo1 = convert_price($amountTo[$i]);

            if ($amountFrom1 >= $amountTo1) {
                $conflict = true;
                break;
            }

            for ($j = 0; $j < count($amountFrom); $j++) {
                if ($i !== $j) {
                    $amountFrom2 = convert_price($amountFrom[$j]);
                    $amountTo2 = convert_price($amountTo[$j]);
                    if ($amountFrom1 <= $amountTo2 && $amountTo1 >= $amountFrom2) {
                        $conflict = true;
                        break 2;
                    }
                }
            }
        }

        if ($conflict) {
            $fail('Có xung đột giữa các khoảng giá trị khuyến mại! Hãy kiểm tra và thử lại');
        }
    }
}
