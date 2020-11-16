@extends('layouts.master')

@section('title', 'create')

@section('content')

    <img class="img-thumbnail rounded-circle" src="/storage/{{ $agent->avatar }}" alt="" width="64">
    <div class="container text-center">
        @if ($office->settings['offer_enabled'])
            <form class="form-signin was-validated text-center" action="{{ route('client.dist') }}" method="POST">
            @else
                <form class="form-signin was-validated text-center" action="{{ route('client.composse') }}" method="POST">
        @endif

        <img class="img-thumbnail rounded-circle" src="/storage/{{ $office->avatar }}" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal"><span class="badge badge-secondary">{{ $office->name }}</span></h1>


        @csrf
        <input type="hidden" name="from_lat" id="from_lat">
        <input type="hidden" name="from_lng" id="from_lng">

        <input type="hidden" name="session" value="{{ $session }}">
        <input type="hidden" name="hash" value="{{ $office->id . '%&' . $session . '%&' . $agent->id }}">

        <div class="form-group">

            <input type="text" class="form-control" placeholder="Enter your name" name="name" required>
            <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="form-group">

            <input type="text" class="form-control" placeholder="Enter your phone" name="phone" required>
            <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="form-group">

            <input type="text" class="form-control" placeholder="Enter your email" name="email" required>
            <div class="invalid-feedback">Please fill out this field.</div>
        </div>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-map-marker-alt text-primary" id="confirmSource"></i>
                    </span>
                </div>

                <input type="text" class="form-control" placeholder="Enter your address" id="from_address"
                    name="from_address" required readonly>
                <div class="invalid-feedback">Please fill out this field.</div>
                <div id="source"></div>

            </div>


        </div>


        @if ($office->settings['offer_enabled'])
            <button type="submit" class="btn btn-lg btn-primary btn-block">Continue</button>
        @else
            <button type="submit" class="btn btn-lg btn-primary btn-block">Submit</button>
        @endif


        </form>
    </div>
@endsection

@section('js')
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANYVpeOpsNN4DqdKR4AKAyd03IQ3_9PvU"></script>
    <script src="https://unpkg.com/location-picker/dist/location-picker.min.js"></script>

    <script>
        $(document).ready(function() {


            $("#confirmSource").click(function() {
                $("#source").slideToggle();
            });


            var lp = new locationPicker('source', {
                setCurrentPosition: true, // You can omit this, defaults to true

            }, {
                zoom: 15 // You can set any google map options here, zoom defaults to 15
            });


            google.maps.event.addListener(lp.map, 'idle', function(event) {
                // Get current location and show it in HTML
                var loc = lp.getMarkerPosition();
                lat = loc.lat;
                lng = loc.lng;

                //onIdlePositionView.innerHTML = 'The chosen location is ' + location.lat + ',' + location.lng;
                $.getJSON(
                    'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + loc.lat + ',' + loc
                    .lng +
                    '&key=AIzaSyANYVpeOpsNN4DqdKR4AKAyd03IQ3_9PvU',
                    function(result) {
                        document.getElementById('from_address').value = result.results[0]
                            .formatted_address;
                        document.getElementById('from_lat').value = loc.lat;
                        document.getElementById('from_lng').value = loc.lng;
                    });
            });

        });

    </script>

@endsection

@section('css')

    <style>
        html,
        body {
            height: 100%;
        }



        body {

            align-items: center;
            padding-top: 16px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }


        #source {
            width: 100%;
            height: 300px;
            display: none;
        }

        #dist {
            width: 100%;
            height: 300px;
            display: none;
        }

        .form-signin {
            width: 100%;

            padding: 15px;
            margin: auto;
        }

        .form-signin .checkbox {
            font-weight: 400;
        }

        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

    </style>
@endsection
