<?php

namespace App\Rules;

use App\Models\Phase;
use Illuminate\Contracts\Validation\InvokableRule;

class EstimatedPhaseHoursRule implements InvokableRule
{
    protected $project_id;
    protected $hour_estimate;
    protected $modal;
    protected $current_estimate_hour;

    public function __construct($project_id, $hour_estimate, $modal, $current_estimate_hour)
    {
        $this->project_id = $project_id;
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
            if((Phase::totalEstimatedHoursPerProject($this->project_id) - $this->current_estimate_hour + $value) > Phase::projectEstimatedHours($this->project_id)){
                $fail('The :attribute must be less or equal than '.(Phase::projectEstimatedHours($this->project_id) - (Phase::totalEstimatedHoursPerProject($this->project_id) - $this->current_estimate_hour)).' for this project'); 
            }
        }
        else if((Phase::totalEstimatedHoursPerProject($this->project_id) + $this->hour_estimate) > Phase::projectEstimatedHours($this->project_id) && $this->modal == false){
            $fail('The :attribute must be less or equal than '.(Phase::projectEstimatedHours($this->project_id) - Phase::totalEstimatedHoursPerProject($this->project_id)).' for this project'); 
        }
    }
}
