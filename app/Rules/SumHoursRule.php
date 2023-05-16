<?php

namespace App\Rules;

use App\Models\Phase;
use Illuminate\Contracts\Validation\Rule;

class SumHoursRule implements Rule
{
    protected $phase;
    protected $total_hour_estimate;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($phase, $total_hour_estimate)
    {
        $this->phase = $phase;
        $this->total_hour_estimate = $total_hour_estimate;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->phase){
            $this->total_hour_estimate = Phase::where('project_id', $this->phase->project->id)->where('id', '<>', $this->phase->id)->sum('hour_estimate');
            return $value <= $this->total_hour_estimate;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No wachin';
    }
}
