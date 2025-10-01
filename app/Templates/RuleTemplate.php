<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\{Module};

class check{Module}ChildrenRule implements ValidationRule
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $flag = {Module}::isNodeCheck($this->id);
        if ($flag == false) {
            $fail('danh muc van con danh muc con');
        }
    }
}
