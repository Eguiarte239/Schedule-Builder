<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Phase extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * Get the user that owns the task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function task()
    {
        return $this->hasMany(Task::class);
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

    public static function projectStartDate($project_id)
    {
        return Project::where('id', $project_id)->value('start_date');
    }

    public static function projectEndDate($project_id)
    {
        return Project::where('id', $project_id)->value('end_date');
    }

    public static function projectEstimatedHours($project_id)
    {
        return Project::where('id', $project_id)->value('hour_estimate');
    }

    public static function totalEstimatedHoursPerProject($project_id)
    {
        return self::where('project_id', $project_id)->sum('hour_estimate');
    }
}
