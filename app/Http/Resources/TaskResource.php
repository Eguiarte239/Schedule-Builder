<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'hour_estimate' => $this->hour_estimate,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'priority' => $this->priority,
            'image' => $this->image,
        ];
    }
}
