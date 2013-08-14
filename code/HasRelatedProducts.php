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
	public static $db = array(
		//'RelatedKeywords'   => 'Varchar(255)',
		'RelatedIDs'        => 'Text', // can be categories and products
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldsToTab('Root.Related', array(
			new HasRelatedProducts_TreeField('RelatedIDs', 'Related Products and/or Categories'),
			//new TextField('RelatedKeywords', 'Keywords for automatically generating related products (will only be used if above is not sufficient or present)'),
		));
	}


	/**
	 * @param int $limit
	 * @return ArrayList
	 */
	public function getRelatedProducts($limit=5) {
		// first look up all the objects they clicked on
		$ids = explode(',', $this->getOwner()->RelatedIDs);
		if (count($ids) == 0) return null;
		$objects = SiteTree::get()->byIDs($ids);

		// then expand any categories into a big list
		$items = array();
		foreach ($objects as $obj) {
			if ((class_exists('ProductCategory') && $obj instanceof ProductCategory) || (class_exists('ProductGroup') && $obj instanceof ProductGroup)) {
				$prods = $obj->ProductsShowable();
				foreach ($prods as $prod) {
					$items[] = $prod;
				}
			} else {
				$items[] = $obj;
			}
		}

		// sort randomly
		$total = count($items);
		$out = new ArrayList();

		if ($total > 0) {
			$limit = min($total, $limit);
			for ($i = 0; $i < $limit; $i++) {
				$out->push($items[rand(0, $total-1)]);
			}
		}

		return $out;
	}

	/**
	 * @param int $limit
	 * @return ArrayList
	 */
	public function RelatedProducts($limit=5) {
		return $this->getRelatedProducts($limit);
	}
}


class HasRelatedProducts_TreeField extends TreeMultiselectField
{
	/**
	 * @param string $name
	 * @param null   $title
	 * @param string $sourceObject
	 * @param string $keyField
	 * @param string $labelField
	 */
	public function __construct($name, $title=null, $sourceObject="SiteTree", $keyField="ID", $labelField="Title") {
		parent::__construct($name, $title, $sourceObject, $keyField, $labelField);

		$buyables = class_exists('EcommerceConfig')
			? EcommerceConfig::get("EcommerceDBConfig", "array_of_buyables")
			: SS_ClassLoader::instance()->getManifest()->getImplementorsOf('Buyable');

		$types = array(
			'(class_exists("ProductCategory") && $obj instanceof ProductCategory)',
			'(class_exists("ProductGroup") && $obj instanceof ProductGroup)'
		);

		if($buyables && is_array($buyables) && count($buyables)) {
			foreach ($buyables as $type) {
				$types[] = '$obj instanceof ' . $type;
			}
		}

		$filter = create_function('$obj', 'return (' . implode(' || ', $types) . ');');
		$this->setFilterFunction($filter);
	}

	/**
	 *
	 * TO DO: explain how this works or what it does.
	 */
	public function saveInto(DataObjectInterface $record) {
		if($this->value !== 'unchanged') {
			$items = array();

			$fieldName = $this->name;

			if($this->value) {
				$items = preg_split("/ *, */", trim($this->value));
			}

			// Allows you to modify the items on your object before save
			$funcName = "onChange$fieldName";
			if($record->hasMethod($funcName)){
				$result = $record->$funcName($items);
				if(!$result){
					return;
				}
			}
			if ($fieldName && ($record->has_many($fieldName) || $record->many_many($fieldName))) {
				// Set related records
				$record->$fieldName()->setByIDList($items);
			}
			else {
				$record->$fieldName = implode(',', $items);
			}
		}
	}
}
