<?php
header('Content-Type: application/json');
ini_set('max_execution_time', 0); //no limit 
error_reporting(0);
include('includes/simple_html_dom.php');
include('includes/common_functions.php');
include('includes/functions.php');

$result = array();

$post_code = $_GET['post_code'];
if(empty($post_code))
{
	$result = array('error' => 'Parameter Error');
	echo json_encode($result);
	exit;
}

/*Residential scraping*/
$url = 'https://find-energy-certificate.digital.communities.gov.uk/find-a-certificate/search-by-postcode?postcode='.urlencode($post_code);
$str = curl_request($url);

$html = new simple_html_dom();
$html->load($str);

$elements = $html->find('table.govuk-table a.govuk-link');
foreach ($elements as $item) 
{
	$insert = array();
	$url = 'https://find-energy-certificate.digital.communities.gov.uk'.trim($item->href);
	$str = curl_request($url);

	$html->load($str);
	$html_str = $html->find('html', 0)->innertext;
	$element = $html->find('p.epc-address', 0);
	$property_address = trim($element->plaintext);
	$property_address = replace_new_line($property_address);
	$insert['property_address'] = $property_address;
	
	$element = $html->find('p.epc-rating-result.govuk-body', 0);
	$energy_rating = trim($element->plaintext);
	$insert['energy_rating'] = $energy_rating;

	$element = $html->find('p.govuk-body.epc-extra-box', 0);
	$element = $element->find('b', 0);
	$validity = trim($element->plaintext);
	$insert['validity'] = $validity;

	$element = $html->find('p.govuk-body.epc-extra-box', 1);
	$element = $element->find('b', 0);
	$certificate_number = trim($element->plaintext);
	$insert['certificate_number'] = $certificate_number;

	$property_type = trim(strip_tags(get_string_between($html_str, 'Property type</dt>', '</dd>')));
	$insert['property_type'] = $property_type;
	$total_floor_area = trim(strip_tags(get_string_between($html_str, 'Total floor area', '</dd>')));
	$insert['total_floor_area'] = $total_floor_area;

	$element = $html->find('text.current-potential-number', 0);
	$int_current_energy_rating = (int)trim($element->plaintext);
	$insert['current_energy_rating'] = $int_current_energy_rating;

	$element = $html->find('text.small-letter', 0);
	$chr_current_energy_rating = trim($element->plaintext);
	$insert['current_energy_rating_letter'] = $chr_current_energy_rating;

	$element = $html->find('text.current-potential-number', 1);
	$int_potential_energy_rating = (int)trim($element->plaintext);
	$insert['potential_energy_rating'] = $int_potential_energy_rating;

	$element = $html->find('text.small-letter', 1);
	$chr_potential_energy_rating = trim($element->plaintext);
	$insert['potential_energy_rating_letter'] = $chr_potential_energy_rating;

	$primary_energy_usage = (int)trim(strip_tags(get_string_between($html_str, 'The primary energy use for this property per year is', 'kilowatt hours per square metre')));
	$insert['primary_energy_usage'] = $primary_energy_usage;

	$elements = $html->find('tbody.govuk-table__body', 0)->find('tr');
	$data = array();
	foreach ($elements as $element) 
	{
		$feature = $desc = $rating = '';
		$temp = array();
		$temp['feature'] = trim($element->find('th', 0)->plaintext);
		$temp['desc'] = trim($element->find('td', 0)->plaintext);
		$temp['rating'] = trim($element->find('td', 1)->plaintext);
		$data[] = $temp;
	}
	$insert['performance_breakdown'] = $data;

	/*Recommendations*/
	$recommendations = array();
	$elements = $html->find('h3.govuk-heading-m');
	foreach ($elements as $element) 
	{
		$temp = trim($element->plaintext);
		if(stripos($temp, 'recommendation') !== FALSE){
			$temp = explode(':', $temp)[1];
			$recommendations[] = trim($temp);
		}
	}
	$insert['recommendations'] = $recommendations;
	$insert['isCommercial'] = false;
	$result['properties'][] = $insert;

}


/*Commercial scraping*/
$url = 'https://find-energy-certificate.digital.communities.gov.uk/find-a-non-domestic-certificate/search-by-postcode?postcode='.urlencode($post_code);
$str = curl_request($url);

$html = new simple_html_dom();
$html->load($str);

$elements = $html->find('table.govuk-table a.govuk-link');
foreach ($elements as $item) 
{
	$insert = array();
	$temp = trim($item->plaintext);
	if($temp == 'CEPC')
	{
		$url = 'https://find-energy-certificate.digital.communities.gov.uk'.$item->href;
		$str = curl_request($url);
		$html = new simple_html_dom();
		$html->load($str);

		$html_str = $html->find('html', 0)->innertext;
		$element = $html->find('p.epc-address', 0);
		$property_address = trim($element->plaintext);
		$property_address = replace_new_line($property_address);
		$insert['property_address'] = $property_address;
		

		$element = $html->find('p.epc-rating-result.govuk-body', 0);
		$energy_rating = trim($element->plaintext);
		$insert['energy_rating'] = $energy_rating;

		$element = $html->find('p.govuk-body.epc-extra-box', 0);
		$element = $element->find('b', 0);
		$validity = trim($element->plaintext);
		$insert['validity'] = $validity;

		$element = $html->find('p.govuk-body.epc-extra-box', 1);
		$element = $element->find('b', 0);
		$certificate_number = trim($element->plaintext);
		$insert['certificate_number'] = $certificate_number;

		$property_type = trim(strip_tags(get_string_between($html_str, 'Property type</dt>', '</dd>')));
		$insert['property_type'] = $property_type;
		$total_floor_area = trim(strip_tags(get_string_between($html_str, 'Total floor area', '</dd>')));
		$insert['total_floor_area'] = $total_floor_area;

		$element = $html->find('text.current-potential-number', 0);
		$int_current_energy_rating = (int)trim($element->plaintext);
		$insert['current_energy_rating'] = $int_current_energy_rating;

		$element = $html->find('text.small-letter', 0);
		$chr_current_energy_rating = trim($element->plaintext);
		$insert['current_energy_rating_letter'] = $chr_current_energy_rating;
		$insert['isCommercial'] = true;
		$result['properties'][] = $insert;
	}
}

$result['count'] = count($result['properties']);
echo json_encode($result);
exit;