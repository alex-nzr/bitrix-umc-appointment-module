## Backend

Для разработки нужны зависимости Composer:

```powershell
cd {MODULE_HOLDER}/modules/anz.appointment
composer install
```

### Unit-тесты

После установки Composer-зависимостей запустите все unit-тесты модуля из его корневого каталога:

```powershell
php vendor/bin/phpunit --configuration phpunit.xml
```

Команда использует `phpunit.xml` модуля и тесты из `tests/Unit`. На Windows при необходимости укажите полный путь к исполняемому файлу PHP вместо `php`.

## Frontend

```powershell
cd local/modules/anz.appointment/install/js/form-react
npm install
npm run build (для windows - buildWin)
```

Команда `build`/`buildWin` отключает source map и собирает production версию фронтенда.
