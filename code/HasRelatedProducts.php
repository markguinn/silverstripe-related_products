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
class HasRelatedProducts extends DataExtension {

	private static $many_many = array(
		'RelatedProductsRelation' => 'Product'
	);

	private static $many_many_extraFields = array(
		'RelatedProductsRelation' => array(
			'Order' => 'Int'
		)
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldsToTab('Root.Related', array(
			GridField::create("RelatedProductsRelation", "Related Products", $this->owner->RelatedProductsRelation()->sort('Order ASC'),
				GridFieldConfig_RelationEditor::create()
					->removeComponentsByType("GridFieldAddNewButton")
					->removeComponentsByType("GridFieldEditButton")
					->addComponent(new GridFieldOrderableRows('Order'))
			)
		));
	}

	/**
	 * @param int $limit
	 * @return DataList
	 */
	public function getRelatedProducts($limit = 5, $random = true) {
		$ids = explode(',', $this->owner->RelatedIDs);
		$filters = array(
			"ID" => $this->owner->RelatedProductsRelation()->getIDList()
		);

		if(Product::config()->related_categories) {
			$filters["ParentID"] = $this->owner->ProductCategories()->getIDList();
			$filters["ParentID"][] = $this->owner->ParentID;
			//TODO: include sub-categories of the chosen categories
				//will result in many queries if there is a lot of nesting
		}

		$products = Product::get()
			->filterAny($filters)
			->limit($limit);

		if($random) {
			$products = $products->sort("RAND()");
		}

		return $products;
	}
}