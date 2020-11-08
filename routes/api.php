<?php

use App\Driver;
use App\Order;
use App\Preference;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/drivers', function () {
    return Driver::all();
});

Route::get('/orders/{id}', function ($id) {
    $user = User::findOrFail($id);

    if ($user->level == 2) {
        return
            $orders = Order::where('user_id', $user->id)
            ->whereIn('status', [0, 1, 12, 2, 21, 3])
            ->orderBy('updated_at', 'DESC')
            ->get();
    }
    return [];
});

Route::post('/orders/create', function (Request $request) {
    $user = User::find($request->uid);

    $order = Order::create(
        [
            'session' => $request->session,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'from_address' => $request->from_address,
            'from_lat' => $request->from_lat,
            'from_lng' => $request->from_lng,
            'user_id' => $user->id,
            'parent' => $user->parent->id
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

    return $order;
});

Route::post('/orders/update', function (Request $request) {
    $order = Order::find(1);
    $order->title = 'title 1 99';
    $order->save();
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
    ]);

    return $order;
});

Route::post('/orders/delete', function (Request $request) {
    $order = Order::find(17);


    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'D',
    ]);
    $order->delete();

    return $order;
});

Route::get('/orders/get/{order}', function ($order) {
    $order = Order::find($order);

    return $order;
});

Route::get('/order/office/reject/{order}', function ($order) {
    $order = Order::find($order);
    $order->status = 91;
    $order->save();
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
    ]);

    return $order;
});
Route::get('/order/office/approve/{order}', function ($order) {
    $order = Order::find($order);
    $order->status = 1;
    $order->save();
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
    ]);

    return $order;
});

Route::get('/order/office/select/{driver}/to/{order}', function ($driver, $order) {
    $driver = Driver::find($driver);
    $order = Order::find($order);
    $order->status = 2;
    $order->driver_id = $driver->id;
    $order->save();

    $driver->busy = 1;
    $driver->save();

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $driver->id,
        'model' => 'Driver',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    return $order;
});

Route::get('/order/office/send/{offer}/to/{order}', function ($offer, $order) {
    $order = Order::find($order);
    $order->status = 3;
    $order->offer = round(floatVal($offer), 2);
    $order->save();
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
    ]);

    return $order;
});

Route::get('/testo/{id}', function ($id) {
    $user = User::find($id);

    $settings = Setting::all();
    $result = [];
    $i = 0;
    foreach ($settings as $setting) {
        $result[$i] = [$setting->key => $setting->value];
        $parentConfig = Preference::where([
            'key' => $setting->key,
            'user_id' => $user->parent->id
        ])->get();
        if ($parentConfig->count() > 0) {
            $result[$i] = [$parentConfig->first()->key => $parentConfig->first()->value];
        }

        $userConfig = Preference::where([
            'key' => $setting->key,
            'user_id' => $user->id
        ])->get();
        if ($userConfig->count() > 0) {
            $result[$i] = [$userConfig->first()->key => $userConfig->first()->value];
        }


        $i++;
    }

    return $result;
});


// API Mobile APP:

Route::get('/app/get/order/{hash}', function ($hash) {
    $driver = Driver::where('hash', $hash)->firstOrFail();
    $pendingOrder = Order::where([
        'driver_id' => $driver->id,
        'status' => 21
    ]);
    if ($pendingOrder->count() > 0) {
        return $pendingOrder->first();
    }

    $newOrder = Order::where([
        'driver_id' => $driver->id,
        'status' => 2
    ]);
    if ($newOrder->count() > 0) {
        return $newOrder->first();
    }

    return false;
});

Route::get('/app/approve/{order_id}', function ($order_id) {
    $order = Order::findOrFail($order_id);
    $order->status = 21;
    $order->save();
    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
    ]);


    return response(1, 200);
});

Route::get('/app/{hash}/reject/{order_id}', function ($hash, $order_id) {
    $driver = Driver::where('hash', $hash)->firstOrFail();
    $order = Order::findOrFail($order_id);
    $order->status = 1;
    $order->driver_id = null;
    $order->block = ($order->block == null) ? $driver->id : '--' . $driver->id;
    $order->save();

    $driver->busy = 0;
    $driver->save();

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $driver->id,
        'model' => 'Driver',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    return response(1, 200);
});

Route::get('/app/{hash}/done/{order_id}', function ($hash, $order_id) {
    $driver = Driver::where('hash', $hash)->firstOrFail();
    $order = Order::findOrFail($order_id);
    $order->status = 9;
    $order->save();

    $driver->busy = 0;
    $driver->save();

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $order->id,
        'model' => 'Order',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $driver->id,
        'model' => 'Driver',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    return response(1, 200);
});

Route::get('/app/{hash}/tracking/{lat}/{lng}', function ($hash, $lat, $lng) {
    $driver = Driver::where('hash', $hash)->firstOrFail();
    $driver->lat = $lat;
    $driver->lng = $lng;
    $driver->save();

    $response = Http::withHeaders([
        'X-Parse-Application-Id' => 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
        'X-Parse-REST-API-Key' => 'ozmiEzNHJIAb3EqCD9lislhOC5dPsC0OS18DFJ6j',
        'Content-Type' => 'application/json'
    ])->post('https://parseapi.back4app.com/functions/stream', [
        'pid' => $driver->id,
        'model' => 'Driver',
        'action' => 'U',
        'meta' => ['hash' => $driver->hash]
    ]);

    return response(1, 200);
});
