<?php
/**
 * Can be applied to any buyable to add the related product feature.
 *
 * NOTE: for this to work in it's current form your buyable must be
 * a child of sitetree.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.06.2013
 * @package related_products
 */
class HasRelatedProducts extends DataExtension
{

    private static $many_many
        = array(
            'RelatedProductsRelation' => 'Product'
        );

    private static $many_many_extraFields
        = array(
            'RelatedProductsRelation' => array(
                'Order' => 'Int',
                'RelatedTitle' => 'Varchar'
            )
        );


    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Related', array(
            $grid
                = GridField::create("RelatedProductsRelation", "Related Products", $this->owner->RelatedProductsRelation()
                    ->sort('Order', 'ASC'),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType("GridFieldAddNewButton")
                    ->removeComponentsByType("GridFieldEditButton")
                    ->addComponent(new GridFieldOrderableRows('Order'))
                    ->addComponent(new GridFieldEditableColumns())
            )
        ));

        $grid->getConfig()->getComponentByType('GridFieldEditableColumns')->setDisplayFields(array(
            'RelatedTitle' => function ($record, $column, $grid) {
                return new TextField($column);
            }
        ));
    }


    /**
     * Cleanup
     */
    public function onBeforeDelete()
    {
        $this->owner->RelatedProductsRelation()->removeAll();
    }


    /**
     * @param int $limit [optional]
     * @param bool $random [optional]
     * @return SS_List
     */
    public function getRelatedProducts($limit = null, $random = false)
    {
        $products = $this->owner->RelatedProductsRelation();

        if ($random) {
            $products = $products->sort("RAND()");
        }

        if ($limit) {
            $products = $products->limit($limit);
        }

        // This allows you to easily customise the products,
        // including adding the "HasGeneratedRelatedProducts" extension
        $this->owner->extend('updateRelatedProducts', $products, $limit, $random);

        return $products;
    }


    /**
     * @param int $limit [optional]
     * @param bool $random [optional]
     * @return SS_List
     */
    public function RelatedProducts($limit = null, $random = false)
    {
        return $this->getRelatedProducts($limit, $random);
    }
}
