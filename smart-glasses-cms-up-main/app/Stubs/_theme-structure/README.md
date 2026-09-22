# DummyName Theme for Amuz CMS-UP

- CMS-UP 버전에서 사용가능한 DummyVendor/DummyName 테마입니다.

## Install
- 다음 명령을 통해, 컴포저에 리포지터리를 등록하고, 컴포저를 통해 패키지가 자동설치될 수 있도록 할 수 있습니다.
- 먼저 저장소를 추가 합니다.

```shell
composer config repositories.DummyVendor/DummyName vcs https://github.com/amuzcorp/DummyName
```

### for production

- 테마의 개발상태에 따라 :dev-main 태그는 제거하고 지정된 버전으로 변경할 수도 있습니다.
```shell
composer require DummyVendor/DummyName:dev-main
```

### for development

- 테마를 개발중인 경우, 저장소를 직접 clone 하여 저장소와 연결되는것이 더 유용할 수 있습니다.
```shell
composer require DummyVendor/DummyName:dev-main && rm -rf ./amuz-themes/DummyVendor/DummyName && cd ./amuz-themes/DummyVendor && git clone https://github.com/amuzcorp/DummyName
```
