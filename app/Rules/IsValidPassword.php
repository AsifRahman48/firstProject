<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class IsValidPassword implements Rule
{
    /**
     * Determine if the Uppercase Validation Rule passes.
     *
     * @var boolean
     */
    public $uppercasePasses = true;

    /**
     * Determine if the Lowercase Validation Rule passes.
     *
     * @var boolean
     */
    public $lowercasePasses = true;

    /**
     * Determine if the Numeric Validation Rule passes.
     *
     * @var boolean
     */
    public $numericPasses = true;

    /**
     * Determine if the Special Character Validation Rule passes.
     *
     * @var boolean
     */
    public $specialCharacterPasses = true;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->uppercasePasses = (Str::lower($value) != $value);
        $this->lowercasePasses = (Str::upper($value) != $value);
        $this->numericPasses = ((bool) preg_match('/[0-9]/', $value));
        $this->specialCharacterPasses = ((bool) preg_match('/[^A-Za-z0-9]/', $value));

        return ($this->uppercasePasses && $this->numericPasses && $this->specialCharacterPasses && $this->lowercasePasses);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        switch (true) {
            case !$this->uppercasePasses && !$this->lowercasePasses && !$this->numericPasses && !$this->specialCharacterPasses:
                return 'The :attribute must be at least 8 characters and contain at least one uppercase character, one lowercase character, one number, and one special character.';

            case !$this->uppercasePasses:
                return 'The :attribute must be contain at least one uppercase character.';

            case !$this->numericPasses:
                return 'The :attribute must be contain at least one number.';

            case !$this->specialCharacterPasses:
                return 'The :attribute must be contain at least one special character.';

            case !$this->lowercasePasses:
                return 'The :attribute must be contain at least one lowercase character.';
        }
    }
}
