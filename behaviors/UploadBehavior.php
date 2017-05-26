<?php

namespace app\behaviors;

/**
 * Class UploadBehavior
 * @package app\behaviors
 */
class UploadBehavior extends \trntv\filekit\behaviors\UploadBehavior
{
    public $documentType;

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        if ($this->documentType) {
            $fields['document_type'] = $this->attributePrefix . $this->documentType;
        }

        return $fields;
    }
}
