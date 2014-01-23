<?php

namespace Syntax\Core\Utility\View;

use HTML;
use CoreView;

class Crud {

	/**
	 * The title of the page
	 */
	public $title;

	/**
	 * The column to sort on
	 */
	public $sortProperty;

	/**
	 * A flag to switch between multiview and standard
	 */
	public $multiViewFlag = false;

	/**
	 * The column names for the multiView
	 */
	public $multiViewColumns = array();

	/**
	 * The name to display for each object
	 */
	public $multiViewDetails = array();

	/**
	 * The name to display for each property of the object
	 */
	public $multiViewPropertyDetails = array();

	/**
	 * The collection to use for the multiView
	 */
	public $multiViewCollection;

	/**
	 * The property to tap on the collection
	 */
	public $multiViewProperty;

	/**
	 * A flag to show the delete button
	 */
	public $deleteFlag = true;

	/**
	 * The location to send deletes
	 */
	public $deleteLink;

	/**
	 * The column to find the object by
	 */
	public $deleteProperty = 'id';

	/**
	 * The resources that will be displayed
	 */
	public $resources;

	/**
	 * Sets the display to paginated
	 */
	public $paginationFlag = false;

	/**
	 * An array of extra buttons to display
	 */
	public $buttons;

	/**
	 * The fields to display on the page
	 */
	public $displayFields;

	/**
	 * The fields to use in the add/edit form
	 */
	public $formFields;

	public function __construct()
	{
		$this->buttons       = new \stdClass();
		$this->displayFields = new \stdClass();
		$this->formFields    = new \stdClass();
	}

	public function get()
	{
		return $this;
	}

	public function make()
	{
		CoreView::setRoute('helpers.crud')->addData('settings', $this);
	}

	/**
	 * Display multiple results as a parameter (replaces display fields)
	 *
	 * @param  Utility_Collection  $collection
	 * @param  string  $column
	 * @param  string  $property
	 * @return Utility_Crud
	 */
	public function setMulti($collection, $property)
	{
		$this->multiViewFlag       = true;
		$this->deleteFlag          = false;
		$this->multiViewCollection = $collection;
		$this->multiViewProperty   = $property;

		return $this;
	}

	/**
	 * Set the column names for the display
	 *
	 * @param  array  $columns
	 * @return Utility_Crud
	 */
	public function setMultiColumns($columns)
	{
		$this->multiViewColumns = $columns;

		return $this;
	}

	/**
	 * Set the property to tap on the collection for it's name and the field this pertains to
	 *
	 * @param  array  $details
	 * @return Utility_Crud
	 */
	public function setMultiDetails($details)
	{
		$this->multiViewDetails = $details;

		return $this;
	}

	/**
	 * Set the property to tap on the collection's property for it's name and the field this pertains to
	 *
	 * @param  array  $details
	 * @return Utility_Crud
	 */
	public function setMultiPropertyDetails($details)
	{
		$this->multiViewPropertyDetails = $details;

		return $this;
	}

	/**
	 * Ad an extra button per row
	 *
	 * @param  string  $name
	 * @param  string  $linkLocation
	 * @param  string  $linkName
	 * @param  array   $options
	 * @return Utility_Crud
	 */
	public function addButton($name, $linkLocation, $linkName, $options = array())
	{
		$this->buttons->{$name} = HTML::link($linkLocation, $linkName, $options);

		return $this;
	}

	/**
	 * Add a field to the display
	 *
	 * @param  string  $fieldName
	 * @param  string  $linkLocation
	 * @param  string  $linkProperty
	 * @return Utility_Crud
	 */
	public function addDisplayField($fieldName, $linkLocation = null, $linkProperty = null)
	{
		$this->displayFields->{$fieldName}               = new \stdClass();
		$this->displayFields->{$fieldName}->linkLocation = $linkLocation;
		$this->displayFields->{$fieldName}->linkProperty = $linkProperty;

		return $this;
	}

	/**
	 * Add a field to the form
	 *
	 * @param  string  $fieldName
	 * @param  string  $fieldType
	 * @param  array   $selectArray
	 * @param  string  $placeholder
	 * @return Utility_Crud
	 */
	public function addFormField($fieldName, $fieldType, $selectArray = null, $required = false, $placeholder = null)
	{
		$this->formFields->{$fieldName}              = new \stdClass();
		$this->formFields->{$fieldName}->field       = $fieldType;
		$this->formFields->{$fieldName}->selectArray = $selectArray;
		$this->formFields->{$fieldName}->required    = $required;
		$this->formFields->{$fieldName}->placeholder = $placeholder;

		return $this;
	}

	/**
	 * Set the pagination flag
	 *
	 * @param  string  $paginationFlag
	 * @return Utility_Crud
	 */
	public function setPaginationFlag($paginationFlag)
	{
		$this->paginationFlag = $paginationFlag;

		return $this;
	}

	/**
	 * Set the title
	 *
	 * @param  string  $title
	 * @return Utility_Crud
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Set the resources
	 *
	 * @param  Utility_Collection  $resources
	 * @return Utility_Crud
	 */
	public function setResources($resources)
	{
		$this->resources = $resources;

		return $this;
	}

	/**
	 * Set the sort property
	 *
	 * @param  string  $sortProperty
	 * @return Utility_Crud
	 */
	public function setSortProperty($sortProperty)
	{
		$this->sortProperty = $sortProperty;

		return $this;
	}

	/**
	 * Set the delete flag
	 *
	 * @param  string  $deleteFlag
	 * @return Utility_Crud
	 */
	public function setDeleteFlag($deleteFlag)
	{
		$this->deleteFlag = $deleteFlag;

		return $this;
	}

	/**
	 * Set the delete link
	 *
	 * @param  string  $deleteLink
	 * @return Utility_Crud
	 */
	public function setDeleteLink($deleteLink)
	{
		$this->deleteLink = $deleteLink;

		return $this;
	}

	/**
	 * Set the delete property
	 *
	 * @param  string  $deleteProperty
	 * @return Utility_Crud
	 */
	public function setDeleteProperty($deleteProperty)
	{
		$this->deleteProperty = $deleteProperty;

		return $this;
	}
}