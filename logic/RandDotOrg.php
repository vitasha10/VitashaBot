<?php

/**
 * PHP API client for random.org true random number generator.
 *
 * @author Robert Paprocki
 * @package RandDotOrg
 */
class RandDotOrg
	{
	/**
	 * RandDotOrg version
	 *
	 * @var decimal
	 */
	const VER = '1.2.0';

	/**
	 * Whether or not this object communicate with random.org using curl
	 * 
	 * @var bool
	 */
	private $uses_curl;

	/**
	 * cURL object used to communicate with random.org
	 * 
	 * @var resource
	 */
	private $curl_ch;

	/**
	 * The user agent sent with the cURL request
	 *
	 * @var string
	 */
	private $user_agent;

	/**
	 * The base URL used to build cURL requests
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * The bit amount needed to make an API request to random.org
	 *
	 * We use this in order to not reach the threshold and get our client's IP banned.
	 * 
	 * @var int
	 */
	private $quota_limit;

	/**
	 * Public constructor
	 *
	 * @param bool Determines whether or not to use SSL in a cURL connection
	 * @param string Additional text to be added to the User Agent. Should include identifying information about the client
	 * @param int Random.org API quota limit
	 * @return void
	 */
	public function __construct( $use_ssl = true, $user_agent = '', $limit = 1000 ) {
		$this->uses_curl = function_exists( 'curl_init' );
		if ( $this->uses_curl ) {
			$this->curl_ch = curl_init();
		}

		$this->user_agent = 'phpRandDotOrg ' . self::VER . ': ' . $user_agent;
		$this->base_url = $this->define_base_url( $use_ssl );
		$this->quota_limit = $limit;
	}

	/**
	 * Public destructor. Closes the cURL channel
	 *
	 * @return void
	 */
	public function __destruct() {
		if ( $this->uses_curl ) {
			curl_close( $this->curl_ch );
		}
	}

	/**
	 * Sets the API quota limit
	 * 
	 * @param int The API quota limit
	 * @return void
	 * @throws Exception
	 */
	public function set_quota_limit( $limit ) {
		if ( isset( $limit ) && intval( $limit ) > 0 ) {
			$this->quota_limit = $limit;
		} else {
			throw new Exception( 'Please make sure the quota limit is a positive integer.' );
		}
	}

	/**
	 * Gets the API quota limit
	 * 
	 * @return int The API quota limit
	 */
	public function get_quota_limit() {
		return $this->$quota_limit;
	}

	/**
	 * Gets a specified number of random integers
	 *
	 * @param int The number of integers requested
	 * @param int The smallest value allowed for each integer
	 * @param int The largest value allowed for each integer
	 * @param int The base that will be used to print the numbers, i.e., binary, octal, decimal or hexadecimal
	 * @return int|array Either a single requested integer or an array of integers
	 * @throws Exception
	 */
	public function get_integers( $num = 1, $min = 0, $max = 10, $base = 10 ) {
		if ( $num < 1 ) {
			throw new Exception( 'Must get at least 1 integer.' );
		}
		if ( $max <= $min) {
			throw new Exception( 'Max must be greater than min.' );
		}
		if ( ! ( $base == 2 || $base == 8 | $base == 10 | $base ==16 ) ) {
			throw new Exception( 'Base must be 2, 8, 10, or 16.' );
		}

		$params = array(    
			'num'   => $num,
			'min'   => $min,
			'max'   => $max,
			'base'  => $base,
		);
		$int = $this->make_request( 'integer', $params );

		return $num == 1 ? $int[0] : $int;
	}

	/**
	 * Randomize a given interval of integers, i.e., arrange them in random order
	 *
	 * @param int The lower bound of the interval (inclusive)
	 * @param int The upper bound of the interval (inclusive)
	 * @return array The randomized order of values
	 * @throws Exception
	 */
	public function get_sequence($params) {
	    $min = $params->minv;
	    $max = $params->maxv;
		if ( intval($min) >= intval($max) ) {
			throw new Exception( 'Max must be greater than min.' );
		}

		$params = array(    
			'min'   => $min,
			'max'   => $max,
		);
		$seq = $this->make_request( 'sequence', $params );

		return $seq;
	}

	/**
	 * Generate truly random strings of various length and character compositions
	 * 
	 * @param int The number of strings requested
	 * @param int The length of the strings. All the strings produced will have the same length
	 * @param bool Determines whether digits (0-9) are allowed to occur in the strings
	 * @param bool Determines whether uppercase alphabetic characters (A-Z) are allowed to occur in the strings
	 * @param bool Determines lowercase alphabetic characters (a-z) are allowed to occur in the strings
	 * @param bool Determines whether the strings picked should be unique
	 * @return string|array Either a single random string of an array of strings
	 */
	public function get_strings( $num = 1, $len = 10, $digits = TRUE, $upperalpha = TRUE, $loweralpha = TRUE, $unique = TRUE ) {
		if ( $num < 1 ) {
			throw new Exception( 'Must request at least 1 string.' );
		}
		if ( $len < 1 || $len > 20 ) {
			throw new Exception( 'String request length must be between 1 and 20.' );
		}
		if ( ! ( $digits || $upperalpha || $loweralpha ) ) {
			throw new Exception( 'At least one character group must be true.' );
		}

		$params = array(    
			'num'        => $num,
			'len'        => $len,
			'digits'     => ( $digits ) ? 'on' : 'off',
			'upperalpha' => ( $upperalpha ) ? 'on' : 'off',
			'loweralpha' => ( $loweralpha ) ? 'on' : 'off',
			'unique'     => ( $unique ) ? 'on' : 'off',
		);
		$str = $this->make_request( 'string', $params );

		return $num == 1 ? $str[0] : $str;
	}  

	/**
	 * Examine the bit quota currently allocated to the server's public IP
	 *
	 * @param string An IP address whose quota to check. Will use the server's own IP if none is provided
	 * @return int The bit quota currently allocated to the specified IP
	 */
	public function quota( $ip = NULL ) {
		$params = array();

		if ( $ip ) {
			$params['ip'] = $ip;
		}  
		$quota = $this->make_request( 'quota', $params );

		return $quota[0];
	}

	/**
	 * Generate and make a request to the public random.org API
	 *
	 * @param string Which type of request to perform
	 * @param array Parameters to be specified in the API query
	 * @return array Parsed data in integer index array format
	 * @throws Exception
	 */
	private function make_request( $type, $params ) {
		$url = $this->base_url;
		switch ( $type ) {
			case 'integer':
				$url .= 'integers/';
				break;
			case 'sequence':
				$url .= 'sequences/';
				break;
			case 'string':
				$url .= 'strings/';
				break;
			case 'quota':
				$url .= 'quota/';
				break;
			default: // this should never happen
				throw new Exception( 'Incorrect API request type specified!' );
				break;
		}
		$url .= "?";
		if(!empty( $params )) {
			$url .= self::query_string( $params );
		}
		$url .= "&" . self::global_params();

		// if we're not making a free request, make sure we have available API quota
		if( $type != 'quota' && $this->quota() < $this->quota_limit ) {
			throw new Exception( 'Not enough quota available! Please wait a while before making additional requests.' );
		}

		if ( $this->uses_curl ) {
			curl_setopt( $this->curl_ch, CURLOPT_URL, $url );
			curl_setopt( $this->curl_ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $this->curl_ch, CURLOPT_FOLLOWLOCATION, TRUE );
			curl_setopt( $this->curl_ch, CURLOPT_USERAGENT, $this->user_agent );
			$raw_data = trim( curl_exec( $this->curl_ch ) );
		} else {
			$raw_data = trim( file_get_contents( $url ) );
		}

		return $this->parse_result( $raw_data );
	}

	/**
	 * Parses the raw data received by the cURL request and handles errors as necessary
	 *
	 * @param string Raw data returned by random.org
	 * @return array Newline-separated array 
	 * @throws Exception
	 */
	private function parse_result( $raw_data ) {
		// Check to see if 'Error:' exists in the returned data, indicating an error.
		if ( strpos( $raw_data, 'Error:' ) !== false ) {
			$error = substr( $raw_data, 7 ); // Remove the 'Error: ' from the beginning.
			throw new Exception( 'RandDotOrg Error: ' . $error );
		}
		// Remove newline from end
		$raw_data = rtrim( $raw_data );
		$parsed_data = explode( "\n", $raw_data );

		return $parsed_data;
	}

	/**
	 * Returns a string with the global parameters
	 *
	 * @return string URL query string with generic parameters
	 */
	private static function global_params() {
		return "col=1&format=plain&rnd=new";
	}

	/**
	 * Form an HTTP query string from a simple array
	 *
	 * @param array An array of API parameters
	 * @return string An acceptable string to be used in a URL query string
	 */
	private static function query_string( $array ) {
		$string = '';
		foreach( $array as $key => $value ) {
			if ( !is_array( $value ) ) {
				$string .= $key . '=' . $value . '&';
			}
		}

		// Remove last &
		return substr( $string, 0, -1 );
	}

	/**
	 * Define the base URL to query
	 *
	 * @param bool Determins whether or not to use SSL
	 * @return string The base URL to query
	 */
	private function define_base_url( $use_ssl ) {
		$proto = $use_ssl ? 'https://' : 'http://';
		$uri = 'www.random.org/';
		return $proto . $uri;
	}
}

?>