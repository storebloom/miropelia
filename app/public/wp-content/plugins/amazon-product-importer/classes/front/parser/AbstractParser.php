<?php
/**
 * Parser abstract class
 *
 * @package: amazon-product-importer
 */
 
namespace Nxtal\AmazonImporter\classes\front\Parser;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

abstract class AbstractParser {

	abstract public function getTitle();

	abstract public function getCategories();

	public function getShortDescription() {
		return null;
	}

	abstract public function getDescription();

	abstract public function getPrice();

	public function getWeight() {
		return array();
	}

	public function getModel() {
		return '';
	}

	public function getDimension() {
		return '';
	}

	public function getPriceCurrency() {
		return 'USD';
	}

	public function getSKU() {
		return null;
	}

	public function getUPC() {
		return null;
	}

	public function getVideos() {
		return array();
	}

	abstract public function getBrand();

	abstract public function getMetaTitle();

	abstract public function getMetaDecription();

	abstract public function getMetaKeywords();

	abstract public function getImages();

	abstract public function getAttributes();

	abstract public function getCombinations();

	public function getFeatures() {
		return array();
	}

	public function getCustomerReviews() {
		return array();
	}

	public function getAttachments() {
		return array();
	}

	protected function strpos( $haystack, $needle, $number = 0) {
		return strpos(
			$haystack,
			$needle,
			$number > 1 ?
			$this->strpos($haystack, $needle, $number - 1) + strlen($needle) : 0
		);
	}

	protected function getJson( $string, $start, $end, $index = 0) {
		$string = ' ' . $string;
		$ini = $this->strpos($string, $start, $index);
		if (0 == $ini) {
			return '';
		}
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
	public function fetch( $url, $data = null, $post = false, $headers = array()) {
		
		try {			
			$agents = array(
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9',
				'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1',
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36 Edg/97.0.1072.62',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Ubuntu/19.04 Chromium/76.0.3809.132 Chrome/76.0.3809.132 Safari/537.36',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Ubuntu/19.10 Chromium/80.0.3987.149 Chrome/80.0.3987.149 Safari/537.36',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_2_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Safari/605.1.15'
			);
			
			$headers[] = 'cache-control: no-cache';
			$headers[] = 'User-Agent: ' . $agents[array_rand($agents)];
			
			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			
			if (true == $post) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				
			} else {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			}
			
			if (null !== $data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, ( is_array($data) ? http_build_query($data) : $data ));
			}
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);			
			
			$response = curl_exec($ch);
			$err = curl_error($ch);			

			curl_close($ch);

			if ($err) {
				return false;
			} else {
				return $response;
			}
		} catch (Exception $e) {
			return false;
		}
	}
}
