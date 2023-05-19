<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'hour_estimate',
        'start_date',
        'end_date',
        'priority',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_assigned');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function predecessorTask()
    {
        return $this->belongsTo(Task::class, 'predecessor_task');
    }

    /**
     * Return a string representation of the start time of a task.
     *
     * @return string
     */
    public function getStartTaskAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->start_date)->format('l jS \of F Y');
    }

    /**
     * Return a string representation of the end time of a task.
     *
     * @return string
     */
    public function getEndTaskAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->end_date)->format('l jS \of F Y');
    }

    public static function phaseEndDate($phase_id)
    {
        return Phase::where('id', $phase_id)->value('end_date');
    }
    
    public static function phaseEstimatedHours($phase_id)
    {
        return Phase::where('id', $phase_id)->sum('hour_estimate');
    }

    public static function totalEstimatedHoursForPhase($phase_id)
    {
        return self::where('phase_id', $phase_id)->sum('hour_estimate');
    }

}
