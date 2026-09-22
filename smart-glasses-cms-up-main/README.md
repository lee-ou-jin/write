# AmuzCMS - UP

## 프로젝트 시작하기

- 프로젝트의 독립성을 위해 저장소를 Fork 하여 새 애플리케이션을 시작하기를 권장합니다. 이 패키지의 저장소는 [https://github.com/amuzcorp/cms-up](https://github.com/amuzcorp/cms-up) 입니다. 저장소의 Fork 기능을 이용해 새 프로젝트를 시작하십시오.

## Patch Note

### 24.03.30
- 이제 패키지를 생성하면, 기본적으로 `resources/views` 경로가 `viewPaths`에 추가되고, `resources/lang` 디렉터리의 `json`파일이 언어팩에 추가됩니다.
- 추가된 언어팩은 `Inertia` 환경에서 즉시 랜더링됩니다.
- 헬퍼함수가 추가됩니다. `app/Helpers.php` 파일을 통해 얼마든지 다른 헬퍼함수를 추가할 수 있습니다.
- `AmuzPackage`의 경로를 반환하는 헬퍼함수가 추가되었습니다.
```php
$> php artisan tinker
Psy Shell v0.11.22 (PHP 8.3.4 — cli) by Justin Hileman
> package_path('vimeo-field')
= "/Users/xiso/develop/projects/cms-up/amuz-packages/vimeo-field"

> package_config_path('vimeo-field')
= "/Users/xiso/develop/projects/cms-up/amuz-packages/vimeo-field/src/config/"

> package_database_path('vimeo-field')
= "/Users/xiso/develop/projects/cms-up/amuz-packages/vimeo-field/src/database/"

> package_resource_path('vimeo-field')
= "/Users/xiso/develop/projects/cms-up/amuz-packages/vimeo-field/src/resources/"

> package_lang_path('vimeo-field')
= "/Users/xiso/develop/projects/cms-up/amuz-packages/vimeo-field/src/resources/lang/"
```
- 이외에도 `amuz_packages()` 및 `amuz_themes()` 헬퍼함수를 통해 설치된 모든 패키지 이름과 경로, 테마목록을 반환받을 수 있습니다.

### 24.03.29
- 이제 `php artisan amuz-cms:resource {ModelName} {packageName}` 을 통해 `Nova Resource`, `Model`, `database migration`을 한번에 생성할 수 있게 됩니다.
- 생성된 리소스는 에디터에서의 `Warning`없이 즉시 사용할 수 있도록 준비되어 있습니다.

### 24.02.29
- `/public/vendor` 디렉터리가 `composer update` 명령어를 통해 자동으로 생성되므로 저장소상에서 제외됩니다. 필요한경우 `.gitignore`에서 제외할 수 있습니다.
- `npm install` 명령어가 작동할 때, 포함된 모든 테마의 `package.json` 파일이 병합되어 처리됩니다. `amuz-packages` 디렉터리도 똑같이 처리할 수 있지만, 주석되어있으므로 필요에 따라 해제하여 사용할 수 있습니다.
- 이제 `app/Nova/Resources` 디렉터리 내에 `Resource`를 생성하기만 하면, 같은 `uri-key`를 가진 `Resource`를 오버라이딩 합니다. 이것은 `Package`가 제공하는 `Resource` 를 오버라이드 하기위해 해야하는 많은 작업을 건너뛰게 해 줍니다. 
