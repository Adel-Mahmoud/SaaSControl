<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'domain' => $this->domain,
            'db_name' => $this->db_name,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];
    }
}