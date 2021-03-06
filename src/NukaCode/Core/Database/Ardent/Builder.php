<?php
namespace NukaCode\Core\Database\Ardent;


class Builder extends \Illuminate\Database\Eloquent\Builder {

	/**
	 * Forces the behavior of findOrFail in very find method - throwing a {@link ModelNotFoundException}
	 * when the model is not found.
	 *
	 * @var bool
	 */
	public $throwOnFind = false;

	public function find($id, $columns = array('*')) {
		return $this->maybeFail('find', func_get_args());
	}

	public function first($columns = array('*')) {
		return $this->maybeFail('first', func_get_args());
	}

	protected function maybeFail($method, $args) {
		$debug = debug_backtrace(false);

		if ($this->throwOnFind && $debug[2]['function'] != "{$method}OrFail") {
			return call_user_func_array(array($this, $method.'OrFail'), $args);
		} else {
			return call_user_func_array("parent::$method", $args);
		}
	}

}