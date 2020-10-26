<?php

namespace App\Nova;


use App\Nova\Actions\ActiveOperator;
use App\Nova\Actions\SendCredentionalAction;
use App\User;
use Bissolli\NovaPhoneField\PhoneNumber;
use Ctessier\NovaAdvancedImageField\AdvancedImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inspheric\Fields\Email;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Opanegro\FieldNovaPasswordShowHide\FieldNovaPasswordShowHide;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;

class Driver extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Driver::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $preventFormAbandonment = true;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'taxiNo'
    ];

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
            AdvancedImage::make('Avatar')->croppable(1 / 1)->resize(320)->disk('public')->path('drivers'),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('Taxi')
                ->rules('required', 'max:255'),
            Text::make('TaxiNo', 'taxiNo')
                ->rules('required', 'max:255'),
            Email::make('Email')
                ->rules('required', 'email', 'max:255')
                ->hideFromIndex()
                ->clickable(),
            Text::make('Password')
                ->rules('required', 'min:8', 'max:20')
                ->default(Str::random(8))
                ->hideFromIndex(),
            PhoneNumber::make('Phone')
                ->rules('required', 'min:6', 'max:20')
                ->hideFromIndex(),
            Text::make('TaxiColor')
                ->rules('required', 'max:25')
                ->hideFromIndex(),
            //Text::make('Agent', 'parent')->onlyOnIndex(),
            //Text::make('Office', 'user_id')->onlyOnIndex(),
            Boolean::make('Busy')
                ->onlyOnIndex(),
            Boolean::make('Active')
                ->onlyOnIndex(),
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
        return [];
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
        return [
            (new ActiveOperator())->onlyOnTableRow(),
            (new SendCredentionalAction()),
        ];
    }
}
