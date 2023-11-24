<?php

class NewRedirector extends RedirectorAbstract
{

    public function redirect(): void
    {
        $url = $this->getMostSuitableUrl('family', 'equipment') ?? get_home_url() . '/new/';
        $this->makeRedirect($url);
    }
}