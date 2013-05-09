<?php

/**
 * Class Aoe_ModelCache_Helper_Data
 *
 * @author Fabrizio Branca
 * @since 2013-05-08
 */
class Aoe_ModelCache_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Get model
	 *
	 * @param string $model
	 * @param int $id
	 * @param bool $clean
	 * @return Mage_Core_Model_Abstract|bool
	 */
	public function get($model, $id, $clean=false) {
		if ($clean) {
			$this->removeFromCache($model, $id);
		}
		$hit = true;
		if (!$this->exists($model, $id)) {
			$hit = false;
			if (!isset($this->_cache[$model])) {
				$this->_cache[$model] = array();
			}
			$object = Mage::getModel($model);
			if (!$object) {
				// Mage::throwException(sprintf('Could not find model "%s"', htmlspecialchars($model)));
				Mage::log(sprintf('Could not find model "%s"', htmlspecialchars($model)));
				$object = false;
			} else {
				/* @var $object Mage_Core_Model_Abstract */
				$object->load($id);
				if ($object->getId() != $id) {
					// Mage::throwException(sprintf('Model "%s" with id "%s" not found', htmlspecialchars($model), htmlspecialchars($id)));
					Mage::log(sprintf('Model "%s" with id "%s" not found', htmlspecialchars($model), htmlspecialchars($id)));
				}
			}
			$this->_cache[$model][$id] = $object;
		}
		// Mage::log(($hit?'[HIT]':'[MISS]') .' Model cache: ' . $model . ':' . $id);
		return $this->_cache[$model][$id];
	}

	/**
	 * Check if this object exists
	 *
	 * @param string $model
	 * @param int $id
	 * @return bool
	 */
	public function exists($model, $id) {
		return isset($this->_cache[$model]) && isset($this->_cache[$model][$id]);
	}

	/**
	 * Remove from cache
	 *
	 * @param string $model
	 * @param int $id
	 */
	public function removeFromCache($model, $id) {
		if ($this->exists($model, $id)) {
			unset($this->_cache[$model][$id]);
		}
	}

}
