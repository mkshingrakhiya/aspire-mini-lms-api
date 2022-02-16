<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'amount' => $this->amount,
            'term' => $this->term,
            'annual_interest_rate' => $this->annual_interest_rate,
            'repayment_frequency' => $this->repayment_frequency,
            'reviewer_id' => $this->reviewer_id,
            'status' => $this->status,
            'disbursed_at' => $this->disbursed_at,

            'status_text' => $this->status_text,
            'is_disbursed' => $this->is_disbursed,

            'client' => UserResource::make($this->whenLoaded('client')),
            'reviewer' => UserResource::make($this->whenLoaded('reviewer')),

            'repayments' => RepaymentCollection::make($this->whenLoaded('repayments'))
        ];
    }
}
