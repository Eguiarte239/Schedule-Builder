<?php

namespace App\Rules;

use App\Models\Task;
use Illuminate\Contracts\Validation\InvokableRule;

class EstimatedTaskHoursRule implements InvokableRule
{
    protected $phase_id;
    protected $hour_estimate;
    protected $modal;
    protected $current_estimate_hour;

    public function __construct($phase_id, $hour_estimate, $modal, $current_estimate_hour)
    {
        $this->phase_id = $phase_id;
        $this->hour_estimate = $hour_estimate;
        $this->modal = $modal;
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
        if($this->modal == true){
            if((Task::totalEstimatedHoursPerPhase($this->phase_id) - $this->current_estimate_hour + $value) > Task::phaseEstimatedHours($this->phase_id)){
                $fail('The :attribute must be less or equal than '.(Task::phaseEstimatedHours($this->phase_id) - (Task::totalEstimatedHoursPerPhase($this->phase_id) - $this->current_estimate_hour)).' for this phase'); 
            }
        }
        else if((Task::totalEstimatedHoursPerPhase($this->phase_id) + $this->hour_estimate) > Task::phaseEstimatedHours($this->phase_id) && $this->modal == false){
            $fail('The :attribute must be less or equal than '.(Task::phaseEstimatedHours($this->phase_id) - Task::totalEstimatedHoursPerPhase($this->phase_id)).' for this phase'); 
        }
    }
}
