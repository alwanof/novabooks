<?php

namespace App\Nova;

use App\Driver;
use App\Nova\Actions\TestActionn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Silvanite\NovaToolPermissions\Role;
use Ctessier\NovaAdvancedImageField\AdvancedImage;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'email',
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
            ID::make()->sortable(),

            //Avatar::make('Avatar')->squared()->disk('public'),
            Avatar::make('Avatar')->onlyOnIndex(),
            AdvancedImage::make('Avatar')->croppable(1 / 1)->resize(320)->disk('public')->path('users')->hideFromIndex(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Text::make('Level', function () {
                switch ($this->level) {
                    case 0:
                        return __('app.LEVELS.0');
                        break;
                    case 1:
                        return __('app.LEVELS.1');
                        break;
                    case 2:
                        return __('app.LEVELS.2');
                        break;

                    default:
                        return '#NA';
                        break;
                }
            })->onlyOnIndex(),
            Select::make('Level')->options(function () {
                $options = [];
                $level = auth()->user()->level;
                if ($level == 0) {
                    $options[0] = __('app.LEVELS.0');
                    $options[1] = __('app.LEVELS.1');
                }
                if ($level == 1) {
                    $options[2] = __('app.LEVELS.2');
                }
                return $options;
            })->creationRules('required')->onlyOnForms(),


            Boolean::make('Active')->onlyOnDetail()->onlyOnForms()->withMeta(["value" => 1]),
            Text::make('Ref', function () {

                return ($this->parent) ? $this->parent->name : '-';
            })->onlyOnIndex(),
            BelongsToMany::make('Roles', 'roles', Role::class),

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
            //(new TestActionn())->showOnTableRow(),
        ];
    }
}
