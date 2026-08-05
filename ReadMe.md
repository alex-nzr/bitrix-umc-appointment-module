# Модуль записи на приём БИТ.УМЦ

`anz.appointment` — модуль 1С-Битрикс для показа формы записи на приём, получения справочников и расписания из 1С БИТ.УМЦ и отправки созданных записей обратно в 1С.

Текущая версия модуля — 4.1.1. Интеграция реализована через пакет `alex-nzr/bit-umc-sdk` версии 2.x и SOAP-публикацию 1С.

## Требования

- PHP 8.1 или новее;
- модуль `main` 1С-Битрикс версии 25.0 или новее;
- опубликованная база 1С БИТ.УМЦ, доступная с веб-сервера;
- БИТ.УМЦ не ниже 2.1.24.9 (Corp) или 2.0.48.15 (Prof).

## Архитектура

```text
React-форма / Bitrix-компоненты
            │ AJAX
            ▼
Controller\Appointment ──► Service\Exchange\Manager
                                    │
                         Integration\UmcSdk\Gateway\Sdk
                                    │ SOAP
                                    ▼
                               1С БИТ.УМЦ
                                    │
        RecordTable ◄── AppointmentRepository ◄── Manager
```

Контроллер остаётся транспортным слоем. Сценарии обмена координирует `Service\Exchange\Manager`, а обращение к 1С изолировано в `Integration\UmcSdk\Gateway\Sdk`. Внешние массивы валидируются и преобразуются в DTO до использования остальной частью модуля.

## Карта модуля

| Путь | Назначение |
| --- | --- |
| `lib/Controller/Appointment.php` | AJAX-действия формы и административных операций. |
| `lib/Service/Exchange/Manager.php` | Запись, отмена, обновление статуса и работа с локальными записями. |
| `lib/Integration/UmcSdk/` | Адаптер SDK, кеширование, маппинг и проверка ответов 1С. |
| `lib/Dto/` | Типизированные представления клиник, сотрудников, услуг, слотов и записей. |
| `lib/Repository/` и `lib/Model/RecordTable.php` | ORM-слой таблицы `anz_appointment_record`. |
| `lib/Service/Security/` | Проверки данных, подтверждение, rate limit, доступ к записям, защита URL и шифрование секретов. |
| `lib/Component/` и `install/components/` | Административные и пользовательские Bitrix-компоненты. |
| `install/js/form-react/` | Исходники и production-сборка React-формы. |
| `lib/Agent/Exchange.php` | Регулярное обновление кеша, очистка кеша и журналов. |

## Основные потоки

1. Форма вызывает действия `Appointment` через `/bitrix/services/main/ajax.php`.
2. Контроллер применяет prefilters, получает сервисы из `Service\Container` и вызывает `Manager`.
3. `Sdk` читает кеш или обращается к SOAP-публикации 1С, проверяет ответ и строит DTO.
4. При создании записи `Manager` отправляет её в 1С и сохраняет локальную запись через `AppointmentRepository`.
5. Агент `Exchange::loadData()` запускается раз в минуту, но фактическое обновление регулируется настройками следующего запуска и интервала обмена.

## Настройка и разработка

После установки откройте пункт меню **ANZ** в административной части и заполните параметры SOAP-публикации, учётной записи и необходимые настройки формы. Пароли и токены хранятся в опциях Битрикс в формате `v2:` с использованием `Bitrix\Main\Security\Cipher` и ключа ядра.

Для локальной разработки:

```powershell
cd local/modules/anz.appointment
composer install
vendor/bin/phpunit --configuration phpunit.xml
cd install/js/form-react
npm install
npm run build (или buildWin для windows)
```

Production-архив не включает тесты, исходники node-модулей, служебные файлы Composer и корневой `ReadMe.md`.

## События модуля

Модуль использует события `onBefore...Parsed` и `onAfter...Parsed` для клиник, сотрудников, номенклатуры и расписания, а также `onBeforeOrderSend`. Для изменения данных обработчик `onAfter...Parsed` возвращает `Bitrix\Main\EventResult` с массивом замены. Неизвестные поля SDK доступны как `_extra` в массивах SDK и как `extra` в DTO.

Подробный список событий и поведение описаны в [docs/interactions.md](docs/interactions.md).

## Техническая документация

- [Архитектура](docs/architecture.md)
- [Интеграция с 1С и хранение данных](docs/modules/integration.md)
- [Безопасность](docs/modules/security.md)
- [Пользовательский интерфейс](docs/modules/frontend.md)
- [Потоки данных и расширение событиями](docs/interactions.md)
- [Установка и локальная разработка](docs/setup.md)
- [Архитектурные решения](docs/decisions/)
