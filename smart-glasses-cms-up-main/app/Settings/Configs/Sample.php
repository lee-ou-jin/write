<?php
namespace App\Settings\Configs;

use App\Services\AbstractNovaConfigs;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Laravel\Nova\Fields\Text;

class Sample extends AbstractNovaConfigs {
    public function pageName(): string
    {
        return __('Sample');
    }

    public function casts(): array
    {
        return [

        ];
    }

    public function fields(): array
    {
        return [
            Text::make('hi'),
            Text::make('hi2'),
            Text::make('hi3'),
            Tabs::make('Tabs',[
                Tab::make('sample',[
                    Text::make('hi'),
                    Text::make('hi2'),
                    Text::make('hi3')
                ]),
                Tab::make('sample2',[
                    Text::make('hi'),
                    Text::make('hi2'),
                    Text::make('hi3')
                ])
            ]),
        ];
    }

    public function setPriority(): int
    {
        return 10;
    }
}
