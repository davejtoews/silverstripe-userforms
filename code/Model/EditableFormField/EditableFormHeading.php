<?php

namespace SilverStripe\UserForms\Model\EditableFormField;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\UserForms\Model\EditableFormField;

/**
 * Allows an editor to insert a generic heading into a field
 *
 * @package userforms
 */
class EditableFormHeading extends EditableFormField
{
    private static $singular_name = 'Heading';

    private static $plural_name = 'Headings';

    private static $literal = true;

    private static $db = [
        'Level' => 'Int(3)', // From CustomSettings
        'HideFromReports' => 'Boolean(0)' // from CustomSettings
    ];

    private static $defaults = [
        'Level' => 3,
        'HideFromReports' => false
    ];

    private static $table_name = 'EditableFormHeading';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['Default', 'Validation', 'RightTitle']);

        $levels = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6'
        ];

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create(
                'Level',
                _t(__CLASS__.'.LEVEL', 'Select Heading Level'),
                $levels
            ),
            CheckboxField::create(
                'HideFromReports',
                _t('SilverStripe\\UserForms\\Model\\EditableFormField\\EditableLiteralField.HIDEFROMREPORT', 'Hide from reports?')
            )
        ]);

        return $fields;
    }

    public function getFormField()
    {
        $labelField = HeaderField::create('userforms-header', $this->EscapedTitle)
            ->setHeadingLevel($this->Level);
        $labelField->addExtraClass('FormHeading');
        $labelField->setAttribute('data-id', $this->Name);
        $this->doUpdateFormField($labelField);
        return $labelField;
    }

    protected function updateFormField($field)
    {
        // set the right title on this field
        if ($this->RightTitle) {
            // Since this field expects raw html, safely escape the user data prior
            $field->setRightTitle(Convert::raw2xml($this->RightTitle));
        }
        // if this field has an extra class
        if ($this->ExtraClass) {
            $field->addExtraClass($this->ExtraClass);
        }
    }

    public function showInReports()
    {
        return !$this->HideFromReports;
    }

    public function getFieldValidationOptions()
    {
        return false;
    }

    public function getSelectorHolder()
    {
        return "$(\":header[data-id='{$this->Name}']\")";
    }

    public function getLevel()
    {
        return $this->getField('Level') ?: 3;
    }
}