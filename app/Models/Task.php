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
        app()->setLocale(auth()->user()->locale);
        return ucwords(Carbon::createFromFormat('Y-m-d', $this->start_date)->isoFormat('dddd Do MMMM YYYY'));
    }

    /**
     * Return a string representation of the end time of a task.
     *
     * @return string
     */
    public function getEndTaskAttribute()
    {
        app()->setLocale(auth()->user()->locale);
        return ucwords(Carbon::createFromFormat('Y-m-d', $this->end_date)->isoFormat('dddd Do MMMM YYYY'));
    }

    public static function phaseStartDate($phase_id)
    {
        return Phase::where('id', $phase_id)->value('start_date');
    }

    public static function phaseEndDate($phase_id)
    {
        return Phase::where('id', $phase_id)->value('end_date');
    }
}
