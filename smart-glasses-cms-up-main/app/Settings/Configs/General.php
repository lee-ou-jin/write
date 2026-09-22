<?php

namespace App\Settings\Configs;

use App\Services\AbstractNovaConfigs;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

/**
 * 설정에 관련된 번역은,프론트에 호출되지 않으므로 별도의 vendor resources 에서 관리한다.
 * resource_path('/lang/vendor/nova-settings/ko.json');
 */
class General extends AbstractNovaConfigs {
    public function pageName(): string
    {
        return __('General');
    }

    public function casts(): array
    {
        return [

        ];
    }

    public function fields(): array
    {
        return [
            Text::make('Some setting', 'some_setting'),
            Number::make('A number', 'a_number'),
        ];
    }

    public function setPriority(): int
    {
        return 1;
    }
}
