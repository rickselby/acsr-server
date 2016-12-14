<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /** @var string[] List of request fields that are checkboxes (and might not exist) **/
    protected $checkboxFields = [];

    /** @var string[] List of request fields that should be set to null if they are empty */
    protected $emptyIsNullFields = [];

    /** @var string Date format used on the site */
    protected $dateFormat = 'jS F Y, H:i';

    /** @var string[] List of request fields that should be converted to time  */
    protected $dateFields = [];

    // Authorisating is handled at controller level.
    public function authorize()
    {
        return true;
    }

    /**
     * When validating, first, set any checkbox fields that may be missing from the request;
     * then, run hoursToMinutes on time inputs
     */
    public function validate()
    {
        $this->setCheckboxes();
        $this->setEmptyIsNull();
        parent::validate();
        $this->dateToCarbon();
    }

    /**
     * Set any checkbox fields that might be missing
     */
    protected function setCheckboxes()
    {
        $this->mergeRequest($this->checkboxFields, function($field) {
            return Request::input($field, 0);
        });
    }

    /**
     * Change empty string to null for the given fields
     */
    protected function setEmptyIsNull()
    {
        $this->mergeRequest($this->emptyIsNullFields, function($field) {
            return Request::input($field) === '' ? null : Request::input($field);
        });
    }

    /**
     * Convert dates to carbon instances
     */
    protected function dateToCarbon()
    {
        $this->mergeRequest($this->dateFields, function($field) {
            return Carbon::createFromFormat(
                $this->dateFormat,
                Request::input($field)
            );
        });
    }

    /**
     * Alter the request object for the given fields using the given callback
     *
     * @param string[] $fieldList List of fields to work on
     * @param callback $callback  Function to run on the field
     */
    protected function mergeRequest($fieldList, $callback)
    {
        $mergeArray = [];
        foreach($fieldList AS $field) {
            $mergeArray[$field] = $callback($field);
        }
        Request::merge($mergeArray);
    }
}
