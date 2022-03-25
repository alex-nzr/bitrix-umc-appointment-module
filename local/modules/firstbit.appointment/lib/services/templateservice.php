<?php
namespace AlexNzr\BitUmcIntegration\Service;


class TemplateService{
    /** generate params template
     * @return array
     */
    public function generateTemplateParams(): array
    {
        $settings = file_get_contents(__DIR__.'/../../templates/settings.json');
        $result = json_decode($settings, true);
        if (is_array($result))
        {
            if ($result["selectDoctorBeforeService"] === "Y"){
                $altSelectionBlocks = [
                    "clinicsBlock"      => $result["selectionBlocks"]["clinicsBlock"],
                    "specialtiesBlock"  => $result["selectionBlocks"]["specialtiesBlock"],
                    "employeesBlock"    => $result["selectionBlocks"]["employeesBlock"],
                    "servicesBlock"     => $result["selectionBlocks"]["servicesBlock"],
                    "scheduleBlock"     => $result["selectionBlocks"]["scheduleBlock"],
                ];
                $result["selectionBlocks"] = $altSelectionBlocks;
            }
        }
        else
        {
            $result = [];
        }
        return $result;
    }
}