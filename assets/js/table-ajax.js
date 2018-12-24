var TableAjax = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true
        });
    };

    // Member and Register Lists
    var handleRecordsMemberList = function() {
        gridTable( $("#member_lists"), true );
    };
    var handleRecordsMemberRegisterList = function() {
        gridTable( $("#member_register_list"), true );
    };
    
    
    // Withdrawal List
    var handleRecordsWithdrawalList = function() {
        var url         = $("#list_withdrawal").data('url');
        var grid        = new Datatable();
        
        grid.init({
            src: $("#list_withdrawal"),
            onSuccess: function(grid) {
            	$( '#select-all' ).change( function() {
            		$( '.cbwithdraw:enabled' ).prop( 'checked', $(this).is(':checked') );
    				$( '.checker:not(.disabled)' ).find( 'span' ).removeClass( 'checked' );
    				if ( $(this).is(':checked') ) $( '.checker:not(.disabled)' ).find( 'span' ).addClass( 'checked' );
            	});
            },
            onError: function(grid) {},
            dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
                "aLengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"]                        // change per page values here
                ],
                "iDisplayLength": 10,                               // default record count per page
                "bServerSide": true,                                // server side processing
                "sAjaxSource": url,                                 // ajax source
                "aoColumnDefs": [
		          { 'bSortable': false, 'aTargets': [ -1, 0, 1, 10 ] }
		       ]
            }
        });
        gridExport( grid, '.table-export-excel' );
    };
    
    var gridTable = function(el, action=false, target='' ) {
        var url     = el.data('url');
        var grid    = new Datatable();
        var tgt     = ( target!="" ? target : [ -1, 0 ] );

        grid.init({
            src: el,
            onSuccess: function(grid) {},
            onError: function(grid) {},
            dataTable: {
                "aLengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"]                        // change per page values here
                ],
                "iDisplayLength": 10,                               // default record count per page
                "bServerSide": true,                                // server side processing
                "sAjaxSource": url,       // ajax source
                "aoColumnDefs": [
		          { 'bSortable': false, 'aTargets': tgt }
		       ]
            }
        });
        
        if( action == true ){
            gridExport( grid, '.table-export-excel' );
        }
    }
    
    var gridExport = function( dataTable, selectorBtn, sAction ) {
    	// handle group actionsubmit button click
        dataTable.getTableWrapper().on('click', selectorBtn, function(e) {
            e.preventDefault();
            
            if ( typeof sAction == 'undefined' )
            	sAction = 'export_excel';
        	
            dataTable.addAjaxParam( "sAction", sAction );
            var table = $( selectorBtn ).closest( '.table-container' ).find( 'table' );
            
            // get all typeable inputs
            $( 'textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', table ).each( function() {
                dataTable.addAjaxParam( $(this).attr("name"), $(this).val() );
            });

            // get all checkable inputs
            $( 'input.form-filter[type="checkbox"]:checked, input.form-filter[type="radio"]:checked', table ).each( function() {
                dataTable.addAjaxParam( $(this).attr("name"), $(this).val() );
            });
            
            dataTable.getDataTable().fnDraw();
            dataTable.clearAjaxParams();
        });
        
        $('a.tooltips').tooltip({
            html:true
		});
    };

    return {
        //main function to initiate the module
        init: function () {
            initPickers();
            handleRecordsMemberList();
        }
    };

}();