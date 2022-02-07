<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name'=>$this->name,
            'email'=>$this->email,
            'email_verified_at'=>$this->email_verified_at != null ?$this->email_verified_at->diffForHumans() : null,
            'token'=>$this->token,
            'created_at'=>$this->created_at->diffForHumans(),
            'id'=>$this->id,
        ];
    }
}
