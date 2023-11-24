<?php

namespace Cat\Controllers\Importers\Mappers;


class FamilyAssignerByRule
{
    private string $model;
    private string $customTextarea;
    private string $taxonomy;
    private $group_id;
    private $usedFamilyName;

    public function __construct($model, $customTextarea, $taxonomy, $group_id = false, $usedFamilyName = false)
    {
        $this->model=$model;
        $this->customTextarea=$customTextarea;
        $this->taxonomy = $taxonomy;
        $this->group_id = $group_id;
        $this->usedFamilyName = $usedFamilyName;

    }


    public function checkRule($jsonData)
    {
        $rule = json_decode($jsonData, true);
        if (!is_array($rule)) {
            return true;
        }
        $modelNumber = $this->parseModelNumber($this->model);
        $countRules = count($rule);
        $count = 0;

        if (isset($rule['ignore'])) {
            $checkIgnore = $this->checkIgnore($rule, $this->model);
            if ($checkIgnore) {
                $count++;
            } else {
                return false;
            }
        }
        if ($this->group_id) {
            if (isset($rule['groupId'])) {
                $checkGroupId = $this->checkGroupId($rule, $this->group_id);
                if ($checkGroupId) {
                    $count++;
                }
            }
        }
        if ($this->usedFamilyName) {
            if (isset($rule['sourceFamilies'])) {
                $checkSourceFamilies = $this->checkSourceUsedFamilies($rule, $this->usedFamilyName);
                if ($checkSourceFamilies) {
                    $count++;
                }
            }
        }
        if (isset($rule['range']) && $modelNumber) {
            $checkRange = $this->checkRange($rule, $modelNumber);
            if ($checkRange) {
                $count++;
            }
        }
        if (isset($rule['end']) && $modelNumber) {
            $checkNumber = $this->checkParity($rule, $modelNumber);
            if ($checkNumber) {
                $count++;
            }
        }
        if (isset($rule['prefix'])) {
            $checkPrefix = $this->checkPrefix($rule, $this->model);
            if ($checkPrefix) {
                $count++;
            }
        }
        if (isset($rule['endNumber']) && $modelNumber) {
            $checkNumberEnd = $this->checkNumberEnd($rule, $modelNumber);
            if ($checkNumberEnd)
                $count++;
        }
        if (isset($rule['exception'])) {
            $countRules--;
            $checkException = $this->checkException($rule, $this->model);
            if ($checkException) {
                return 9999;
            }
        }
        if ($countRules == $count) {
            return $count;
        } else {
            return true;
        }
    }



    public function checkFamilyByRule()
    {

        $terms = get_terms(array('taxonomy' => $this->taxonomy,
            'hierarchical' => true,
            'hide_empty' => false,));
        $famArray = array();
        foreach ($terms as $key => $term) {
            $rule = get_term_meta($term->term_id, $this->customTextarea, true);
            if ($rule) {
                $response = $this->checkRule( $rule);
                if ($response === false) {
                    return false;
                }
                if ($response !== true) {
                    $famArray[$key] = array(
                        'value' => $response,
                        'key' => $key
                    );
                }
            }
        }
        if (!empty($famArray)) {
            $trueFamilyArray = max($famArray);
            return $terms[$trueFamilyArray['key']];
        } else {
            return true;
        }
    }

    private function checkSourceUsedFamilies($rule, $usedFamilyName): bool
    {
        return in_array($usedFamilyName, $rule['sourceFamilies']);
    }

    private function checkIgnore($rule, $model): bool
    {
        return !str_contains($model, $rule['ignore']);

    }

    private function checkPrefix($rule, $model): bool
    {
        if (is_array($rule['prefix'])) {
            foreach ($rule['prefix'] as $prefix) {
                if (strpos($model, $prefix) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    private function checkNumberEnd($rule, $model): bool
    {
        return $rule['endNumber'] === $this->getEndNumber($model);
    }


    private function checkRange($rule, $modelNumber): bool
    {
        if (is_array($rule['range'])) {
            foreach ($rule['range'] as $range) {
                if ($this->checkRangeValue($range, $modelNumber)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function checkRangeValue(array $range, $numberToCheck): bool
    {
        return isset($range['start'])
            && isset($range['end'])
            && $range['start'] <= $numberToCheck
            && $numberToCheck <= $range['end'];
    }

    private function checkParity($rule, $modelNumber): bool
    {
        return $this->parityTypeCheck($rule['end'], $modelNumber);
    }

    private function parityTypeCheck($type, $number): bool
    {
        switch ($type) {
            case "odd":
                return $number % 2 !== 0;
            case "even":
                return $number % 2 === 0;
            default:
                return false;
        }
    }

    private function checkException($rule, $model): bool
    {
        if (is_array($rule['exception'])) {
            return in_array($model, $rule['exception']);
        }
        return false;
    }


    private function getEndNumber($modelNumber): int
    {
        return $modelNumber % 10;
    }

    private function parseModelNumber($model)
    {
        $pattern = '/(\d+)/';
        preg_match($pattern, $model, $modelNumber);
        return $modelNumber[1] ?? false;
    }

    private function checkGroupId($rule, $groupId): bool
    {
        return $rule['groupId'] == $groupId;
    }
}