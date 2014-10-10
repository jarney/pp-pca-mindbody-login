<?php
require_once(dirname(__FILE__) . "/mbApi.php");

class MBClassService extends MBAPIService
{	
	function __construct($debug = false)
	{
		$endpointUrl = "https://" . GetApiHostname() . "/0_5/ClassService.asmx";
		$wsdlUrl = $endpointUrl . "?wsdl";
	
		$this->debug = $debug;
		$option = array();
		if ($debug)
		{
			$option = array('trace'=>1);
		}
		$this->client = new soapclient($wsdlUrl, $option);
		$this->client->__setLocation($endpointUrl);
	}
	
	/**
	 * Returns the raw result of the MINDBODY SOAP call.
	 * @param int $PageSize
	 * @param int $CurrentPage
	 * @param string $XMLDetail
	 * @param string $Fields
	 * @param SourceCredentials $credentials A source credentials object to use with this call
	 * @return object The raw result of the SOAP call
	 */
	public function AddClientsToClasses(array $clientIDs, array $classIDs, $test = false, $PageSize = null, $CurrentPage = null, $XMLDetail = XMLDetail::Full, $Fields = null, SourceCredentials $credentials = null)
	{		
		$additions = array();
		if (isset($clientIDs))
		{
			$additions['ClientIDs'] = $clientIDs;
		}
		if (isset($classIDs))
		{
			$additions['ClassIDs'] = $classIDs;
		}
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		$result = $this->client->AddClientsToClasses($params);
		
		if ($this->debug)
		{
			DebugRequest($this->client);
			DebugResponse($this->client, $result);
		}
		
		return $result;
	}
	
	/**
	 * Returns the raw result of the MINDBODY SOAP call.
	 * @param int $PageSize
	 * @param int $CurrentPage
	 * @param string $XMLDetail
	 * @param string $Fields
	 * @param SourceCredentials $credentials A source credentials object to use with this call
	 * @return object The raw result of the SOAP call
	 */
	public function GetClasses(array $classDescriptionIDs, array $classIDs, array $staffIDs, $startDate, $endDate, $clientID = null, $PageSize = null, $CurrentPage = null, $XMLDetail = XMLDetail::Full, $Fields = NULL, SourceCredentials $credentials = null)
	{		
		$additions = array();
		if (isset($classDescriptionIDs))
		{
			$additions['ClassDescriptionIDs'] = $classDescriptionIDs;
		}
		if (isset($classIDs))
		{
			$additions['ClassIDs'] = $classIDs;
		}
		if (isset($staffIDs))
		{
			$additions['StaffIDs'] = $staffIDs;
		}
		if (isset($startDate))
		{
			$additions['StartDateTime'] = $startDate->format(DateTime::ATOM);
		}
		if (isset($endDate))
		{
			$additions['EndDateTime'] = $endDate->format(DateTime::ATOM);
		}
		if (isset($clientID))
		{
			$additions['ClientID'] = $clientID;
		}
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		try
		{
			$result = $this->client->GetClasses($params);
		}
		catch (SoapFault $fault)
		{
			DebugResponse($this->client);
			// <xmp> tag displays xml output in html
			echo '</xmp><br/><br/> Error Message : <br/>', $fault->getMessage(); 
		}
		
		if ($this->debug)
		{
			DebugRequest($this->client);
			DebugResponse($this->client, $result);
		}
		
		return $result;
	}
	
	/**
	 * Returns the raw result of the MINDBODY SOAP call.
	 * @param string $XMLDetail
	 * @param string $Fields
	 * @param SourceCredentials $credentials A source credentials object to use with this call
	 * @return object The raw result of the SOAP call
	 */
	public function GetClassDescriptions(array $classDescriptionIDs, array $staffIDs, array $locationIDs, $startTime, $endTime, $PageSize = NULL, $CurrentPage = NULL, $XMLDetail = XMLDetail::Full, $Fields = NULL, SourceCredentials $credentials = null)
	{
		$additions = array();
		if (count($classDescriptionIDs) > 0)
		{
			$additions['ClassDescriptionsIDs'] = $classDescriptionIDs;
		}
		if (count($staffIDs) > 0)
		{
			$additions['StaffIDs'] = $staffIDs;
		}
		if (count($locationIDs) > 0)
		{
			$additions['LocationIDs'] = $locationIDs;
		}
		if (isset($startDate))
		{
			$additions['StartClassDateTime'] = $startDate->format(DateTime::ATOM);
		}
		if (isset($endDate))
		{
			$additions['EndClassDateTime'] = $endDate->format(DateTime::ATOM);
		}
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		try
		{
			$result = $this->client->GetClassDescriptions($params);
		}
		catch (SoapFault $fault)
		{
			DebugResponse($result);
			echo '</xmp><br/><br/> Error Message : <br/>', $fault->getMessage(); 
		}
		
		if ($this->debug)
		{
			DebugRequest($this->client);
			DebugResponse($this->client, $result);
		}
		
		return $result;
	}
	/**
	 * Returns the raw result of the MINDBODY SOAP call.
	 * @param string $XMLDetail
	 * @param string $Fields
	 * @param SourceCredentials $credentials A source credentials object to use with this call
	 * @return object The raw result of the SOAP call
	 */
	public function GetEnrollments(array $locationIDs, array $classScheduleIDs, array $staffIDs, array $programIDs, array $sessionTypeIDs, array $semesterIDs, array $courseIDs, $startDate, $endDate, $PageSize = NULL, $CurrentPage = NULL, $XMLDetail = XMLDetail::Full, $Fields = NULL, SourceCredentials $credentials = null)
	{
		$additions = array();
		if (count($locationIDs) > 0)
		{
			$additions['LocationIDs'] = $locationIDs;
		}
		if (count($classScheduleIDs) > 0)
		{
			$additions['ClassScheduleIDs'] = $classScheduleIDs;
		}
		if (count($staffIDs) > 0)
		{
			$additions['StaffIDs'] = $staffIDs;
		}
		if (count($programIDs) > 0)
		{
			$additions['ProgramIDs'] = $programIDs;
		}
		if (count($sessionTypeIDs) > 0)
		{
			$additions['SessionTypeIDs'] = $sessionTypeIDs;
		}
		if (count($semesterIDs) > 0)
		{
			$additions['SemesterIDs'] = $semesterIDs;
		}
		if (count($courseIDs) > 0)
		{
			$additions['CourseIDs'] = $courseIDs;
		}
		if (isset($startDate))
		{
			$additions['StartClassDateTime'] = $startDate->format(DateTime::ATOM);
		}
		if (isset($endDate))
		{
			$additions['EndClassDateTime'] = $endDate->format(DateTime::ATOM);
		}
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		try
		{
			$result = $this->client->GetEnrollments($params);
		}
		catch (SoapFault $fault)
		{
			DebugResponse($result);
			echo '</xmp><br/><br/> Error Message : <br/>', $fault->getMessage(); 
		}
		
		if ($this->debug)
		{
			DebugRequest($this->client);
			DebugResponse($this->client, $result);
		}
		
		return $result;
	}
}
