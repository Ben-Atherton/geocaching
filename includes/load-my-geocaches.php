<?php
  
require_once('../../../../wp-config.php');
global $wpdb;
 
/*
 * Example PHP implementation used for the index.html example
 */
 
// DataTables PHP library
include( "datatables/DataTables.php" );
 
// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;
 
// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, $wpdb->prefix . 'geocaches_placed' )
  ->fields(
    Field::inst( 'id' ),
    Field::inst( 'geocacheID' ),
    Field::inst( 'geocacheTitle' ),
    Field::inst( 'lat' ),
    Field::inst( 'lng' ),
    Field::inst( 'placed' ),
    Field::inst( 'type' )
  )
  ->process( $_POST )
  ->json();