var editor; // use a global for the submit and return data rendering in the examples

jQuery(document).ready(function($) {
  editor = new $.fn.dataTable.Editor( {
    ajax: "/wp-content/plugins/geocaching/includes/load-found-geocaches.php",
    table: "#found-geocaches",
    fields: [ {
      label: "Geocache ID:",
      name: "geocacheID"
    }, {
      label: "Geocache Title:",
      name: "geocacheTitle"
    }, {
      label: "Latitude:",
      name: "lat"
    }, {
      label: "Longitude:",
      name: "lng"
    }, {
      label: "Date Found:",
      name: "found",
      type: "datetime"
    }, {
      label: "Type:",
      name: "type",
      type:  "select",
      options: [
        { label: "Please Select", value: "" },
        { label: "Traditional", value: "Traditional" },
        { label: "A.P.E.", value: "APE" },
        { label: "Letterbox", value: "Letterbox" },
        { label: "Multi-Cache", value: "Multi-Cache" },
        { label: "Event", value: "Event" },
        { label: "Mega-Event", value: "MegaEvent" },
        { label: "Giga-Event", value: "GigaEvent" },
        { label: "Cache In Trash Out", value: "CacheInTrashOut" },
        { label: "GPS Adventures", value: "GPSAdventures" },
        { label: "Virtual", value: "Virtual" },
        { label: "Webcam", value: "Webcam" },
        { label: "EarthCache", value: "EarthCache" },
        { label: "Mystery", value: "Mystery" },
        { label: "Wherigo", value: "Wherigo" }
      ]
    } ] 
  });

  $('#found-geocaches').DataTable( {
    order: [[ 0, "desc" ]],
    dom: "Bfrtip",
    ajax: "/wp-content/plugins/geocaching/includes/load-found-geocaches.php",
    columns: [
      { data: "id" },
      { data: "geocacheID" },
      { data: "geocacheTitle" },
      { data: "lat" },
      { data: "lng" },
      { data: "found" },
      { data: "type" }
    ],
    select: true,
    buttons: [
      { extend: "create", editor: editor },
      { extend: "edit",   editor: editor },
      { extend: "remove", editor: editor }
    ]
  });
  
  editor = new $.fn.dataTable.Editor( {
    ajax: "/wp-content/plugins/geocaching/includes/load-my-geocaches.php",
    table: "#my-geocaches",
    fields: [ {
      label: "Geocache ID:",
      name: "geocacheID"
    }, {
      label: "Geocache Title:",
      name: "geocacheTitle"
    }, {
      label: "Latitude:",
      name: "lat"
    }, {
      label: "Longitude:",
      name: "lng"
    }, {
      label: "Date Placed:",
      name: "placed",
      type: "datetime"
    }, {
      label: "Type:",
      name: "type",
      type:  "select",
      options: [
        { label: "Please Select", value: "" },
        { label: "Traditional", value: "Traditional" },
        { label: "A.P.E.", value: "APE" },
        { label: "Letterbox", value: "Letterbox" },
        { label: "Multi-Cache", value: "Multi-Cache" },
        { label: "Event", value: "Event" },
        { label: "Mega-Event", value: "MegaEvent" },
        { label: "Giga-Event", value: "GigaEvent" },
        { label: "Cache In Trash Out", value: "CacheInTrashOut" },
        { label: "GPS Adventures", value: "GPSAdventures" },
        { label: "Virtual", value: "Virtual" },
        { label: "Webcam", value: "Webcam" },
        { label: "EarthCache", value: "EarthCache" },
        { label: "Mystery", value: "Mystery" },
        { label: "Wherigo", value: "Wherigo" }
      ]
    }] 
  });

  $('#my-geocaches').DataTable( {
    order: [[ 0, "desc" ]],
    dom: "Bfrtip",
    ajax: "/wp-content/plugins/geocaching/includes/load-my-geocaches.php",
    columns: [
      { data: "id" },
      { data: "geocacheID" },
      { data: "geocacheTitle" },
      { data: "lat" },
      { data: "lng" },
      { data: "placed" },
      { data: "type" }
    ],
    select: true,
    buttons: [
      { extend: "create", editor: editor },
      { extend: "edit",   editor: editor },
      { extend: "remove", editor: editor }
    ]
  });
});