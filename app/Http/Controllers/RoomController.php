<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageNotification;
use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\MessageResource;

class RoomController extends Controller
{
    public function index()
    {
        $rooms_id = [];
        foreach(Message::orderBy('id','desc')->where('from',auth()->user()->id)->orWhere('to',auth()->user()->id)->get() as $message){
            if(!in_array($message->room_id, $rooms_id)){
                $rooms_id[] = $message->room_id;
            }
        }
        $rooms = [];
        foreach($rooms_id as $room_id){
            $rooms[] = new RoomResource(Room::find($room_id));
        }
        return $rooms;
    }
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        if($request->id === auth()->user()->id){
            return response()->json(['message' => 'You cannot create a room with yourself.'], 403);
        }
        $room = Room::where('user_id_1', auth()->user()->id)
            ->where('user_id_2', $request->id)
            ->first();
        if (!$room) {
            $room = Room::where('user_id_1', $request->id)
                ->where('user_id_2', auth()->user()->id)
                ->first();
        }
        if (!$room) {
            $room = Room::create([
                'user_id_1' => auth()->user()->id,
                'user_id_2' => $request->id
            ]);
        }
        return new RoomResource($room);
    }
    public function show(Request $request, Room $room)
    {
        if($room->user_id_1 !== auth()->user()->id && $room->user_id_2 !== auth()->user()->id){
            return response()->json(['message' => 'You are not in this room.'], 403);
        }
        foreach(Message::orderBy('id','asc')->where('room_id', $room->id)->where('to',auth()->user()->id)->get() as $message){
            $message->seen = true;
            $message->save();
        }
        event(new MessageNotification(Message::orderBy('id','desc')->where('room_id', $room->id)->where('to',auth()->user()->id)->first()));
        return new RoomResource($room);
    }
    public function storeMessage(Request $request)
    {
        $request->validate([
            'room_id' => 'required|integer',
            'message' => 'required|string',
        ]);
        $room = Room::find($request->room_id);
        if(!$room){
            return response()->json(['message' => 'Room not found.'], 404);
        }
        if($room->user_id_1 !== auth()->user()->id && $room->user_id_2 !== auth()->user()->id){
            return response()->json(['message' => 'You are not in this room.'], 403);
        }
        $message = $room->messages()->create([
            'room_id' => $room->id,
            'from' => auth()->user()->id,
            'to' => $room->user_id_1 === auth()->user()->id ? $room->user_id_2 : $room->user_id_1,
            'body' => $request->message,
        ]);
        event(new MessageNotification($message));
        return new MessageResource($message);
    }
    public function getUser(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
        ]);
        $user = User::where('email', $request->email)->first();
        return new UserResource($user);
    }
}
