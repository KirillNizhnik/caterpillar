<?php

namespace Cat\Core;

class Product_Factory extends \Cat\Core\Abstracts\Factory
{

    /**
     * Get an item from the collection by key.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($the_product = false, $default = null)
    {
        $the_product = $this->get_object($the_product);
        if (! $the_product instanceof \WP_Post) {
            return false;
        }
        if ($this->contains('id', $the_product->ID)) {
            //echo '<pre>'; var_dump('where'); echo '</pre>'; die;
            return $this->where('id', $the_product->ID);
        }

        if ($this->offsetExists($the_product->ID)) {
            //echo '<pre>'; var_dump('where'); echo '</pre>'; die;
            return $this->items[$the_product->ID];
        }

        $this->add(new \Cat\Models\Product($the_product));
        return $this->last();
    }


    private function get_object($the_product)
    {
        /* if (!is_object($the_product)){
             $the_product = "";
             return;
         } */
        if (false === $the_product) {
            $the_product = $GLOBALS['post'];
        } elseif (is_numeric($the_product)) {
            $the_product = get_post($the_product);
        } elseif ($the_product instanceof \Cat\Models\Product) {
            $the_product = get_post($the_product->id);
        } elseif ($the_product instanceof \WP_Post) {
            if ($the_product->post_type !== 'equipment') {
                $the_product = false;
            }
        }


        return $the_product;
    }

}