<?php
  
  require_once('../../../../wp-config.php');
  global $wpdb;
  
  $found_geocache = $wpdb->get_results( 'SELECT * from ' . $wpdb->prefix . 'geocaches_found');
  $placed_geocache = $wpdb->get_results( 'SELECT * from ' . $wpdb->prefix . 'geocaches_placed');

  function parseToXML($htmlStr) { 
    $xmlStr=str_replace('<','&lt;',$htmlStr); 
    $xmlStr=str_replace('>','&gt;',$xmlStr); 
    $xmlStr=str_replace('"','&quot;',$xmlStr); 
    $xmlStr=str_replace("'",'&#39;',$xmlStr);
    $xmlStr=str_replace("\\",'',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr); 
    return $xmlStr; 
  }

  header("Content-type: text/xml");
  
  // Start XML file, echo parent node
  echo '<markers>';
  
  foreach ($found_geocache as $found_geocache_marker) {
    
    // ADD TO XML DOCUMENT NODE
    echo '<marker ';
    echo 'geocacheID="' . $found_geocache_marker->geocacheID . '" ';
    echo 'geocacheTitle="' . parseToXML($found_geocache_marker->geocacheTitle) . '" ';
    echo 'lat="' . $found_geocache_marker->lat . '" ';
    echo 'lng="' . $found_geocache_marker->lng . '" ';
    echo 'type="' . $found_geocache_marker->type . '" ';
    echo 'found="' . date('d/m/Y', strtotime($found_geocache_marker->found)) . '" ';
    echo '/>';
    
  }
  
  foreach ($placed_geocache as $placed_geocache_marker) {
    
    // ADD TO XML DOCUMENT NODE
    echo '<marker ';
    echo 'geocacheID="' . $placed_geocache_marker->geocacheID . '" ';
    echo 'geocacheTitle="' . parseToXML($placed_geocache_marker->geocacheTitle) . '" ';
    echo 'lat="' . $placed_geocache_marker->lat . '" ';
    echo 'lng="' . $placed_geocache_marker->lng . '" ';
    echo 'type="' . 'Placed' . $placed_geocache_marker->type . '" ';
    echo 'placed="' . date('d/m/Y', strtotime($placed_geocache_marker->placed)) . '" ';
    echo '/>';
    
  }
  
  // End XML file
  echo '</markers>';

?>