<?php namespace NukaCode\Core\Models;

use NukaCode\Core\Database\Ardent\Ardent;
use Utility_Collection;

class BaseModel extends Ardent {

     /**
     * Set the presenter.  If a specific one does not exist, use the CorePresenter.
     *
     * @return void
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Make sure the uniqueId is always unique.
     * 
     * @param string $model The model to search for the uniqueId on.
     *
     * @return string
     */
    public static function findExistingReferences($model)
    {
        $invalid = true;

        while ($invalid == true) {
            // Create a new random string.
            $uniqueString = Str::random(10);

            // Look for any instances of that string on the model.
            $existingReferences = $model::where('uniqueId', $uniqueString)->count();

            // If none exist, this is a valid unique string.
            if ($existingReferences == 0) {
                $invalid = false;
            }
        }

        return $uniqueString;
    }

    /**
     * Use the custom collection that allows tapping.
     *
     * @param array $models An array of models to turn into a collection.
     *
     * @return Utility_Collection[]
     */
    public function newCollection(array $models = array())
    {
        return new Utility_Collection($models);
    }

    /********************************************************************
     * Scopes
     *******************************************************************/
    /**
     * Order by created_at ascending scope.
     *
     * @param array $query The current query to append to
     */
    public function scopeOrderByCreatedAsc($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Order by name ascending scope.
     *
     * @param array $query The current query to append to
     */
    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Get only active rows.
     *
     * @param array $query The current query to append to
     */
    public function scopeActive($query)
    {
        return $query->where('activeFlag', 1);
    }

    /**
     * Get only inactive rows.
     *
     * @param array $query The current query to append to
     */
    public function scopeInactive($query)
    {
        return $query->where('activeFlag', 0);
    }

    /********************************************************************
     * Model events
     *******************************************************************/

    /**
     * Common tasks needed for all models.
     * Registers the observer if it exists.
     * Sets the default creating event to check for uniqueIds when the model uses them.
     */
    public static function boot()
    {
        parent::boot();

        // Get the possible class names.
        $class        = get_called_class();
        $observer     = $class .'Observer';
        $coreObserver = 'NukaCode\Core\\'. $observer;

        // If the class uses uniqueIds, make sure itis truly unique.
        if (self::testClassForUniqueId($class) == true) {
            $class::creating(function($object) use ($class)
            {
                $object->uniqueId = parent::findExistingReferences($class);
            });
        }

        // Register an observer if one exists.
        if (class_exists($observer)) {
            $class::observe(new $observer);
        } elseif (class_exists($coreObserver)) {
            $class::observe(new $coreObserver);
        }
    }

    /********************************************************************
     * Getters and Setters
     *******************************************************************/
    /**
     * Allow id to be called regardless of the primary key.
     *
     * @param int|null $value The original value of id.
     *
     * @return int|string
     */
    public function getIdAttribute($value)
    {
        if (isset($this->uniqueId)) {
            return $this->uniqueId;
        }

        return $value;
    }

    /********************************************************************
     * Extra Methods
     *******************************************************************/
    /**
     * See if a given class uses uniqueId as the primary key.
     *
     * @param string $class The model to search for the uniqueId on.
     *
     * @return bool
     */
    public static function testClassForUniqueId($class)
    {
        $object = new $class;

        if ($object->primaryKey == 'uniqueId') {
            return true;
        }

        return false;
    }
}