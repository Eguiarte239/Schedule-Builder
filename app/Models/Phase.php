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
        'hour_estimate',
        'start_time',
        'end_time',
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
        return $this->belongsToMany(Task::class);
    }

    /**
     * Return a string representation of the start time of a task.
     *
     * @return string
     */
    public function getStartTaskAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->start_time)->format('l jS \of F Y');
    }

    /**
     * Return a string representation of the end time of a task.
     *
     * @return string
     */
    public function getEndTaskAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->end_time)->format('l jS \of F Y');
    }
}
