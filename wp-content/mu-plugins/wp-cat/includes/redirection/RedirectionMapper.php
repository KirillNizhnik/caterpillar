<?php

class RedirectionMapper
{
    private array $uriParts;

    private array $redirectTypes = [
//        'equipment' => EquipmentRedirector::class,
        'new-equipment' => NewRedirector::class,
        'used-equipment' => UsedRedirector::class,
        'used-product' => NotAvailableUsedRedirector::class
    ];

    public function __construct()
    {
        add_action('template_redirect', [$this, 'evaluate_redirect']);
        $this->uriParts = $this->getUriParts($_SERVER['REQUEST_URI']);

    }

    public function evaluate_redirect()
    {
        try {
            if (isset($this->uriParts[0]) && array_key_exists($this->uriParts[0], $this->redirectTypes)) {

                $className = $this->redirectTypes[$this->uriParts[0]];
                /** @var RedirectorAbstract $class */
                $class = new $className($this->uriParts);
                $class->redirect();
            }
        } catch (Exception $e) {
        }
    }

    private function getUriParts(string $uri)
    {
        return explode('/', trim($this->removeFolderFromUrl($uri), '/'));
    }

    private function removeFolderFromUrl(string $uri): string
    {
        $removeFolder = $this->getFolderToRemove();
        if ($removeFolder !== '') {
            $index = strpos($uri, $removeFolder);
            if ($index === 0) {
                $uri = substr_replace($uri, '', 0, strlen($removeFolder));
            }
        }
        return $uri;
    }

    private function getFolderToRemove()
    {
        $slugToRemove = get_option('removeFolderByLink');
        if ($slugToRemove) {
            $slugParts = explode('/', trim($slugToRemove, '/'));
            $slugToRemove = '/' . implode('/', $slugParts);
        }
        return $slugToRemove ?? '';
    }
}

new RedirectionMapper();