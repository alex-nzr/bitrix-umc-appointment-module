# Потоки данных и события

## Получение справочников и расписания

1. Клиент вызывает действие `getClinicsAction`, `getEmployeesAction`, `getServicesAction` или `getScheduleAction` контроллера.
2. Контроллер получает `Manager` из `Container`.
3. `Manager` вызывает `Sdk`.
4. `Sdk` читает `CacheProvider` либо обращается к SDK и 1С, применяет события, валидирует и маппит данные в DTO.
5. Контроллер возвращает данные в формате AJAX-ответа Битрикс.

## Создание записи

1. `bookSlotAction` резервирует выбранный слот и сохраняет результат в `BookingSession`.
2. `sendAppointmentAction` проверяет подтверждение, session booking и входные поля через security-сервисы.
3. `Manager::sendAppointment()` передаёт данные в 1С через SDK.
4. Успешный результат сохраняется в `anz_appointment_record`; сессия помечается как содержащая созданную запись.
5. `sendEmailNoteAction` может отправить уведомление только для такой записи и адреса из этой же сессии.

## События расширения

SDK вызывает события модуля `anz.appointment`:

| Данные | До обработки | После обработки |
| --- | --- | --- |
| Клиники | `onBeforeClinicsParsed` | `onAfterClinicsParsed` |
| Сотрудники | `onBeforeEmployeesParsed` | `onAfterEmployeesParsed` |
| Номенклатура / услуги | `onBeforeNomenclatureParsed` | `onAfterNomenclatureParsed` |
| Расписание | `onBeforeScheduleParsed` | `onAfterScheduleParsed` |

`onBefore...Parsed` вызывается для совместимости и диагностики: его результат не заменяет данные. Чтобы изменить массив, обработчик `onAfter...Parsed` возвращает `Bitrix\Main\EventResult` типа success с изменённым массивом. `Event::processEventResult()` заменяет списки целиком, а ассоциативные массивы объединяет через `array_replace_recursive`.

Перед отправкой заказа используется событие `onBeforeOrderSend`.
