<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Task extends Model
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
        'image',
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
    public function user()
    {
        return $this->belongsToMany(User::class, 'task_user');
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

    public function assignedTasks()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id')->withTimestamps();
    }
}
