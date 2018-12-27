var TableAjax = function () {

    // Init Date Pickers
    /*
    var initPickers = function () {
        //Datetimepicker plugin
        $('.date-picker').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            clearButton: true,
            weekStart: 1,
            time: false
        });
    };
    */

    // Module Lists
    var handleRecordsModuleList = function() {
        gridTable( $("#module_list"), true, [ -1, 1, 0 ], true, $('#btn_list_module') );
    };
    
    // Menu Lists
    var handleRecordsMenuList = function() {
        gridTable( $("#menu_list"), true, [ -1, 1, 0 ], true, $('#btn_list_menu') );
    };

    var gridTable = function(el, action=false, target='', process=false, listbtn='' ) {
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
                "iDisplayLength": 50,                               // default record count per page
                "bServerSide": true,                                // server side processing
                "sAjaxSource": url,                                 // ajax source
                "aoColumnDefs": [
		          { 'bSortable': false, 'aTargets': tgt }
		       ]
            }
        });

        if( action == true ){
            gridExport( grid, '.table-export-excel', 'export_excel' );
            gridExport( grid, '.table-export-pdf', 'export_pdf' );
        }
        
        if( process == true ){
            gridProcess( grid, listbtn );
        }
    }

    var gridExport = function( dataTable, selectorBtn, sAction ) {
    	// handle group actionsubmit button click
        dataTable.getTableWrapper().on('click', selectorBtn, function(e) {
            e.preventDefault();

            if ( typeof sAction == 'undefined' )
            	sAction = 'export_excel';

            dataTable.addAjaxParam( "sAction", sAction );
            dataTable.getDataTable().fnDraw();
            dataTable.clearAjaxParams();
        });
    };
    
    var gridProcess = function( dataTable, selectorBtn ){
        // handle group actionsubmit button click
        dataTable.getTableWrapper().on('click', '.table-group-action-submit', function(e){
            e.preventDefault();
            
            var processVal = $('select.table-group-action-input option:selected', dataTable.getTableWrapper()).val();
            var processTxt = $('select.table-group-action-input option:selected', dataTable.getTableWrapper()).text().toUpperCase();
            
            if( processVal == "" ){
                swal('Silahkan pilih proses');
            }else{
                bootbox.confirm("Anda yakin akan melakukan proses "+processTxt+" data terpilih?", function(result) {
                    if( result == true ){
                        var action = $(".table-group-action-input", dataTable.getTableWrapper());
                        if (action.val() != "" && dataTable.getSelectedRowsCount() > 0) {
                            dataTable.addAjaxParam("sAction", "group_action");
                            dataTable.addAjaxParam("sGroupActionName", action.val());
                            var records = dataTable.getSelectedRows();
                            for (var i in records) {
                                dataTable.addAjaxParam(records[i]["name"], records[i]["value"]);    
                            }
                            dataTable.getDataTable().fnDraw();
                            dataTable.clearAjaxParams();
                        } else if (action.val() == "") {
                            App.alert({type: 'danger', icon: 'warning', message: 'Silahkan pilih proses', container: dataTable.getTableWrapper(), place: 'prepend'});
                        } else if (dataTable.getSelectedRowsCount() === 0) {
                            App.alert({type: 'danger', icon: 'warning', message: 'Tidak ada data terpilih untuk di proses', container: dataTable.getTableWrapper(), place: 'prepend'});
                        }
                        
                        selectorBtn.trigger('click');
                        $('#select_all').prop('checked', false);
                        $('select.table-group-action-input').attr('disabled','disabled');
                        $('button.table-group-action-submit').attr('disabled','disabled');
                    }
                });
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            //initPickers();
            //ADM
            handleRecordsModuleList();
            handleRecordsMenuList();
        }
    };

}();
