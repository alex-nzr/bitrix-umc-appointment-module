<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?php
$bSuccess = false;
$arResult = array();
$arErrors = array();
$arUpdater = array();
$arSettings = array();

$arSettings["MODULE_ID"] = 'anz.appointment';
$arSettings["U_PATH"] = '/iupdate/';

$arSettings["MODULE_PATH"] = $_SERVER["DOCUMENT_ROOT"].'/local/modules/'.$arSettings["MODULE_ID"].'/';
$arSettings["UPDATE_PATH"] = $_SERVER["DOCUMENT_ROOT"].'/'.$arSettings["U_PATH"];

$arSettings["DIR_READ_NOFOLLOW"] = array(
    $arSettings["MODULE_PATH"].'admin/',
    $arSettings["MODULE_PATH"].'install/',
    $arSettings["MODULE_PATH"].'lang/ru/install/',
    $arSettings["MODULE_PATH"].'lang/ru/admin/',
    $arSettings["MODULE_PATH"].'lang/ru/lib/',
    $arSettings["MODULE_PATH"].'lib/Config/',
    $arSettings["MODULE_PATH"].'lib/Controllers/',
    $arSettings["MODULE_PATH"].'lib/Event/',
    $arSettings["MODULE_PATH"].'lib/Model/',
    $arSettings["MODULE_PATH"].'lib/Services/',
    $arSettings["MODULE_PATH"].'lib/Soap/',
    $arSettings["MODULE_PATH"].'lib/Tools/',
);

$arSettings["DIR_NOFOLLOW"] = array(
    $arSettings["MODULE_PATH"].'lang/ru/install/',
    $arSettings["MODULE_PATH"].'lang/ru/admin/',
);

$arSettings["DIR_SKIP"] = array();

$arSettings["FILE_NAME_SKIP"] = array(
    '.',
    '..',
    '.hg',
    '.hgignore',
    '.svn',
    '.csv',
);

$arSettings["FILE_SKIP"] = array(
    $arSettings["MODULE_PATH"].'install/version.php',
    $arSettings["MODULE_PATH"].'version_control.php',
);

$arSettings["UPDATER_COPY"] = array(
    "install/js" => "js/anz/appointment",
    "install/components" => "components",
);

if(array_key_exists("UPD",$_POST))
{
    $UPD = $_POST["UPD"];
    
    //remove if exists
    if($UPD["REMOVE_UPD"]=='Y')
        DeleteDirFilesEx($arSettings["U_PATH"].'/'.$UPD["VERSION"].'/');    
    
    if(!isset($UPD["VERSION"])
        || !preg_match('~^\d{1,}\.\d{1,}\.\d{1,}$~i', $UPD["VERSION"])
    )
        $arErrors[] = 'Некорректная версия обновления';
    if(!$UPD["DESCRIPTION"])
        $arErrors[] = 'Пустое описание';
    if(!isset($UPD["FOLDERS"]) && !isset($UPD["FILES"]))
        $arErrors[] = 'Не выбраны файлы для сборки обновления';
    if(is_dir($arSettings["UPDATE_PATH"].$UPD["VERSION"]))
        $arErrors[] = 'Директория дла данного обновления уже занята';
    if(empty($arErrors))
    {
        if(!isset($UPD["FOLDERS"]))
            $UPD["FOLDERS"] = array();
        if(!isset($UPD["FILES"]))
            $UPD["FILES"] = array();
        //copy files
        foreach($UPD["FOLDERS"] as $folder)
            CopyDirFiles($arSettings["MODULE_PATH"].$folder, $arSettings["UPDATE_PATH"].$UPD["VERSION"].'/'.$folder, true, true);

        foreach($UPD["FILES"] as $file)
            CopyDirFiles($arSettings["MODULE_PATH"].$file, $arSettings["UPDATE_PATH"].$UPD["VERSION"].$file, true, true);
        
        //write version
        if(!is_dir($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/install/'))
            mkdir($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/install/');
        $fp = fopen($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/install/version.php', 'w+');
        fwrite($fp, '<?
$arModuleVersion = array(
	"VERSION" => "'.$UPD["VERSION"].'",
	"VERSION_DATE" => "'.date("Y-m-d H:i:s").'"
);
?>');
        fclose($fp);
        
        //write description
        $fp = fopen($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/description.ru', 'w+');
        fwrite($fp, $UPD["DESCRIPTION"]);
        fclose($fp);

        foreach($arSettings["UPDATER_COPY"] as $cFrom => $cTo)
        {
            if(file_exists($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/'.$cFrom))
                $arUpdater[] = '$updater->CopyFiles("'.$cFrom.'", "'.$cTo.'");';
        }
        if(!empty($arUpdater))
        {
            //write updater
            $fp = fopen($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/updater.php', 'w+');
            fwrite($fp, '<?
'.join("\n",$arUpdater).'
?>');
            fclose($fp);
        }
        
        //cleanup file_name_skip
        iCleanUp($arSettings["UPDATE_PATH"].$UPD["VERSION"].'/');

        //tar update
        if($UPD["ARCHIVE"] =="Y")
        {
            unlink($arSettings["UPDATE_PATH"]."/".$UPD["VERSION"].".tar.gz");
            $tempFile = $arSettings["UPDATE_PATH"]."/".$UPD["VERSION"].".tar.gz";
            $bUseCompression = true;
            if(!extension_loaded('zlib') || !function_exists("gzcompress"))
                    $bUseCompression = false;

            require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/tar_gz.php");
            $oArchiver = new CArchiver($tempFile, $bUseCompression);
            $success = $oArchiver->add($arSettings["UPDATE_PATH"].$UPD["VERSION"], false, $arSettings["UPDATE_PATH"].$UPD["VERSION"]);
            if(!$success)
                $arErrors[] = 'Не удалось создать архив';
        }

                
        $bSuccess = true;
    }
}

function iCleanUp($path)
{
    global $arSettings;
    if ($handle = opendir($path)) 
    {
        while (false !== ($file = readdir($handle))) 
        {
            if($file == '.' || $file == '..') continue;
            //FILE_NAME_CLEANUP
            if(in_array($file, $arSettings["FILE_NAME_SKIP"]))
            {
                $tmpPath = str_replace($_SERVER["DOCUMENT_ROOT"],'',$path.$file.'/');
                DeleteDirFilesEx($tmpPath);
            }
            
            if(is_dir($path.$file.'/'))
                iCleanUp($path.$file.'/');      
        }        
    }
    closedir($handle);
}

function iReadDir($path)
{
    global $arResult, $arSettings;
    if ($handle = opendir($path)) 
    {
        while (false !== ($file = readdir($handle))) 
        {
            //FILE_NAME_SKIP
            if(in_array($file, $arSettings["FILE_NAME_SKIP"])) 
                continue;

            //DIR_READ_NOFOLLOW
            if(is_dir($path.$file.'/') && in_array($path.$file.'/', $arSettings["DIR_SKIP"]))
                continue;
            elseif(is_dir($path.$file.'/') && in_array($path.$file.'/', $arSettings["DIR_NOFOLLOW"]))
            {
                $arResult[$path.$file.'/'] = array();
                continue;
            }
            elseif(in_array($path, $arSettings["DIR_READ_NOFOLLOW"]))
            {
                if(is_dir($path.$file.'/'))
                    $arResult[$path.$file.'/'] = array();
                else
                    $arResult[$path][] = $file;
                
                continue;
            }
            else
            {
                if(is_dir($path.$file.'/'))
                {
                    if(!array_key_exists($path, $arResult))
                        $arResult[$path] = array();
                    iReadDir($path.$file.'/');
                }
                else
                {   
                    //!FILE_SKIP
                    if(!in_array($path.$file, $arSettings["FILE_SKIP"])) 
                        $arResult[$path][] = $file;
                }
            }
        }

        closedir($handle);
    }   
}

//start reading
iReadDir($arSettings["MODULE_PATH"]);
?>
<?$l = strlen($arSettings["MODULE_PATH"])-1;?>
<h1>Создание Updater'ов для модуля <?=$arSettings["MODULE_ID"]?></h1>
<?if(!empty($arErrors)):?>
    <div style="color:red;"><?=join('<br/>', $arErrors)?></div>
<?endif;?>
<?if($bSuccess):?>
    <div style="color:green;">обновление <?=$UPD["VERSION"]?> собрано</div>
<?endif;?>
<form action="<?=$SERVER["PHP_SELF"]?>" method="POST">
    <table>
        <tr>
            <td valign="top">
                Версия: <input type="text" name="UPD[VERSION]" value="<?=htmlspecialchars($UPD["VERSION"])?>"><br/>
                Описание:<br/>
                <textarea name="UPD[DESCRIPTION]" style="width:300px;" rows="7"><?=htmlspecialchars($UPD["DESCRIPTION"])?></textarea>
                <br/><br/>
                <input type="submit" value="Собрать"><br/>
                <input type="checkbox" name="UPD[REMOVE_UPD]" value="Y"> Очистить директорию сборки<br/>
                <input type="checkbox" name="UPD[ARCHIVE]" value="Y"> Создать архив<br/>
            </td>
            <td align="left" valign="top" style="padding-left:25px;">
        <?foreach($arResult as $path=>$arFile):?>
            <input type="checkbox" value="<?=substr($path, $l)?>" name="UPD[FOLDERS][]"><b><?=substr($path, $l)?></b><br/>
            <?foreach($arFile as $file):?>
                <input type="checkbox" value="<?=substr($path, $l).$file?>" name="UPD[FILES][]"><small><i><?=$file?></i></small><br/>
            <?endforeach;?>
        <?endforeach;?>
            </td>
        </tr>
    </table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>