<?php

class Preferences extends Moore_Db_Table 
{
	protected $_name = 'preferences';
	
	/**
	 * Save preferences
	 */	
	public function save($data)
	{
	    foreach ($data as $preferenceName => $preferenceValue) {
	        if (!$this->update(array('preference_value' => $preferenceValue), "preference_name = '$preferenceName'"))
	            return false;
	    }
	    return true;
	}
	
	/**
	 * Get preferences
	 */		
	public function getPreferences()
	{
		$rows = $this->fetchAll();
		if($rows->count()>0){
			$rows = $rows->toArray();
			foreach ($rows as $row) {
			    $preferences[$row['preference_name']] = $row['preference_value'];
			}
			return $preferences;
		}
		return false;
	}
	
	/**
	 * Get preference by preference name
	 */		
	public function getPreferenceByName($name)
	{
		$row = $this->fetchRow("preference_name = '$name'");
		if ($row) {
			$row = $row->toArray();
			return $row['preference_value'];
		}
		return false;
	}	
	
	public function updateVisit()
	{
		$row = $this->getPreferenceByName('site_visit');
		$array = array('preference_value'=>(int)$row + 1);
		$result = $this->update($array,"preference_name = 'site_visit'");
		return $result;
	}
}