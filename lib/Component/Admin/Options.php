<?php
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

        if(isset($arParams['PAGE_TITLE']))
        {
            $this->App->SetTitle($arParams['PAGE_TITLE']);
        }

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
                                Option::set($this->moduleId, "GROUP_DEFAULT_RIGHT", $RIGHTS[$i]);
                            }
                            else
                            {
                                // Set Default for site $SITES[$i]
                                Option::set($this->moduleId, "GROUP_DEFAULT_RIGHT", $RIGHTS[$i], $SITES[$i]);
                            }
                        }
                        else
                        {
                            if (
                                !in_array($RIGHTS[$i], $arRightsUseSites)
                                || $SITES[$i] == ''
                            )
                            {
                                // Set Right for group ".$group_id." all sites: ".$RIGHTS[$i]."<br>";
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
                                $this->assertValidOptionFile($arFile, $arOption);
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
     * @throws \Exception
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
        elseif ($type[0] === 'file')
        {
            $extensions = $this->getFileOptionExtensions($option);
            if (!empty($extensions) && empty($attrs['accept']))
            {
                $attrs['accept'] = implode(',', array_map(static fn($ext) => '.' . $ext, $extensions));
            }
        }
        unset($attrs['type'], $attrs['value']);

        $attrsString = '';
        foreach($attrs as $attrName => $attrValue)
        {
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', (string)$attrName))
            {
                continue;
            }
            $attrsString .= ' ' . $attrName . '="' . htmlspecialcharsbx((string)$attrValue) . '"';
        }
        ?>
        <td style="width: 50%">
        <label for="<?=htmlspecialcharsbx($name)?>" class="module-option-label">
            <?
            switch ($type[0])
            {
                case "checkbox":
                    $checked = ($val === "Y") ? "checked" : '';
                    echo '<input type="checkbox" value="Y" ' . $checked . ' ' . $attrsString . '>';
                    break;
                case "text":
                case "password":
                    $val = $type[0] === 'password' ? Constants::PASSWORD_MASKED_VALUE : htmlspecialcharsbx($val);
                    echo '<input type="' . htmlspecialcharsbx($type[0]) . '" value="' . $val . '" ' . $attrsString . '>';
                    break;
                case "number":
                    $val = htmlspecialcharsbx($val);
                    echo '<input type="number" value="' . $val . '" ' . $attrsString . '>';
                    break;
                case "datetime":
                    echo '<input type="datetime-local" value="' . htmlspecialcharsbx($val) . '" ' . $attrsString . '>';
                    break;
                case "hidden":
                    echo '<input type="hidden" value="' . htmlspecialcharsbx($val) . '" ' . $attrsString . '>';
                    break;
                case "select":
                    $arr = is_array($type['LIST']) ? $type['LIST'] : [];
                    echo '<select ' . $attrsString . '>';
                    foreach($arr as $optionVal => $displayVal)
                    {
                        $displayVal = htmlspecialcharsbx((string)$displayVal);
                        $selected = ($val === (string)$optionVal) ? "selected" : '';
                        $safeOptionVal = htmlspecialcharsbx((string)$optionVal);
                        echo '<option value="' . $safeOptionVal . '" ' . $selected . '>' . $displayVal . '</option>';
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
                    echo '<select multiple ' . $attrsString . '>';
                    foreach($arr as $optionVal => $displayVal)
                    {
                        $displayVal = htmlspecialcharsbx((string)$displayVal);
                        $safeOptionVal = htmlspecialcharsbx((string)$optionVal);
                        $selected = (in_array($optionVal, $arr_val)) ? "selected" : '';
                        echo '<option value="' . $safeOptionVal . '" ' . $selected . '>' . $displayVal . '</option>';
                    }
                    echo "</select>";
                    break;
                case "textarea":
                    $val = htmlspecialcharsbx($val);
                    echo '<textarea ' . $attrsString . '>' . $val . '</textarea>';
                    break;
                case "staticText":
                    $val = htmlspecialcharsbx($val);
                    echo '<span>' . (!empty($val) ? $val : htmlspecialcharsbx((string)$option[2])) . '</span>';
                    break;
                case "link":
                    $val = $option[2];
                    if (is_array($val))
                    {
                        [$linkHref, $linkText] = $val;
                        $displayValue = '<a href="'.htmlspecialcharsbx($linkHref).'" target="_blank">
                                            '.htmlspecialcharsbx($linkText).'
                                        </a>';
                    }
                    else
                    {
                        $displayValue = '';
                    }
                    echo "<span style='font-weight: 600'>".$displayValue."</span>";
                    break;
                case "colorPicker":
                    echo '<input type="color" value="' . htmlspecialcharsbx($val) . '" ' . $attrsString . '>';
                    break;
                case "file":
                    if (is_numeric($val) && (int)$val > 0)
                    {
                        $arFile = CFile::GetFileArray($val);
                        if (!empty($arFile))
                        {
                            $fileLink = htmlspecialcharsbx((string)$arFile['SRC']);
                            $fileName = htmlspecialcharsbx((string)$arFile['FILE_NAME']);
                            if (CFile::IsImage($fileName))
                            {
                                echo '<div><a href="' . $fileLink . '" download="' . $fileName . '"><img src="' . $fileLink . '" alt="image" width="200"></a></div>';
                            }
                            else
                            {
                                echo '<div><a href="' . $fileLink . '" download="' . $fileName . '">' . $fileName . '</a></div>';
                            }
                        }
                    }
                    echo '<input type="file" ' . $attrsString . '/>';
                    break;
                default:
                    echo "<p>Unknown option type '$type[0]'</p>";
                    break;
            }
            ?>
        </label>
        </td><?
    }

    /**
     * @throws \Exception
     */
    private function assertValidOptionFile(array $file, array $option): void
    {
        if ((int)($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK)
        {
            throw new Exception('Upload failed');
        }

        $maxSize = $this->getFileOptionMaxSize($option);
        if ($maxSize > 0 && (int)($file['size'] ?? 0) > $maxSize)
        {
            throw new Exception('File is too large');
        }

        $ext = mb_strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = $this->getFileOptionExtensions($option);
        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions, true))
        {
            throw new Exception('Unsupported file type');
        }

        $imageRules = $this->getFileOptionImageRules($option);
        if (!empty($imageRules))
        {
            $check = CFile::CheckImageFile(
                $file,
                $maxSize,
                (int)($imageRules['maxWidth'] ?? 0),
                (int)($imageRules['maxHeight'] ?? 0)
            );
            if ($check !== null)
            {
                throw new Exception($check);
            }
        }
    }

    private function getFileOptionMaxSize(array $option): int
    {
        $type = is_array($option[3] ?? null) ? $option[3] : [];
        return (int)($type['maxSize'] ?? 0);
    }

    private function getFileOptionExtensions(array $option): array
    {
        $type = is_array($option[3] ?? null) ? $option[3] : [];
        if (!is_array($type['extensions'] ?? null))
        {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn($ext) => mb_strtolower(trim((string)$ext, ". \t\n\r\0\x0B")),
            $type['extensions']
        )));
    }

    private function getFileOptionImageRules(array $option): array
    {
        $type = is_array($option[3] ?? null) ? $option[3] : [];
        return is_array($type['image'] ?? null) ? $type['image'] : [];
    }
}
