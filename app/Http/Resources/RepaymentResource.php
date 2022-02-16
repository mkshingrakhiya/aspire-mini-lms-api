<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
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
            'loan_id' => $this->loan_id,
            'due' => $this->due,
            'interest' => $this->interest,
            'principal' => $this->principal,
            'outstanding' => $this->outstanding,
            'due_on' => $this->due_on,
            'paid_on' => $this->paid_on,

            'is_paid' => $this->is_paid,

            'loan' => LoanResource::make($this->whenLoaded('loan'))
        ];
    }
}
