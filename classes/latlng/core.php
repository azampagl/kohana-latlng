<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Latitude and Longitude functions.
 *
 * @see http://williams.best.vwh.net/avform.htm
 * @see http://www.movable-type.co.uk/scripts/latlong.html
 *
 * @package    Latlng
 * @author     azampagl
 * @license    ISC
 */
class Latlng_Core {

	// Radius of the earth, in km
	const R = 6378.1;

	/**
	 * Gets a new coordinate given a initial coordinate,
	 * heading, and distance to travel.
	 *
	 * @param   array     starting coordinate
	 * @param   float     heading
	 * @param   float     distance (km)
	 * @return  array
	 */
	public static function coord(array $coord, $heading, $dist)
	{
		// Convert to radians
		list($lat1, $lng1) = Latlng::deg2rad($coord);
		$heading = deg2rad($heading);

		$lat2 = asin(sin($lat1) * cos($dist / Latlng::R) + cos($lat1) *
			sin($dist / Latlng::R) * cos($heading));
		$lng2 = $lng1 + atan2(sin($heading) * sin($dist / Latlng::R) *
			cos($lat1), cos($dist / Latlng::R) - sin($lat1) * sin($lat2));

		return Latlng::rad2deg(array($lat2, $lng2));
	}

	/**
	 * Converts a coordinate from degrees to radians.
	 *
	 * @param   array   coordinate
	 * @return  array
	 */
	public static function deg2rad(array $coord)
	{
		return array(deg2rad($coord[0]), deg2rad($coord[1]));
	}

	/**
	 * Distance between two points.
	 *
	 * @param   array     coordinate 1
	 * @param   array     coordinate 2
	 * @param   boolean   use earth's radius
	 * @return  float
	 */
	public static function distance(array $coord1, array $coord2, $radius = TRUE)
	{
		// Convert to radians
		list($lat1, $lng1) = Latlng::deg2rad($coord1);
		list($lat2, $lng2) = Latlng::deg2rad($coord2);

		// Difference should be calculated using degrees, more accurate
		$dlat = deg2rad($coord1[0] - $coord2[0]);
		$dlng = deg2rad($coord1[1] - $coord2[1]);

		$c = 2 * asin(sqrt(pow((sin(($dlat) / 2)), 2 ) +
			cos($lat1) * cos($lat2) * pow(sin(($dlng) / 2), 2)));

		if ($radius)
		{
			$c = Latlng::R * $c;
		}

		return $c;
	}

	/**
	 * Heading (bearing) between two points (in degrees).
	 *
	 * @see http://mathforum.org/library/drmath/view/55417.html
	 * @see http://williams.best.vwh.net/avform.htm
	 *
	 * @param   array     coordinate 1
	 * @param   array     coordinate 2
	 * @param   boolean   result is degrees (else radians)
	 * @return  float
	 */
	public static function heading(array $coord1, array $coord2, $degrees = TRUE)
	{
		// Convert to radians
		list($lat1, $lng1) = Latlng::deg2rad($coord1);
		list($lat2, $lng2) = Latlng::deg2rad($coord2);

		$y = sin($lng2 - $lng1) * cos($lat2);
		$x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lng2 - $lng1);

		$heading = fmod(atan2($y, $x), 2 * pi());

		if ($degrees)
		{
			$heading = $heading * (180 / pi());
		}

		return $heading;
	}

	/**
	 * Returns an intermediate point between two coordinates based on a given
	 * fractional amount (between 0 and 1).
	 *
	 * @see http://fraserchapman.blogspot.com/2008/09/intermediate-points-on-great-circle.html
	 *
	 * @param   array   coordinate 1
	 * @param   array   coordinate 2
	 * @param   float   fraction of the distance
	 * @return  array
	 */
	public static function intermediate(array $coord1, array $coord2, $frac)
	{
		$dist = Latlng::distance($coord1, $coord2, FALSE);

		// Convert to radians
		list($lat1, $lng1) = Latlng::deg2rad($coord1);
		list($lat2, $lng2) = Latlng::deg2rad($coord2);

		$a = sin((1 - $frac) * $dist) / sin($dist);
		$b = sin($frac * $dist) / sin($dist);

		$x = $a * cos($lat1) * cos($lng1) + $b * cos($lat2) * cos($lng2);
		$y = $a * cos($lat1) * sin($lng1) + $b * cos($lat2) * sin($lng2);
		$z = $a * sin($lat1) + $b * sin($lat2);

		$lat3 = atan2($z, sqrt($x * $x + $y * $y));
		$lng3 = atan2($y, $x);

		return Latlng::rad2deg(array($lat3, $lng3));
	}

	/**
	 * Convert a coordinate from radians to degrees.
	 *
	 * @param   array   coordinate
	 * @return  array
	 */
	public static function rad2deg(array $coord)
	{
		return array(rad2deg($coord[0]), rad2deg($coord[1]));
	}

} // End Latlng_Core
