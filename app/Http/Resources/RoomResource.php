<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'room_id'=>$this->id,
            'self'=>new UserResource(User::find(auth()->user()->id)),
            'opponent'=>new UserResource($this->user_id_1 === auth()->user()->id ? User::find($this->user_id_2): User::find($this->user_id_1)),
            'created_at'=>$this->created_at->diffForHumans(),
            'messages'=>MessageResource::collection(Message::orderBy('id','desc')->where('room_id', $this->id)->get()),
        ];
    }
}
