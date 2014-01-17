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

	public static $many_many = array(
		'RelatedProductsRelation' => 'Product'
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldsToTab('Root.Related', array(
			GridField::create("RelatedProductsRelation", "Related Products", $this->owner->RelatedProductsRelation(),
				GridFieldConfig_RelationEditor::create()
					->removeComponentsByType("GridFieldAddNewButton")
					->removeComponentsByType("GridFieldEditButton")
			)
		));
	}

	/**
	 * @param int $limit
	 * @return DataList
	 */
	public function getRelatedProducts($limit=5) {
		$ids = explode(',', $this->owner->RelatedIDs);
		$filters = array(
			"ID" => $this->owner->RelatedProductsRelation()->getIDList()
		);
		if(Product::config()->related_categories){
			$filters["ParentID"] = $this->owner->ProductCategories()->getIDList();
			$filters["ParentID"][] = $this->owner->ParentID;
			//TODO: include sub-categories of the chosen categories
				//will result in many queries if there is a lot of nesting
		}

		return Product::get()
			->filterAny($filters)
			->limit($limit)
			->sort("RAND()");
	}

	/**
	* @param int $limit
	* @return ArrayList
	*/
	public function RelatedProducts($limit=5) {
	    return $this->getRelatedProducts($limit);
	}

}