<?php

namespace App\Http\Controllers;

use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClientController extends Controller
{
    public function index($office_email)
    {
        $office = User::where('email', $office_email)->firstOrFail();

        if ($office->level != 2) abort(404);
        $agent = $office->parent;
        $session = session()->getId();

        return view('client.form', compact(['office', 'agent', 'session']));
    }

    public function composse(Request $request)
    {

        $hash = explode('%&', $request->hash);

        //$order = $request->all();
        $office = User::findOrFail($hash[0]);
        $agent = User::findOrFail($hash[2]);
        $session = session()->getId();
        $oldOrder = Order::where('session', $session)
            ->whereNotIn('status', [9, 91, 92, 93, 94])->count();
        if ($oldOrder > 0) {

            $order = $oldOrder = Order::where('session', $session)->firstOrFail();
            //return $order;
        } else {
            //return 'new';
            $order = Order::create(
                [
                    'session' => $request->session,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'from_address' => $request->from_address,
                    'from_lat' => $request->from_lat,
                    'from_lng' => $request->from_lng,
                    'to_address' => ($request->to_address) ? $request->to_address : null,
                    'to_lat' => ($request->from_lat) ? $request->from_lat : null,
                    'to_lng' => ($request->from_lng) ? $request->from_lng : null,
                    'user_id' => $office->id,
                    'parent' => $agent->id,
                    'status' => 0,
                ]
            );
            $response = Http::withHeaders([
                'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
                'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
                'Content-Type' => 'application/json'
            ])->post('https://parseapi.back4app.com/functions/stream', [
                'pid' => $order->id,
                'model' => 'Order',
                'action' => 'C',
            ]);
        }

        return view('client.order', compact(['office', 'agent', 'order']));
    }

    public function dist(Request $request)
    {
        $hash = explode('%&', $request->hash);

        $office = User::findOrFail($hash[0]);
        $agent = User::findOrFail($hash[2]);
        $order = $request->all();
        return view('client.dist', compact(['office', 'agent', 'order']));
    }
}
