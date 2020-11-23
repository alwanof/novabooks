<?php

namespace App\Nova;

use App\Driver;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Muradalwan\DriversMap\DriversMap;
use Muradalwan\OrdersCard\OrdersCard;
use Muradalwan\OrderStream\OrderStream;

class Order extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Order::class;
    //public static $polling = true;
    //public static $showPollingToggle = true;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];
    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Orders');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Order');
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Name'), 'name'),
            Text::make(__('Email'), 'email'),
            Text::make(__('Phone'), 'phone'),
            Text::make(__('Status'), 'status', function () {
                return $this->statusLabel($this->status);
            }),
            Text::make(__('Driver'), function () {
                if ($this->driver_id) {
                    return Driver::find($this->driver_id)->name;
                }
                return '-';
            }),
            Number::make(__('Offer'), 'offer'),


        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new OrdersCard)->authUser(),
            new DriversMap(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    private function statusLabel($status)
    {
        $label = '-';
        switch ($status) {
            case 0:
                $label = 'New';
                break;
            case 1:
                $label = 'Accepted';
                break;
            case 2:
                $label = 'Waiting Driver Approve';
                break;
            case 21:
                $label = 'Proccessing..';
                break;
            case 3:
                $label = 'Waiting Customer Approve';
                break;
            case 9:
                $label = 'Done';
                break;
            case 91:
                $label = 'Office Reject';
                break;
            case 92:
                $label = 'Customer Reject';
                break;
            case 93:
                $label = 'No-Resp from Office';
                break;
            case 94:
                $label = 'No-Resp from Customer';
                break;

            default:
                break;
        }
        return $label;
    }
}
