<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with(['utilisateur', 'signalement'])->get();
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contenu_notification' => 'required|string',
            'etat_notification' => 'required|string',
            'type_notification' => 'required|string',
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'signalement_id' => 'required|exists:signalements,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = Notification::create($request->all());
        return response()->json($notification, 201);
    }

    public function show($id)
    {
        $notification = Notification::with(['utilisateur', 'signalement'])->findOrFail($id);
        return response()->json($notification);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'etat_notification' => 'sometimes|string',
            'contenu_notification' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification->update($request->only(['etat_notification', 'contenu_notification']));
        return response()->json($notification);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return response()->json(null, 204);
    }
}
