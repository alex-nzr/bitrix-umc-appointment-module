# Интеграция с 1С и локальные данные

## SOAP и SDK

`Integration\UmcSdk\Gateway\Sdk` реализует `UmcGatewayInterface` и является адаптером `alex-nzr/bit-umc-sdk`. Текущая версия поддерживает только `ExchangeMode::SOAP`; HTTP-режим в `Configuration::getOneCApiUrl()` и `Sdk::createClient()` не реализован.

`Sdk::parsePublicationUrl()` выделяет протокол, хост с портом и имя базы из URL публикации. Для клиента создаются `ConnectionOptions` и `BasicAuth`; значения логина, пароля и токена берутся только из `Configuration`. Их реальные значения не должны попадать в код, журналы или документацию.

Методы `getClinics()`, `getEmployees()`, `getServices()` и `getSchedule()` сначала проверяют `CacheProvider`. Если данных нет, gateway устанавливает ключ блокировки в кеше, получает данные 1С, валидирует их и сохраняет результат. Ожидающий конкурентный запрос опрашивает кеш ограниченное время вместо параллельного обращения к 1С.

## Запись на приём

`Service\Exchange\Manager` объединяет gateway и `AppointmentRepository`.

- `sendBooking()` резервирует слот и возвращает `BookingDto`.
- `sendAppointment()` создаёт `AppointmentDto` либо `WaitListDto`, затем сохраняет локальную запись.
- `deleteAppointment()`, `cancelOwnAppointment()` и `updateAppointmentStatus()` синхронизируют изменения с 1С и локальной таблицей.

Перед созданием записи `AppointmentPayloadGuard` сверяет идентификаторы клиники и сотрудника с текущими данными, проверяет слот и длительность, а при включённых услугах — соответствие услуг сотруднику.

## Конфигурация обмена

Ключи опций и набор вкладок административной страницы определены в `Config\Constants` и `Config\Options\Module`. Не следует читать `Option` напрямую в прикладном коде: добавляйте типизированный метод в `Configuration`.

`Configuration::getOneCPassword()` и `getOneCToken()` расшифровывают только значения формата `v2:` через `Service\Security\Encryptor`. При обновлении legacy-значения мигрируются release-updater’ом, а legacy-дешифрование не входит в runtime-модуль.
