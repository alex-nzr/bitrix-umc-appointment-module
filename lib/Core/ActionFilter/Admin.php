<?php
namespace ANZ\Appointment\Core\ActionFilter;

use ANZ\Appointment\Core\Exception\ServiceContainerException;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

final class Admin extends Base
{
    /**
     * @throws ServiceContainerException
     */
    public function onBeforeAction(Event $event): ?EventResult
    {
		if (!Container::getInstance()->getUserPermissions()->isAdmin())
		{
			$this->addError(new Error('Permission denied. Admin access required', 403));

			return new EventResult(EventResult::ERROR, null, null, $this);
		}

		return null;
	}
}
