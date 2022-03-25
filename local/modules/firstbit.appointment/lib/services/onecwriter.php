<?php
namespace FirstBit\Appointment\Services;

use Bitrix\Main\Localization\Loc;
use FirstBit\Appointment\Config\Constants;
use FirstBit\Appointment\Utils\Utils;

Loc::loadMessages(__FILE__);

class OneCWriter extends AbstractOneCService
{
    /** make request to creating order
     * @param array $params
     * @return array
     */
    public function addOrder(array $params): array
    {
        if (Utils::validateOrderParams($params))
        {
            if (Constants::DEMO_MODE === "Y"){
                sleep(3);
                return ['success' => true];
            }

            $params['orderDate'] = Utils::formatDateToOrder($params['orderDate']);
            $params['timeBegin'] = Utils::formatDateToOrder($params['timeBegin'], true);
            $params['timeEnd']   = Utils::formatDateToOrder($params['timeEnd'], true);

            if (empty($params["clientUid"]))
            {
                $params["comment"] =    $params['name'] . " "
                    . $params['middleName'] . " "
                    . $params['surname'] . "\n"
                    . $params['phone'] ."\n". $params["comment"];

                $params['unauthorized'] = "Y";
            }

            return $this->send(Constants::CREATE_ORDER_ACTION_1C, $params);
        }
        return Utils::createErrorArray(Loc::getMessage("FIRSTBIT_APPOINTMENT_REQUIRED_PARAMS_ERROR"));
    }

    /** cancelling order in 1C
     * @param string $orderUid
     * @return array
     */
    public function deleteOrder(string $orderUid): array
    {
        return $this->send(Constants::DELETE_ORDER_ACTION_1C, $orderUid);
    }

    // TODO - make methods for adding WaitList and Reserve
}