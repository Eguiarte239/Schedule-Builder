<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class CurrentEstimatedHoursRule implements InvokableRule
{
    protected $current_estimate_hour;

    public function __construct($current_estimate_hour)
    {
        $this->current_estimate_hour = $current_estimate_hour;
    }
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if($value < $this->current_estimate_hour){
            $fail("The :attribute can't be less than ".$this->current_estimate_hour);
        }
    }
}
