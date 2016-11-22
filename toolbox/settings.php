<?php

class Setting
{
	private $m_dbId;
	private $m_name;
	private $m_value;
	private $m_active;
	private $m_date;

	function __construct()
	{
		$m_dbId = 0;
		$m_name = null;
		$m_value = null;
		$m_active = false;
		$m_date = null;
	}
	
	public function setId($dbId)
	{
		$this->m_dbId = $dbId;
	}
	
	public function getId()
	{
		return $this->m_dbId;
	}
	
	public function setName($name)
	{
		$this->m_name = $name;
	}
	
	public function getName()
	{
		return $this->m_name;
	}
	
	public function setValue($value)
	{
		$this->m_value = $value;
	}
	
	public function getValue()
	{
		return $this->m_value;
	}
	
	public function setActive($active)
	{
		$this->m_active = $active;
	}
	
	public function getActive()
	{
		return $this->m_active;
	}
	
	public function setTimeStamp($dateValue)
	{
		$this->m_date = new DateTime($dateValue, new DateTimeZone('America/New_York'));
	}
	
	public function getTimeStamp()
	{
		return $this->m_date;
	}
}

?>