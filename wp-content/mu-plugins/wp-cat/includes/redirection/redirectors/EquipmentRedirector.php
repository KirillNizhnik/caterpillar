<?php

class EquipmentRedirector extends RedirectorAbstract
{

    public function redirect(): void
    {
        if (isset($this->parts[0]) && $this->parts[0] === 'ag-tractors') {
            $url = $this->getMostSuitableUrl('family', 'equipment') ?? get_home_url() . '/equipment/new/';
            $this->makeRedirect($url);
        }
    }
}