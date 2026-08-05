# Пользовательский интерфейс

Исходный код React-формы расположен в `install/js/form-react`. Сборка создаётся `react-scripts` и попадает в `install/js/form-react/build`; установщик копирует JavaScript в `/bitrix/js/anz/appointment`.

Форма использует React 19, Material UI, `react-hook-form`, `react-imask`, `zustand` и обращается к `/bitrix/services/main/ajax.php`. Типы данных и API-клиент находятся в `install/js/form-react/src`.

Bitrix-компоненты в `install/components/anz/` подключают форму и административные элементы. `ServiceManager::includeAppointmentExtension()` автоматически загружает JS-расширение на публичной части, только если включена соответствующая опция, запрос не находится в административной части и у пользователя есть право чтения либо административное право.

После изменения исходников frontend выполните в `install/js/form-react`:

```powershell
npm install
npm run buildWin
```

В релиз включается только production-сборка, а не `node_modules`.
