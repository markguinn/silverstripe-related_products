<?php
/**
 * 
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 11.13.2014
 * @package related_products
 */
class HasGeneratedRelatedProducts extends DataExtension
{
    /** @var array - which fields to use for generating related products - this would be set on Product in yml */
    private static $related_products_fields = array();

    /** @var string - what class should related products be? */
    private static $related_products_class = 'Product';


    /**
     * @param ManyManyList $products
     */
    public function updateRelatedProducts(&$products, $limit, $random) {
        $curCount = $products->count();
        if ($curCount < $limit) {
            $cfg = Config::inst()->forClass(get_class($this->owner));
            $class = $cfg->get('related_products_class');

            // look up the fields
            $fields = $cfg->get('related_products_fields');
            if (empty($fields)) return;
            if (!is_array($fields)) $fields = array($fields);

            // create a filter from the fields
            $filter = array();
            foreach ($fields as $f) {
                $filter[$f] = $this->owner->getField($f);
            }

            // Convert to an array list so we can add to it
            $products = new ArrayList($products->toArray());

            // Look up products that match the filter
            $generated = DataObject::get($class)
                ->filterAny($filter)
                ->exclude('ID', $this->owner->ID)
                ->sort('RAND()')
                ->limit($limit - $curCount);

            foreach ($generated as $prod) {
                $products->push($prod);
            }
        }
    }
} 
