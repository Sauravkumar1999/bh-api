<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProductNumberFormatRule implements Rule
{
    /**
     * @var string
     */
    private string $pattern;
    protected string $attribute;

    /**
     * Create a new rule instance.
     *
     * @param string|null $pattern
     * @return void
     */
    public function __construct(?string $pattern = null)
    {
        $this->pattern = $pattern ?? '/^\d{1,3}(,\d{3})*$/';
    }


    /**
     * Set a custom pattern.
     *
     * @param string $pattern
     * @return $this
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return preg_match($this->pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.product_number_format_rule', ['attribute' => $this->attribute]);

    }
}
