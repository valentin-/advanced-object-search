<?php

namespace ESBackendSearch\FieldDefinitionAdapter;

use DeepCopy\Filter\Filter;
use ESBackendSearch\FieldSelectionInformation;
use ESBackendSearch\FilterEntry;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use Pimcore\Model\Object\ClassDefinition\Data;

class Numeric extends DefaultAdapter implements IFieldDefinitionAdapter {

    /**
     * field type for search frontend
     *
     * @var string
     */
    protected $fieldType = "numeric";

    /**
     * @return array
     */
    public function getESMapping() {
        return [
            $this->fieldDefinition->getName(),
            [
                'type' => 'float',
                'index' => 'not_analyzed'
            ]
        ];
    }


    /**
     * @param $fieldFilter
     *
     * filter field format as follows:
     *   - simple number like
     *       234.54   --> creates TermQuery
     *   - array with gt, gte, lt, lte like
     *      ["gte" => 40, "lte" => 45] --> creates RangeQuery
     *
     * @param bool $ignoreInheritance
     * @param string $path
     * @return BuilderInterface
     */
    public function getQueryPart($fieldFilter, $ignoreInheritance = false, $path = "") {
        if(is_array($fieldFilter)) {
            return new RangeQuery($path . $this->fieldDefinition->getName(), $fieldFilter);
        } else {
            return new TermQuery($path . $this->fieldDefinition->getName(), $fieldFilter);
        }
    }



    /**
     * returns selectable fields with their type information for search frontend
     *
     * @return FieldSelectionInformation[]
     */
    public function getFieldSelectionInformation()
    {
        return [new FieldSelectionInformation(
            $this->fieldDefinition->getName(),
            $this->fieldDefinition->getTitle(),
            $this->fieldType, ['operators' => ['lt', 'lte', 'eq', 'gte', 'gt', FilterEntry::EXISTS, FilterEntry::NOT_EXISTS ]
        ])];
    }

    /**
     * @param Concrete $object
     * @return mixed
     */
    public function getIndexData($object) {
        $value = $this->fieldDefinition->getForWebserviceExport($object);
        if($value) {
            return $value;
        } else {
            return null;
        }
    }


}