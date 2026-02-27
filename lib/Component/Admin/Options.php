<?php
/*
 * ==================================================
 * This file is part of project Bit UMC - Bitrix integration
 * 04.03.2025
 * ==================================================
*/
namespace ANZ\Appointment\Component\Admin;

use ANZ\Appointment\Component\BaseComponent;
use ANZ\Appointment\Config\Configuration;
use ANZ\Appointment\Config\Constants;
use ANZ\Appointment\Event\EventType;
use ANZ\Appointment\Service\Container;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use CAdminTabControl;
use CFile;
use CModule;
use COption;
use Exception;
use Throwable;

abstract class Options extends BaseComponent
{
    protected array $tabs;
    protected CAdminTabControl $tabControl;

    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams): array
    {
        $arParams = parent::onPrepareComponentParams($arParams);
        $arParams["CACHE_TYPE"] = "N";
        $arParams["CACHE_TIME"] = 0;

        $this->tabs = (key_exists('TABS', $arParams) && is_array($arParams['TABS'])) ? $arParams['TABS'] : [];
        $this->tabControl = new CAdminTabControl('tabControl', $this->tabs);

        return $arParams;
    }

    public function processRequest(): void
    {
        try
        {
            if (!Container::getInstance()->getUserPermissions()->checkOptionsWritePermissions())
            {
                throw new Exception('Current user has no permission to write this module options.');
            }

            if ($this->request->isPost() && $this->request->getPost('Update') && check_bitrix_sessid())
            {
                if (is_array($GROUPS = $this->request->getPost('GROUPS'))
                    && is_array($RIGHTS = $this->request->getPost('RIGHTS'))
                    && is_array($SITES = $this->request->getPost('SITES'))
                )
                {
                    $oModule = CModule::CreateModuleObject($this->moduleId);
                    if (method_exists($oModule, "GetModuleRightList"))
                    {
                        $arModuleRights = call_user_func([$oModule, "GetModuleRightList"]);
                    }
                    else
                    {
                        $arModuleRights = $this->App->GetDefaultRightList();
                    }

                    $arRightsUseSites = [];
                    if (array_key_exists('use_site', $arModuleRights))
                    {
                        foreach ($arModuleRights['use_site'] as $reference_id)
                        {
                            $arRightsUseSites[] = $reference_id;
                        }
                    }

                    $siteIds = array_column(
                        SiteTable::query()->setSelect(['ID'])->where('ACTIVE', 'Y')->fetchAll(),
                        'ID'
                    );

                    Option::delete($this->moduleId, ['name' => 'GROUP_DEFAULT_RIGHT']);//Remove all options
                    $this->App->DelGroupRight($this->moduleId);//Delete group rights for all sites
                    foreach($siteIds as $siteId)
                    {
                        $this->App->DelGroupRight($this->moduleId, [], $siteId);//Delete group rights for site
                    }

                    foreach($GROUPS as $i => $group_id)
                    {
                        if ($group_id === '' || !array_key_exists($i, $RIGHTS) || $RIGHTS[$i] === '')
                        {
                            continue;
                        }

                        if ((int)$group_id === 0)
                        {
                            if (
                                !in_array($RIGHTS[$i], $arRightsUseSites)
                                || $SITES[$i] == ''
                            )
                            {
                                // Set Default for all sites
                                COption::SetOptionString($this->moduleId, "GROUP_DEFAULT_RIGHT", $RIGHTS[$i], "Right for groups by default");
                            }
                            else
                            {
                                // Set Default for site $SITES[$i]
                                COption::SetOptionString($this->moduleId, "GROUP_DEFAULT_RIGHT", $RIGHTS[$i], "Right for groups by default for site ".$SITES[$i], $SITES[$i]);
                            }
                        }
                        else
                        {
                            if (
                                !in_array($RIGHTS[$i], $arRightsUseSites)
                                || $SITES[$i] == ''
                            )
                            {
                                // "Set Right for group ".$group_id." all sites: ".$RIGHTS[$i]."<br>";
                                $this->App->SetGroupRight($this->moduleId, $group_id, $RIGHTS[$i]);
                            }
                            else
                            {
                                // "Set Right for group ".$group_id." ".$SITES[$i].": ".$RIGHTS[$i]."<br>";
                                $this->App->SetGroupRight($this->moduleId, $group_id, $RIGHTS[$i], $SITES[$i]);
                            }
                        }
                    }
                }

                foreach ($this->tabs as $arTab)
                {
                    foreach ($arTab['OPTIONS'] as $arOption)
                    {
                        if(!is_array($arOption) || !empty($arOption['note']))
                        {
                            continue;
                        }

                        $optionName = $arOption[0];
                        if(empty($optionName)/* || !in_array($optionName, $allowedOptions)*/)
                        {
                            continue;
                        }

                        $optionValue = $this->request->getPost($optionName);
                        $optionType  = is_array($arOption[3]) ? $arOption[3][0] : '';
                        if ($optionType === 'file')
                        {
                            $currentValue = Option::get($this->moduleId, $optionName);
                            $optionValue = $this->request->getFile($optionName);

                            if (empty($optionValue['name']) && !empty($currentValue)){
                                continue;
                            }

                            $arFile = $optionValue;
                            $arFile['MODULE_ID'] = $this->moduleId;

                            if (strlen($arFile['name']) > 0)
                            {
                                $fid = CFile::SaveFile($arFile, $arFile['MODULE_ID']);
                                $optionValue = (int)$fid > 0 ? $fid : '';
                            }
                        }
                        elseif ($optionType === 'password')
                        {
                            if (empty($optionValue))
                            {
                                if (!empty(Configuration::getInstance()->getOneCPassword()))
                                {
                                    global $APPLICATION;
                                    $APPLICATION->ThrowException(Loc::getMessage('ANZ_APPOINTMENT_API_PASSWORD_EMPTY_ERROR'));
                                    continue;
                                }
                            }
                            elseif ($optionValue === Constants::PASSWORD_MASKED_VALUE)
                            {
                                continue;
                            }
                            else
                            {
                                $optionValue = Container::getInstance()->getEncryptService()->encrypt($optionValue);
                            }
                        }
                        Option::set(
                            $this->moduleId,
                            $optionName,
                            is_array($optionValue) ? json_encode($optionValue) : $optionValue
                        );
                    }
                }

                $event = new Event(
                    Configuration::getModuleId(),
                    EventType::ON_AFTER_OPTIONS_SET_EVENT,
                    [
                        'moduleId' => Configuration::getModuleId(),
                        'eventType' => EventType::ON_AFTER_OPTIONS_SET_EVENT,
                        'data' => $this->request->getPostList()->toArray()
                    ]
                );
                $event->send();
            }
        }
        catch (Exception $e)
        {
            ShowError($e->getMessage());
        }
    }

    public function getTabControl(): CAdminTabControl
    {
        return $this->tabControl;
    }

    public function getResult(): array
    {
        $this->processRequest();

        return [
            'FORM_ACTION' => $this->getFormAction(),
            'MODULE_ID' => $this->moduleId,
            'TABS' => $this->tabs,
            'SHOW_ACCESS_TAB' => true
        ];
    }

    public function getFormAction(): string
    {
        return $this->request->getRequestedPage() . "?" . http_build_query([
                'mid'  => htmlspecialcharsbx($this->request->get('mid')),
                'lang' => $this->request->get('lang')
            ]);
    }

    public function checkRequirements(): bool
    {
        return true;
    }

    /**
     * @throws \ANZ\Appointment\Core\Exception\ConfigurationException
     */
    public function drawSettingsRow(string $module_id, $option): void
    {
        global $APPLICATION;

        if(empty($option))
        {
            return;
        }

        if(!is_array($option))
        {
            echo "<tr class='heading'><td colspan='2'>$option</td></tr>";
        }
        elseif(isset($option["note"]))
        {
            echo    "<tr>
                        <td colspan='2'>
                            <div class='adm-info-message-wrap'>
                                <div class='adm-info-message'>{$option["note"]}</div>
                            </div>
                        </td>
                    </tr>";
        }
        elseif(isset($option['component']))
        {?>
            <tr>
                <td colspan='2'>
                    <?php
                    $APPLICATION->IncludeComponent(
                        $option['component'],
                        $option['template'] ?: '',
                        is_array($option['params']) ? $option['params'] : []
                    );
                    ?>
                </td>
            </tr>
        <?php }
        elseif($option['group_access'] === 'Y')
        {?>
            <tr>
                <td colspan='2'>
                    <?php
                    global $USER;
                    global $APPLICATION;
                    global $DB;
                    $module_id = Configuration::getModuleId();
                    require_once(Application::getDocumentRoot()."/bitrix/modules/main/admin/group_rights.php");
                    ?>
                </td>
            </tr>
        <?php }
        else
        {
            $currentVal = !empty($option[0]) ? Option::get($module_id, $option[0], $option[2]) : '';
            echo "<tr>";
            $this->renderTitle((string)$option[1]);
            $this->renderInput($option, $currentVal);
            echo "</tr>";
        }
    }

    protected function renderTitle(string $text): void
    {
        echo "<td><span>$text</span></td>";
    }

    /**
     * @throws \Exception
     */
    protected function renderInput(array $option, string $val): void
    {
        $name  = $option[0];
        $type  = $option[3];
        $attrs = is_array($type['attrs']) ? $type['attrs'] : [];

        $attrs['id'] = $name;
        $attrs['name'] = $name;
        if ($type[0] === 'text' || $type[0] === 'password')
        {
            $attrs['size'] = $attrs['size'] ?? '50';
            $attrs['maxlength'] = $attrs['maxlength'] ?? '1000';

            if ($type[0] === 'password')
            {
                $attrs['autocomplete'] = 'new-password';
            }
        }
        elseif ($type[0] === 'number')
        {
            $attrs['size'] = $attrs['size'] ?? '10';
            $attrs['min'] = $attrs['min'] ?? '1';
            $attrs['max'] = $attrs['max'] ?? '999999';
        }
        elseif ($type[0] === 'textarea')
        {
            $attrs['rows'] = $attrs['rows'] ?? '50';
            $attrs['cols'] = $attrs['cols'] ?? '4';
        }
        elseif ($type[0] === 'multiselect')
        {
            $attrs['name'] = $attrs['name'].'[]';
            $attrs['size'] = $attrs['size'] ?? '5';
        }
        unset($attrs['type'], $attrs['value']);

        $attrsString = '';
        foreach($attrs as $attrName => $attrValue)
        {
            $attrsString .= " $attrName='$attrValue'";
        }
        ?>
        <td style="width: 50%">
        <label for="<?=$name?>" class="module-option-label">
            <?
            switch ($type[0])
            {
                case "checkbox":
                    $checked = ($val === "Y") ? "checked" : '';
                    echo "<input type='checkbox' value='Y' $checked $attrsString>";
                    break;
                case "text":
                case "password":
                    $val = $type[0] === 'password' ? Constants::PASSWORD_MASKED_VALUE : htmlspecialchars($val);
                    echo "<input type='$type[0]' value='$val' $attrsString>";
                    break;
                case "number":
                    $val = htmlspecialchars($val);
                    echo "<input type='number' value='$val' $attrsString>";
                    break;
                case "datetime":
                    echo "<input type='datetime-local' value='$val' $attrsString>";
                    break;
                case "hidden":
                    echo "<input type='hidden' value='$val' $attrsString>";
                    break;
                case "select":
                    $arr = is_array($type['LIST']) ? $type['LIST'] : [];
                    echo "<select $attrsString>";
                    foreach($arr as $optionVal => $displayVal)
                    {
                        $displayVal = htmlspecialchars($displayVal);
                        $selected = ($val === (string)$optionVal) ? "selected" : '';
                        echo "<option value='$optionVal' $selected>$displayVal</option>";
                    }
                    echo "</select>";
                    break;
                case "multiselect":
                    $arr = is_array($type['LIST']) ? $type['LIST'] : [];
                    try{
                        $arr_val = json_decode($val, true);
                        if (!is_array($arr_val))
                        {
                            $arr_val = [];
                        }
                    }catch (Throwable){
                        $arr_val = [];
                    }
                    echo "<select multiple $attrsString>";
                    foreach($arr as $optionVal => $displayVal)
                    {
                        $displayVal = htmlspecialchars($displayVal);
                        $selected = (in_array($optionVal, $arr_val)) ? "selected" : '';
                        echo "<option value='$optionVal' $selected>$displayVal</option>";
                    }
                    echo "</select>";
                    break;
                case "textarea":
                    $val = htmlspecialchars($val);
                    echo "<textarea $attrsString>$val</textarea>";
                    break;
                case "staticText":
                    $val = htmlspecialchars($val);
                    echo "<span>".(!empty($val) ? $val : $option[2])."</span>";
                    break;
                case "htmlText":
                    echo "<span style='font-weight: 600'>".(!empty($val) ? $val : $option[2])."</span>";
                    break;
                case "colorPicker":
                    echo "<input type='color' value='$val' $attrsString>";
                    break;
                case "file":
                    if (is_numeric($val) && (int)$val > 0)
                    {
                        $arFile = CFile::GetFileArray($val);
                        if (!empty($arFile))
                        {
                            $fileLink = $arFile['SRC'];
                            $fileName = $arFile['FILE_NAME'];
                            if (CFile::IsImage($fileName))
                            {
                                echo "<div>
                                        <a href='$fileLink' download='$fileName'><img src='$fileLink' alt='image' width='200'></a>
                                      </div>";
                            }
                            else
                            {
                                echo "<div>
                                        <a href='$fileLink' download='$fileName'>$fileName</a>
                                      </div>";
                            }
                        }
                    }
                    echo "<input type='file' $attrsString/>";
                    break;
                default:
                    echo "<p>Unknown option type '$type[0]'</p>";
                    break;
            }
            ?>
        </label>
        </td><?
    }
}