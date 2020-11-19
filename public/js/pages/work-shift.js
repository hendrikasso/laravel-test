var dataTabel = null;

$(function () {
  /**
   * Datatable configuration for work shifts table
   */
  dataTabel = $( "#work-shift-table" ).DataTable({
    "ajax": {
      "url": "/api/workshifts/",
      "dataSrc": '',
    },
    "rowId": 'id',
    "columns": [
      { "data": "name" },
      { "data": "begin" },
      { "data": "end" },
      { 
        "data": "duration",
        "render": function ( data ) {
          return data + 'h';
        }
      },
      { 
        "data": "daily_hours",
        "render": function ( data ) {
          return data + 'h';
        }
      },
      { 
        "data": "nightly_hours",
        "render": function (data) {
          return data + 'h';
        }
      },
      { 
        "data": "id",
        "render": function (data) {
          return '<div class="float-right">' +
            '<button type="button" data-id="' + data + '" class="edit-shift btn btn-primary m-1"><i class="fas fa-edit"></i></button>' +
            '<button type="button" data-id="' + data + '" class="delete-shift btn btn-danger m-1"><i class="fas fa-trash"></i></button>' +
            '</div>';
        }
      },
    ],
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });

  /**
   * save work shift
   */
  $( ".save-shift" ).click( function () {
    var id = parseInt( $( "input[name='id']" ).val() );

    // if id exist then we update, othrwise add new
    if (id) {
      var requestType = 'PUT';
      var url = '/api/workshifts/' + id;
    } 
    else {
      var requestType = 'POST';
      var url = '/api/workshifts';
    }
    var data = $( '#work-shift-form' ).serialize();

    $.ajax({
      type: requestType,
      url: url,
      data: data,
    }).done(function () {
      dataTabel.ajax.reload();
      $( "#work-shift-modal" ).modal('hide');
    }).fail(function (msg) {
      alert(JSON.stringify(msg, null, 2))
    });
  });

  /**
   * Open new work shift dialog
   */
  $( ".add-shift" ).click( function () {
    $( "input[name='id']" ).val(0);
    $( "input[name='name']" ).val("");
    $( "select[name='begin']" ).val("09:00");
    $( "select[name='end']" ).val("18:00");

    $( '#work-shift-modal').modal('show');
  });

  /**
   * Open edit work shift dialog
   */
  $( "#work-shift-table tbody" ).on( 'click', '.edit-shift', function () {
    var id = $( this ).data('id');
    var rowData = dataTabel.row('#' + id).data();

    $( "input[name='id']" ).val(id);
    $( "input[name='name']" ).val(rowData['name']);
    $( "select[name='begin']" ).val(rowData['begin']);
    $( "select[name='end']" ).val(rowData['end']);

    $( '#work-shift-modal' ).modal('show');
  });

  /**
   * Open deletes work shift
   */
  $( "#work-shift-table tbody" ).on( 'click', '.delete-shift', function () {
    var id = $( this ).data('id');
    
    $.ajax({
      type: 'DELETE',
      url: '/api/workshifts/' + id,
    }).done(function () {
      dataTabel.ajax.reload();
      $( '#work-shift-modal' ).modal('hide');
    }).fail(function (msg) {
      alert(JSON.stringify(msg, null, 2));
    });
  });
});