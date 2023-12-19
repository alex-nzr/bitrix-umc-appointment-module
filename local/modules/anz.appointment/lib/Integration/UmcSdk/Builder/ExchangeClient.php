<?php
/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2023
 * ==================================================
 * Bit.Umc - Bitrix integration - ExchangeClient.php
 * 02.12.2023 02:55
 * ==================================================
 */
namespace ANZ\Appointment\Integration\UmcSdk\Builder;

use ANZ\Appointment\Integration\UmcSdk\Client\SoapClient;
use ANZ\BitUmc\SDK\Core\Contract\Connection\IClient;
use ANZ\BitUmc\SDK\Core\Dictionary\Protocol;
use Bitrix\Main\Web\Uri;

/**
 * @class ExchangeClient
 * @package ANZ\Appointment\Integration\UmcSdk\Builder
 */
class ExchangeClient extends \ANZ\BitUmc\SDK\Builder\ExchangeClient
{
    protected string $fullBaseUrl = '';

    public function build(): IClient
    {
        $this->fillDataByFullUrl();

        $this->checkFields();

        return SoapClient::create(
            $this->login,
            $this->password,
            $this->publicationProtocol,
            $this->publicationAddress,
            $this->baseName,
            $this->scope
        );
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setFullBaseUrl(string $url): static
    {
        $this->fullBaseUrl = $url;
        return $this;
    }

    /**
     * @return void
     */
    protected function fillDataByFullUrl(): void
    {
        if (!empty($this->fullBaseUrl))
        {
            $oUri = new Uri($this->fullBaseUrl);
            switch ($oUri->getScheme())
            {
                case Protocol::HTTP->value:
                    $this->setPublicationProtocol(Protocol::HTTP);
                    break;
                case Protocol::HTTPS->value:
                    $this->setPublicationProtocol(Protocol::HTTPS);
                    break;
            }

            $this->setPublicationAddress($oUri->getHost() . ':' . $oUri->getPort());

            $path = $oUri->getPath();
            if (str_starts_with($path, '/'))
            {
                $path = substr($path, 1);
            }

            $webServicePart = '/ws/ws1.1cws';
            if (str_contains($path, $webServicePart))
            {
                $baseName = stristr($path, $webServicePart, true);
                if (is_string($baseName) && !empty($baseName))
                {
                    $this->setBaseName($baseName);
                }
            }
        }
    }
}