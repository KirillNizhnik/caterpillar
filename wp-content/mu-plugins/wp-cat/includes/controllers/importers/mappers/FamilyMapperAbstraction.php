<?php

namespace Cat\Controllers\Importers\Mappers;

abstract class FamilyMapperAbstraction
{
    protected string $taxonomy;
    protected string $familyName;
    protected string $subfamilyName;
    protected ?FamilyAssignerByRule $assignerByRule;

    public function __construct(string $familyName, ?FamilyAssignerByRule $assignerByRule, string $taxonomy, $subfamilyName = false)
    {
        $this->taxonomy = $taxonomy;
        $this->familyName = $familyName;
        $this->assignerByRule = $assignerByRule;
        $this->subfamilyName = $subfamilyName;
    }

    public function applyFamily()
    {
        $response = true;
        if ($this->assignerByRule !== null) {
            $response = $this->assignerByRule->checkFamilyByRule();
        }
        if ($this->isTerm($response)) {
            return $this->getTermsList($response);
        } elseif ($response === false) {
            return false;
        } else {
            return $this->getTermsByFamilies();
        }
    }


    private function getTermsByFamilies()
    {
        $parentTerm = $this->getTermByName($this->familyName);
        $childTerm = $this->getTermByName($this->subfamilyName);
        if ($this->isTerm($childTerm)) {
            return $this->getTermsList($childTerm);
        } elseif ($this->isTerm($parentTerm)) {
            return $this->getTermsList($parentTerm);
        } else {
            return true;
        }
    }

    protected function getTermsList(\WP_Term $term): array
    {
        $terms = get_ancestors($term->term_id, $term->taxonomy);
        $terms[] = $term->term_id;
        return $terms;
    }


    protected function isTerm($term): bool
    {
        return $term instanceof \WP_Term;
    }

    protected function getTermByName($categoryName)
    {
        if ($categoryName === false) {
            return false;
        }
        return get_term_by('name', $categoryName, $this->taxonomy);
    }

}