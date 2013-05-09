<?php

/**
 * ModelCache observer
 * This observer should not be active on any production environment but can be enabled temporary to find out what
 * model are loaded multiple times and where this is happening.
 * The results will be written to system.log
 * These items are candidates to be stored in the model cache
 *
 * @author Fabrizio Branca
 * @since 2013-05-08
 */
class Aoe_ModelCache_Model_Observer {

	protected $data = array();
	protected $loadedModels = 0;

	/**
	 * Log data
	 *
	 * @param Varien_Event_Observer $event
	 */
	public function log(Varien_Event_Observer $event) {
		$object = $event->getObject(); /* @var $object Mage_Core_Model_Abstract */
		$class = get_class($object);
		$id = $event->getValue();

		if (!isset($this->data[$class])) {
			$this->data[$class] = array();
		}
		if (!isset($this->data[$class][$id])) {
			$this->data[$class][$id] = array();
		}
		$trace = debug_backtrace();
		$this->data[$class][$id][] = $trace[5]['file'] . ':' . $trace[5]['line'];

		$this->loadedModels++;
	}

	/**
	 * Process data and write to log
	 */
	public function __destruct() {

		// remove every id that was called only once
		foreach ($this->data as $className => $classes) {
			foreach ($classes as $id => $lineAndFiles) {
				if (count($lineAndFiles) <= 1) {
					unset($this->data[$className][$id]);
					if (count($this->data[$className]) == 0) {
						unset($this->data[$className]);
					}
				}
			}
		}

		Mage::log(var_export($this->data, true));
		Mage::log('Total number of loaded models: ' . $this->loadedModels);
	}

}