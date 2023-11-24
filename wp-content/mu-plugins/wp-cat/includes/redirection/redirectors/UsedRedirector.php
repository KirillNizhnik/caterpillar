<?php

class UsedRedirector extends RedirectorAbstract
{

    public function redirect(): void
    {
        $url = $this->getMostSuitableUrl('used-family', 'used-equipment') ?? get_home_url() . '/used/';
        $this->makeRedirect($url);
    }
}