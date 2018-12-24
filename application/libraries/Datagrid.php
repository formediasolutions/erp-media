<?php if(!defined('BASEPATH')) exit('No direct script access allowed');


class Datagrid {
  public  $dataGridID;
  public  $bootstrapStyle = TRUE;
  public  $useFixedHeader = FALSE;
  public  $useFixedBottom = FALSE;
  public  $caption = 'Datagrid';
  public  $showExportButton = TRUE;
  public  $exportButtonType = array("copy", "csv", "excel", "pdf", "print");
  public  $defaultOrder = array();
  public  $actionButtonPosition = 'bottom';
  public  $actionButtonAlign = 'left';
  public  $dataGridExtraParams = array();
  public  $pagingLenght = 25;
  public  $showPaging = true;
  public  $showInfo = true;
  public  $showSearchFilter = true;
  public  $enableOrdering = true;
  public  $dataGridLoadEvent = array();
  public  $useResponsivePlugin = false;
  public  $strInitCompleteCustom = '';
  public  $drawCallbackCustom = '';
  public  $dataTableScrollY = '450px';
  public  $dataGridInitialSearch = '';

  // 
  public  $dataGridSQLSearchPattern = '%[keyword]%';
  // start, end, both
  public  $dataGridArraySearchPattern = 'start';

  public  $rowOrderingParams = array('url' => '', 'request_type' => 'POST', 'dataIndex' => 'id', 'indexColumn' => 0);

  protected $ci;
  protected $bindType = 'QUERY';

  private $dataset = array();
  private $pageLimit = 10;
  private $column = array();
  private $error = array('error' => 0, 'message' => '');
  private $language;
  private $resultString;
  private $buttons = array();

  private $searchKeyword = '';
  private $searchRegex = false;
  private $arrIndexToSearch = array();

  private $orderDataIndex;
  private $orderDataType;
  private $orderDir = 'asc';

  private $pagingStart = 0;
  //private $pagingLenght = 10;
  private $hasNumberingColumn = FALSE;
  private $hasCheckBoxColumn = FALSE;
  private $checkBoxColumnIndex = '';
  private $hasSpecialButton = FALSE;
  private $strActionButton;

  private $hasRowSummary = FALSE;
  private $arrSummaryDataIndex = array();
  private $arrSummaryColIndex = array();
  private $summaryThousandSeparator = array();
  private $summaryDecimalSeparator = array();

  private $hasRowDetail = FALSE;
  private $strRowDetailFormater = '';

  private $hasButtonModal = FALSE;
  private $arrButtonModal = array();

  private $columnNumbering = array("title" => "No", 'dataIndex' => null, 'colProperties' => "searchable: false, orderable:false, width: '30px'");
  private $columnNumberingIndex = 0;
  private $columnCheckBox = array("title" => '', 'dataIndex' => null, 'colProperties' => "searchable: false, orderable:false, sClass: 'text-center', width: '20px'");

  private $autoCompleteSourceList = array();

  private $rowButtonEdit = array();
  private $rowButtonDelete = array();

  private $hasColumnFilter = false;
  private $arrColumnFilter = array();

  private $useFixedColumn = FALSE;
  private $fixedColumProp = array('left' => 1, 'right' => 1);

  private $buttonLink = array();
  private $hasModalWindow = FALSE;

  private $dataTablesJSPath                 = "js/datatables/jquery.dataTables.min.js";
  private $dataTablesBootstrapJSPath        = "js/datatables/dataTables.bootstrap.js";
  private $dataTablesBootstrapCSSPath       = "css/datatables/dataTables.bootstrap.css";
  private $dataTableskeyTableCSSPath        = "css/datatables/keyTable.dataTables.min.css";
  private $dataTablesFixedHeaderJSPath      = "js/datatables/dataTables.fixedHeader.min.js";
  private $dataTablesFixedHeaderCSSPath     = "css/datatables/dataTables.fixedHeader.css";
  private $dataTablesFixedColumnJSPath      = "js/datatables/dataTables.fixedColumns.js";
  private $dataTablesFixedColumnCSSPath     = "css/datatables/fixedColumns.dataTables.min.css";

  private $dataTablesResponsiveJSPath       = "js/datatables/responsive.bootstrap.min.js";
  private $dataTablesResponsiveCSSPath      = "css/datatables/responsive.bootstrap.min.css";

  // button export, print, pdf
  private $dataTablesButtonJSPath           = "js/datatables/buttons/dataTables.buttons.min.js";
  private $dataTablesButtonCSSPath          = "css/datatables/buttons.dataTables.min.css";

  private $dataTablesButtonFlashJSPath      = "js/datatables/buttons/buttons.flash.min.js";
  private $dataTablesButtonHtml5JSPath      = "js/datatables/buttons/buttons.html5.min.js";
  private $dataTablesButtonPrintJSPath      = "js/datatables/buttons/buttons.print.min.js";

  private $dataTablesJsZipJSPath            = "js/jszip.min.js";
  private $dataTablesPdfMakeJSPath          = "js/datatables/pdfmake/pdfmake.min.js";
  private $dataTablesPdfFontJSPath          = "js/datatables/pdfmake/vfs_fonts.js";
  private $dataTablesRowOrderingJSPath      = "js/datatables/jquery.dataTables.rowReordering.js";

  //public function __construct($idDataGrid = 'DataGrid_'){
  public function __construct($arrParams = array()){
    $this->ci =& get_instance();
    $this->ci->load->helper('form');
    $idDataGrid = (isset($arrParams['id'])) ? $arrParams['id'] : 'DataGrid_';
    
    $this->dataGridID = $idDataGrid;
    $this->dataGridID .= $this->ci->session->session_id;
  }

  public function addColumn($arrColumnProperties) {
    if (isset($arrColumnProperties['dataIndex']) && ($arrColumnProperties['dataIndex'] != '') )
      $this->arrIndexToSearch[$arrColumnProperties['dataIndex']] = 1;

    if (isset($arrColumnProperties['type']) && $arrColumnProperties['type']=='date') {
      $dateFormat = (isset($arrColumnProperties['dateFormat'])) ? $arrColumnProperties['dateFormat'] : 'DD MMM YYYY';
      $arrColumnProperties['colRenderer'] = "var mDate = moment(data); return (mDate && mDate.isValid()) ? mDate.format('$dateFormat') : '';";
    }
    $this->column[] = $arrColumnProperties;
  }

  public function addColumnNumbering($colProperties=array()) {
    if ( $this->hasNumberingColumn == false ) {
      $arrAttr = array_merge($colProperties, $this->columnNumbering);
      $this->addColumn($arrAttr);
    }

    $this->hasNumberingColumn = true;
    $this->columnNumberingIndex = count($this->column)-1;
  }

  public function addColumnCheckBox($arrColumnProperties = array()  ) {

    if ( $this->hasCheckBoxColumn == false ) {
      $this->columnCheckBox['colRenderer'] = ' return \'<div class=\"checkbox-inline\"><label><input type="checkbox" id="checkBox_'.$this->dataGridID.'_{{counter}}" class="checkbox style-0 checkBox_'.$this->dataGridID.'" value="{{value}}"/><span></span></label></div>\'';
      $this->columnCheckBox['title'] = ' <label class="checkbox-inline"><input type="checkbox" id="checkBox_checkAll_'.$this->dataGridID.'" class="checkbox style-0 checkBox_checkAll_'.$this->dataGridID.'"/><span></span></label>';

      $arrProp = array_merge($this->columnCheckBox, $arrColumnProperties);

      $this->addColumn($arrProp);
      $this->checkBoxColumnIndex = $arrColumnProperties['dataIndex'];
    }

    $this->hasCheckBoxColumn = true;
  }

  public function addColumnFilter($dataIndex, $inputType, $defaultData = '') {
    $this->hasColumnFilter = true;
    $this->arrColumnFilter[$dataIndex] = array('type' => $inputType, 'indexColumn' => 0, 'dataOption' => $defaultData);
  }

  public function addActionColumn($title, $dataIndex, $customHtml = "", $colProperties=array()) {
    $arrButton = array();
    $strRenderer   = 'var strResult = \'\';';

    if (!empty($this->rowButtonEdit)) {
      $strRenderer .= 'if '.$this->rowButtonEdit['renderCondition'].' { ';
      $strRenderer .= "strResult +='".$this->rowButtonEdit['button']."'";
      $strRenderer .= '}';
      //$arrButton[] = $this->rowButtonEdit['button'];
    } 

    if (!empty($this->rowButtonDelete)) { 
      $strRenderer .= 'if '.$this->rowButtonDelete['renderCondition'].' { ';
      $strRenderer .= "strResult +='".$this->rowButtonDelete['button']."'";
      $strRenderer .= '}';
      // $arrButton[] = $this->rowButtonDelete['button'];
    }

    if (!empty($this->arrButtonModal)) {
      foreach ($this->arrButtonModal AS $btn) {
        $strRenderer .= 'if '.$btn['renderCondition'].' { ';
        $strRenderer .= "strResult +='".$btn['button']."'";
        $strRenderer .= '}';
        // $arrButton[] = $btn['button'];
      }
    }

    //$arrButton[] = $customHtml;
    $strRenderer .= $customHtml;
    $strRenderer .= 'return strResult;';

    // $strRenderer = implode('', $arrButton);

    $strNoWrap = (isset($colProperties['nowrap'])) ? 'nowrap' : '';

    $strWidth = (isset($colProperties['width'])) ? $colProperties['width'] : '120px';

    $defaultActionAttribute = array(
      'title' => $title, 
      'dataIndex' => $dataIndex, 
      // 'colRenderer' => "return '".$strRenderer."'",  
      'colRenderer' => $strRenderer,  
      'colProperties' => "sClass: 'text-center ".$strNoWrap."', width: '".$strWidth."', searchable: false, orderable:false"
    );

    $arrAttr = array_merge($defaultActionAttribute, $colProperties);
    $this->addColumn($arrAttr);
  }


  public function addColumLink($title, $dataIndex='', $customHtml = "", $colProperties=array()) {
    $arrButton = array();
    if (!empty($this->buttonLink)) $arrButton[] = $this->buttonLink['button'];

    $arrButton[] = $customHtml;
    $strRenderer = implode('', $arrButton);

    $strNoWrap = (isset($colProperties['nowrap'])) ? 'nowrap' : '';

    $defaultActionAttribute = array('title' => $title, 'dataIndex' => $dataIndex, 'colRenderer' => "return '".$strRenderer."'",  'colProperties' => "sClass: 'text-center ".$strNoWrap."', width: '120px', searchable: false, orderable:false");
    $arrAttr = array_merge($defaultActionAttribute, $colProperties);
    $this->addColumn($arrAttr);
  }

  public function addActionColumnInputText($colProperties=array(), $arrInputAttr=array(), $htmlAfter='') {
    if (isset($colProperties['dataIndex']) && ($colProperties['dataIndex'] != '') )
      $this->arrIndexToSearch[$colProperties['dataIndex']] = 1;

    if (isset($colProperties['colRenderer'])) unset($colProperties['colRenderer']);

    $arrDefaultAttr = array('class' => 'form-control input-sm');
    //added by Dedy
    if (isset($arrInputAttr['class'])) {
      $arrDefaultAttr['class'] .= ' '.$arrInputAttr['class'];
    }

    $arrDefaultAttr['row-data-index'] = $colProperties['dataIndex'];
    $arrAttr = array_merge($arrInputAttr, $arrDefaultAttr);
    $colProperties['colRenderer'] = "if (!data) data=''; return '".form_input($arrAttr, "{{value}}", "")." ".$htmlAfter."'";

    $this->addColumn($colProperties);
  }

  public function addActionColumnInputSelect($colProperties=array(), $arrInputAttr=array(), $arrDataOption=array()) {
    if (isset($colProperties['dataIndex']) && ($colProperties['dataIndex'] != '') )
      $this->arrIndexToSearch[$colProperties['dataIndex']] = 1;

    if (isset($colProperties['colRenderer'])) unset($colProperties['colRenderer']);

    $arrDefaultAttr = array('class' => 'form-control input-sm');
    //added by Dedy
    if (isset($arrInputAttr['class'])) {
      $arrDefaultAttr['class'] .= ' '.$arrInputAttr['class'];
    }
    
    $arrDefaultAttr['row-data-index'] = $colProperties['dataIndex'];
    $arrAttr = array_merge($arrInputAttr, $arrDefaultAttr);
    //LOGIC untuk data dari fungsi generateList dengan tipe array value-text
    if ($arrDataOption != null && count($arrDataOption) > 0)
    {
      $isFromTextValue = false;
      foreach($arrDataOption as $testKey => $testOption) {
        if (is_array($testOption) && isset($testOption["text"]) && isset($testOption["value"]))
        {
          $isFromTextValue = true;
          break;
        }
      }
      if ($isFromTextValue) {
        $arrDataOptionTemp = array();
        foreach($arrDataOption as $testOption) {
          $arrDataOptionTemp[$testOption["value"]] = $testOption["text"];
        }
        $arrDataOption = $arrDataOptionTemp;
      }
    }
    //End of LOGIC untuk data dari fungsi generateList dengan tipe array value-text

    $colProperties['colRenderer'] = "if (!data) data=''; return '".form_dropdown($arrAttr, $arrDataOption, "", '')."'";
    $this->addColumn($colProperties);
  }

  public function addActionColumnInputCheckbox($colProperties=array(), $arrInputAttr=array()) {
    if (isset($colProperties['dataIndex']) && ($colProperties['dataIndex'] != '') )
      $this->arrIndexToSearch[$colProperties['dataIndex']] = 1;

    if (isset($colProperties['colRenderer'])) unset($colProperties['colRenderer']);

    $colProperties['colProperties'] = "sClass : 'text-center'";
    $arrDefaultAttr = array('class' => 'checkbox');
    $arrDefaultAttr['row-data-index'] = $colProperties['dataIndex'];
    $arrAttr = array_merge($arrInputAttr, $arrDefaultAttr);
    //form_checkbox($arrInputAttr, $key, $checked, $jsFunction).'<span>'.$val.'</span>';

    $colProperties['colRenderer'] = "
      if ( (data == 1) || (data == 't') || (data == '1') || (data == 'true'))
        return '<div class=\"checkbox-inline\"><label>".form_checkbox($arrAttr, "", '1')."<span></span></label></div>';
      else
        return '<div class=\"checkbox-inline\"><label>".form_checkbox($arrAttr, "")."<span></span></label></div>';
        ";

    $this->addColumn($colProperties);

  }

  public function addActionColumnInputAutoComplete($colProperties=array(), $arrInputAttr=array(), $arrDataOption=array(), $labelDataIndex='', $inputDataIndex='') {
    if (isset($colProperties['dataIndex']) && ($colProperties['dataIndex'] != '') )
      $this->arrIndexToSearch[$colProperties['dataIndex']] = 1;

    $inputDataValue = ($inputDataIndex == '') ? "{{value}}" : "{{".$inputDataIndex."}}";
    $labelDataValue = ($labelDataIndex == '') ? "{{value}}" : "{{".$labelDataIndex."}}";

    if (isset($colProperties['colRenderer'])) unset($colProperties['colRenderer']);

    $minLength = 1;
    $devider = 10;

    $remain = count($arrDataOption);
    while($remain >= $devider) {
      $remain = $remain / $devider;
      if ($remain >= $devider )
        $minLength++;
    }

    if (!isset($this->autoCompleteSourceList[$colProperties['dataIndex']])) {
      $strDataList = 'var '.$colProperties['dataIndex'].'_options_'.$this->dataGridID.' = ';
      $arrTempOptions = array();

      //LOGIC untuk data dari fungsi generateList dengan tipe array value-text
      if ($arrDataOption != null && count($arrDataOption) > 0)
      {
        $isFromTextValue = false;
        foreach($arrDataOption as $testOption) {
          if (is_array($testOption) && isset($testOption["text"]) && isset($testOption["value"]))
          {
            $isFromTextValue = true;
            break;
          }
        }
        if ($isFromTextValue) {
          $arrDataOptionTemp = array();
          foreach($arrDataOption as $testOption) {
            $arrDataOptionTemp[$testOption["value"]] = $testOption["text"];
          }
          $arrDataOption = $arrDataOptionTemp;
        }
      }

      foreach ($arrDataOption AS $key =>$value) {
        if (is_array($value)) {
          $arrDefaultOptions = array('value' => $key);
          $arrTempOptions[] = array_merge($value, $arrDefaultOptions);
        }
        else {
          $indexDataGroup = '';
          $arrTempOptions[] = array('value' => $key, 'label' => $value);
        }
      }

      // sorting for grouping auto complete
      $arrTmpForSorting = array();
      if (!empty($indexDataGroup)) {
        foreach ($arrTempOptions AS $arrProp) {
          $arrTmpForSorting[$arrProp[$indexDataGroup]][] = $arrProp;
        }
        ksort($arrTmpForSorting);
        $arrTempOptions = array();
        foreach($arrTmpForSorting AS $group => $rowGroup) {
          foreach ($rowGroup AS $row) {
            $arrTempOptions[] = $row;
          }
        }
      }
      $strDataList .= json_encode($arrTempOptions).';';

      $this->autoCompleteSourceList[$colProperties['dataIndex']] = array('dataList' => $strDataList , 'minLength' => $minLength);
    }

    $colProperties['defaultContent'] = "''";
    $arrDefaultAttr = array('class' => 'form-control input-sm auto_complete_'.$colProperties['dataIndex'].'_'.$this->dataGridID);    
    //added by Dedy
    if (isset($arrInputAttr['class'])) {
      $arrDefaultAttr['class'] .= ' '.$arrInputAttr['class'];
    }
    
    $arrDefaultAttr['list'] = $colProperties['dataIndex'].'_options_'.$this->dataGridID;
    $arrDefaultAttr['row-data-index'] = $colProperties['dataIndex'];
    $arrAttr = array_merge($arrInputAttr, $arrDefaultAttr);
    $colProperties['colRenderer'] = "if (!data) data=''; if (!row.".$labelDataIndex.") row.".$labelDataIndex." = ''; return '".form_input($arrAttr, $inputDataValue, '')." <p class=\"help-block\" id=\"label_auto_complete\" style=\"min-height:18px;\">".$labelDataValue."</p>'";

    $this->column[] = $colProperties;
  }

  function addButton($id, $name, $type, $value, $clientAction="", $serverAction="", $class = "", $jsAfterSave='')  {
    $this->buttons[] = array("special" => false, "id"=>$id, "name"=>$name, "type"=>$type, "value"=>$value, 'jsAfterSave' => $jsAfterSave, "class"=> "btn btn-sm btn-default ".$class, "clientAction"=>$clientAction, "serverAction"=>$serverAction);
  }

  public function addCustomLoadEvent($strElementSelector, $strEventTrigger) {
    $this->dataGridLoadEvent = array('element' => $strElementSelector, 'eventTrigger' => $strEventTrigger);
  }

  // $clientAction :
  // "POST" => post data to server action,
  // "REDIRECT" => redirect to new page with variable,
  // "OPEN" => open new window
  public function addSpecialButton($id, $name, $type, $value, $clientAction="", $serverAction="", $class="")  {
    $this->buttons[] = array("special" => true, "id"=>$id, "name"=>$name, "type"=>$type, "value"=>$value, "class"=> "btn btn-sm btn-default buttonSpecial_".$this->dataGridID." ".$class, "clientAction"=>$clientAction, "serverAction"=>$serverAction);
    $this->hasSpecialButton = true;
  }

  public function addRenderer($arrRenderProp) {
    if (isset($arrRenderProp['title'])) {
      foreach($this->column AS &$column) {
        if ($column['title'] == $arrRenderProp['title'])
          $column['colRenderer'] = $arrRenderProp['colRenderer'];
      }
    }
  }


  // destination = 'formID' OR 'url'
  public function addLink($destination, $key, $strValue, $strValueBefore='', $strValueAfter='') {
    $strValueText = ($this->ci->lang->line($strValue) == '') ? $strValue : $this->ci->lang->line($strValue);
    $strValueButton = $strValueBefore.$strValueText.$strValueAfter;

    $strButton = '<button type="button" class="btn btn-success btn-xs btn-link-'.$this->dataGridID.'">'.$strValueButton.'</button>';

    $this->buttonLink = array('dataIndex' => $key, 'button' => $strButton, 'destination' => $destination);
  }

  // destination = 'formID' OR 'url'
  public function addRowButtonEdit($key, $action, $destination, $windowType='popup', $windowProperties='', $renderCondition='(true)') {
    // if (!$this->ci->bolEdit)
    //   return false;
    $strValueButton = ($this->ci->lang->line('Edit') == '') ? 'Edit' : $this->ci->lang->line('Edit');

    $strButton = '<button type="button" class="btn btn-primary btn-xs btn-edit-'.$this->dataGridID.'"><span class="glyphicon glyphicon-edit"></span> '.$strValueButton.'</button>';
    $this->hasModalWindow = true;
    // $strModal = '';
    // if ($action == 'url') {
    //   $strModal = '
    //     <div class="modal fade" id="remoteModal_'.$this->dataGridID.'" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    //       <div class="modal-dialog modal-lg">
    //           <div class="modal-content">
    //             <div class="modal-header bg-default clearfix no-padding">
    //               <div class="jarviswidget-ctrls" role="menu">
    //                 <a href="javascript:void(0);" class="button-icon" rel="tooltip" title="reload" id="reload_remoteModal_'.$this->dataGridID.'"><i class="fa fa-refresh"></i></a>
    //                 <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
    //               </div>
    //             </div>
    //             <div class="modal-body" style="padding: 5px !important;">
    //             </div>
    //           </div>
    //       </div>
    //     </div>';

    //   $strModal = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strModal);
    //   $strModal = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strModal);
    // }

    $this->rowButtonEdit = array('dataIndex' => $key, 'button' => $strButton, 'action' => $action, 'destination' => $destination, 'windowType' => $windowType, 'windowProp' => $windowProperties, 'renderCondition' => $renderCondition);
  }

  //added by Dedy $fieldMapping
  //untuk mapping field alias ke real field database
  //contoh apabila isinya: array('id_grn_detail' => 'id'), maka field alias waktu select adalah id_grn_detail, tetapi field di table adalah id
  public function addRowButtonDelete($key, $tableName, $fieldMapping = array(), $renderCondition='(true)') {
    // if (!$this->ci->bolDelete)
    //   return false;    
    $strValueButton = ($this->ci->lang->line('Delete') == '') ? 'Delete' : $this->ci->lang->line('Delete');
    $strButton = '<button type="button" class="btn btn-danger btn-xs btn-delete-'.$this->dataGridID.'"><span class="glyphicon glyphicon-remove"></span> '.$strValueButton.'</button>';

    $strKey = $key;
    if (is_array($key)) $strKey = implode(',', $key);
    $this->rowButtonDelete = array('dataIndex' => $strKey, 'tableName' => $tableName,'ser' ,'button' => $strButton, 'fieldMapping' => $fieldMapping, 'renderCondition' => $renderCondition);
  }

  public function addRowButtonModal($key, $url, $strTextButton = '<span class="glyphicon glyphicon-zoom-in"></span>  show Modal', $modalSize = 'large', $renderCondition='(true)') {
    //arrButtonModal

    $strClassModalSize = ($modalSize == 'large') ? 'modal-lg' : '';
    $currentCount = (empty($this->arrButtonModal)) ? 1 : (count($this->arrButtonModal) + 1) ;

    $strValueButton = ($this->ci->lang->line($strTextButton) == '') ? $strTextButton : $this->ci->lang->line($strTextButton);
    $strButton = '<button type="button" class="btn btn-success btn-xs btn-showModal-'.$this->dataGridID.'-'.$currentCount.'">'.$strValueButton.'</button>';
    $this->hasModalWindow = true;
    // $strModal = '
    //   <div class="modal fade" id="blockModal_'.$this->dataGridID.'_'.$currentCount.'" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
    //       <div class="modal-dialog '.$strClassModalSize.'">
    //           <div class="modal-content">
    //             <div class="modal-header bg-default clearfix no-padding">
    //               <div class="jarviswidget-ctrls" role="menu">
    //                 <a href="javascript:void(0);" class="button-icon jarviswidget-refresh-btn" data-loading-text="&nbsp;&nbsp;Loading...&nbsp;" rel="tooltip" title="" data-placement="bottom" data-original-title="Refresh"><i class="fa fa-refresh"></i></a>
    //                 <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
    //               </div>
    //             </div>
    //             <div class="modal-body" style="padding: 5px !important;">
    //             </div>
    //           </div>
    //       </div>
    //   </div>';
    // $strModal = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strModal);
    // $strModal = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strModal);

    $this->hasButtonModal = true;
    $this->arrButtonModal[] = array('dataIndex' => $key, 'button' => $strButton, 'url' => $url, 'renderCondition' => $renderCondition);
  }

  public function addRowDetail($customHtml, $arrColumnProperties=array()) {
    $this->hasRowDetail = true;
    $this->strRowDetailFormater = $this->parseRenderString($customHtml, '', 'd');
    $defaultColumnDetail = array("title" => '', 'dataIndex' => null, 'colProperties' => "searchable: false, orderable:false, sClass: 'text-center rowDetail-control', width: '50px'",
    "colRenderer" => 'return " <button type=\"button\" class=\"btn btn-sm btn-primary\"><span class=\"glyphicon glyphicon-zoom-in\"></span>View</button>";');
    $arrColumnAttr = array_merge($defaultColumnDetail, $arrColumnProperties);
    $this->column[] = $arrColumnAttr;
  }

  public function addRowSummary($arrDataIndex = array(), $thousandSeparator = '.', $decimalSeparator = ',') {
    if (!empty($arrDataIndex)) {
      $this->hasRowSummary = true;
      foreach($arrDataIndex AS $idx) {
        $this->arrSummaryDataIndex[$idx] = $idx;
      }
      $this->summaryThousandSeparator = $thousandSeparator;
      $this->summaryDecimalSeparator = $decimalSeparator;
    }
  }

  public function setFixedColumn($intColumnLeft = 1, $intColumnRight = 0)  {
    $this->useFixedColumn = true;

    $this->fixedColumProp['left'] = $intColumnLeft;
    $this->fixedColumProp['right'] = $intColumnRight;
  }

  public function bindQuery($strSQL) {
    if ($this->ci->input->get('ajax'.$this->dataGridID)) {
      $this->_generateDataFromQuery($strSQL);

      foreach($this->arrColumnFilter AS $dataIndex => $filter) {

        if (($filter['type'] == 'select') && (empty($filter['dataOption']))) {
          $arrSelectOption = array();

          $strSQL ="SELECT distinct(".$dataIndex.")as datafilteridx FROM ($strSQL) AS tmpQueryDatagrid";

          $arrQuery = $this->ci->db->query($strSQL)->result_array();
          foreach($arrQuery AS $row){
            $arrSelectOption[$row['datafilteridx']] = $row['datafilteridx'];
          }

          $this->dataset["dataFilter_" . $dataIndex . "_" . $this->dataGridID] = $arrSelectOption;
        }
      }
    }
  }

  public function bindArray($arrData){
    if ($this->ci->input->get('ajax'.$this->dataGridID)) {
      $draw = ($this->ci->input->post('draw')) ? $this->ci->input->post('draw') : 1;
      $this->dataset = array(
      'draw' => $draw,
      'recordsTotal' => count($arrData),
      'recordsFiltered' => count($arrData),
      'data' => $arrData
      );

      foreach($this->arrColumnFilter AS $dataIndex => $filter) {
        if (($filter['type'] == 'select') && (empty($filter['dataOption']))) {
          $arrSelectOption = array();

          foreach($arrData AS $row){
            foreach($row AS $key => $val) {
              if ($key == $dataIndex) {
                $arrSelectOption[$val] = $val;
              }
            }
          }
          $this->dataset["dataFilter_" . $dataIndex . "_" . $this->dataGridID] = $arrSelectOption;
        }

        //print_r($this->dataset["dataFilter_" . $dataIndex . "_" . $this->dataGridID]);
      }

      $this->bindType = 'ARRAY';
      $this->_getFiltering();
      $this->_getOrdering();
      $this->_getPaging();
    }
  }

  public function catchRequestData($arrPostData) {
    if (is_array($arrPostData)) {

      foreach($arrPostData AS $post) {
        if ($this->ci->input->get_post($post))
          $this->dataGridExtraParams[] = "'".$post."' :'".$this->ci->input->get_post($post)."'";
      }

    }
  }

  public function generate() {
    if ($this->ci->input->get_post('ajax'.$this->dataGridID)) {

      if ($this->ci->input->get('action') && ($this->ci->input->get('action') == 'deleteRowData') ) {
        $arrResult = array('success' =>0, 'message' => '');

        if ($this->ci->input->post('tableName') && $this->ci->input->post('dataIndex'))
          $arrResult = $this->deleteRowData();
        else {
          $arrResult['message'] = 'Uncomplete Parameter';
        }
        if (ENVIRONMENT == 'development') ini_set('display_errors', 1);
        echo json_encode($arrResult);
      }
      else {
        // save datagrid data to session
        // when to destoy datagrid data session ????
        echo json_encode($this->dataset);
      }
      exit();
    }
    else {
      // if error happen
      if ($this->error['error'] == 1) {

      }
      else {
        $this->_generateButtons();
        $this->resultString  = $this->_generateTableHTML();
        $this->resultString .= $this->_generateJS();
      }
    }
    return $this->resultString;
  }

  private function deleteRowData() {
    if (ENVIRONMENT == 'development') ini_set('display_errors', 0);
    $tableName = $this->ci->input->post('tableName');
    $arrData = $this->ci->input->post('dataIndex');

    foreach($arrData AS $key => $value) {
      
      //added by Dedy $fieldMapping
      $fieldName = $key;
      if (isset($this->rowButtonDelete['fieldMapping'])) {
        //sebelum
        if (isset($this->rowButtonDelete['fieldMapping'][$key])) {
          $fieldName = $this->rowButtonDelete['fieldMapping'][$key];
        }
      }
      
      $this->ci->db->where($fieldName, $value);
    }
    

    if (!$this->ci->db->delete($tableName)) {
      $arrResult = array('success' => 0, 'message' => '');
      if (ENVIRONMENT == 'development') {
        $arrError = $this->ci->db->error();
        $arrResult['message'] = $arrError['message'];
      }
      else $arrResult['message'] = 'Error In Deleting Process';
      return $arrResult;
    }
    else {
      return array('success' => 1, 'message' => 'data has been deleted');

    }
  }

  private function _getFiltering() {
    $arrResult = array();

    if ($this->ci->input->post('search')) {
      $arrSearch = $this->ci->input->post('search');

      $this->searchKeyword = strtolower($arrSearch['value']);
      $this->searchRegex   = $arrSearch['regex'];

      if ( ($this->bindType == 'ARRAY') && ($this->searchKeyword != '') ) {

        $arrIndexSearch = array();
        // search column data index to search
        foreach($this->dataset['data'] AS $row) {
          foreach($row AS $idx => $value) {
            if ( !empty($value) && isset($this->arrIndexToSearch[$idx])) {
              if ($this->dataGridArraySearchPattern == 'start') {
                if ($this->stringStartsWith(strtolower($value), strtolower($this->searchKeyword)) !== false) {
                  $arrResult[] = $row;
                  break;                  
                }
              }
              else if ($this->dataGridArraySearchPattern == 'end') {
                if ($this->stringEndsWith(strtolower($value), strtolower($this->searchKeyword)) !== false) {
                  $arrResult[] = $row;
                  break;                  
                }                
              }
              else {
                if ( stripos(strtolower($value), strtolower($this->searchKeyword)) !== false) {
                  $arrResult[] = $row;
                  break;
                }
              }
            }
          }
        }
        $this->dataset['recordsFiltered'] = count($arrResult);
        $this->dataset['data'] = $arrResult;
        return true;
      }
    }

    $arrPostColumn =$this->ci->input->post('columns');

    $arrSearchColumn = array();
    if (!empty($arrPostColumn)) {
      if (empty($arrResult)) $arrResult = $this->dataset['data'];

      foreach($arrPostColumn AS $column) {
        if ( ($column['searchable']) && ($column['search']['value'] != '') && ($column['data'] != '') && ($column['search']['value'] != '0')) {
          $arrSearchColumn[$column['data']] = array('value' => $column['search']['value'], 'isFound' => 0);
        }
      }

      if (!empty($arrSearchColumn)) {
        foreach($arrResult AS $idx => $row){

          foreach($arrSearchColumn AS $dataIndex => &$search){
            if (isset($row[$dataIndex])) {

              if ($this->dataGridArraySearchPattern == 'start') {
                if ($this->stringStartsWith(strtolower($row[$dataIndex]), strtolower($search['value'])) !== false)
                  $search['isFound'] = 1;                     
              }
              else if ($this->dataGridArraySearchPattern == 'end') {
                if ($this->stringEndsWith(strtolower($row[$dataIndex]), strtolower($search['value'])) !== false)
                  $search['isFound'] = 1;             
              }
              else {
                if ( stripos(strtolower($row[$dataIndex]), $search['value']) !== false)
                  $search['isFound'] = 1;
              }


              // if ( stripos(strtolower($row[$dataIndex]), $search['value']) !== false) {
              //   $search['isFound'] = 1;
              // }
            }
          }

          $isFound = 0;

          foreach($arrSearchColumn AS $dataIndex => $sc){
            if (!$sc['isFound']) {
              $isFound = 0;
              break;
            }
            else $isFound = 1;
          }

          if (!$isFound)
            unset($arrResult[$idx]);

          foreach($arrSearchColumn AS $dataIndex => $t){
            $arrSearchColumn[$dataIndex]['isFound'] = 0;
          }

        }
      }
    }

    // $arrFinalResult = (empty($arrSearchColumn)) ? $arrResult : $arrSearchColumn;
    $this->dataset['recordsFiltered'] = count($arrResult);
    $this->dataset['data'] = array_values($arrResult);
  }

  private function _getOrdering(){
    if ($this->ci->input->get('ajax'.$this->dataGridID)) {
      if ($this->ci->input->post('order')) {
        $arrOrder = $this->ci->input->post('order');
        $arrColumns = $this->ci->input->post('columns');

        foreach($arrOrder AS $idx => $order){
          if ( (isset($order['column'])) ) {
            if ( isset($arrColumns[$order['column']]) ) {
              $this->orderDataIndex = $arrColumns[$order['column']]['data'];
            }
          }

          if (isset($order['dir'])) $this->orderDir = $order['dir'];

          // sorting array dataset
          if ($this->bindType == 'ARRAY')
            usort($this->dataset['data'], array($this,'_sortDataset'));
        }
      }
    }
    return true;
  }

  private function _sortDataset($a, $b) {
    if (strtolower($a[$this->orderDataIndex]) == strtolower($b[$this->orderDataIndex])) {
      return 0;
    }
    if ($this->orderDir == 'asc')
      return (strtolower($a[$this->orderDataIndex]) < strtolower($b[$this->orderDataIndex])) ? -1 : 1;
    if ($this->orderDir == 'desc')
      return (strtolower($a[$this->orderDataIndex]) > strtolower($b[$this->orderDataIndex])) ? -1 : 1;
  }

  private function _getPaging() {
    if ($this->ci->input->get('ajax'.$this->dataGridID)) {

      // filter limit & offset
      $this->pagingLenght = $this->ci->input->post('length');
      $this->pagingStart  = $this->ci->input->post('start');

      if ( ($this->ci->input->post('length')) && (intval($this->ci->input->post('length')) > 0) ) {

        if ($this->bindType == 'ARRAY') {
          $arrResult = array();
          $counter = 0;
          foreach($this->dataset['data'] AS $index =>$row) {
            if ($index >= $this->pagingStart) {
              if ($counter < $this->pagingLenght) {
                $arrResult[] = $row;
                $counter++;
              }
              else break;
            }
          }
          $this->dataset['data'] = $arrResult;
        }
      }
    }
    return true;
  }

  private function _generateDataFromQuery($strSQL) {
    $draw = ($this->ci->input->post('draw')) ? $this->ci->input->post('draw') : 1;
      $this->dataset = array(
      'draw' => $draw,
      'recordsTotal' => 0,
      'recordsFiltered' => 0,
      'data' => array()
      );

    if ($strSQL != '') {
        // get total data
        $queryCount = " SELECT COUNT(*) AS total FROM ($strSQL) AS tmpQuery ";
        if ( $resCount = $this->ci->db->query($queryCount) ) {
          $arrCount = $resCount->result_array();
          $this->dataset = array(
          'draw' => $draw,
          'recordsTotal' => $arrCount[0]['total'],
          'recordsFiltered' => $arrCount[0]['total'],
          'data' => array()
          );
        }
        else $this->error = array('error' => 1, 'message' => 'failed');

        // filtering data
        //$this->_getFiltering();
        // ordering data
        $this->_getOrdering();
        // paging data
        $this->_getPaging();

        $strSearch = '';
        if ( !empty($this->searchKeyword) ) {
          $arrSearchString = array();
          $counter = 1;
          foreach($this->arrIndexToSearch AS $idx => $val) {
            // $arrSearchString[] = " CAST($idx AS character varying) ilike '%".$this->searchKeyword."%' ";
            $strKeyWord = str_replace("[keyword]", $this->searchKeyword, $this->dataGridSQLSearchPattern);
            $arrSearchString[] = " CAST($idx AS character varying) ilike '".$strKeyWord."' ";
            $counter++;
          }
          if (!empty($arrSearchString)) $strSearch .= ' AND '.implode(' OR ', $arrSearchString);
        }

        $arrPostColumn =$this->ci->input->post('columns');
        $arrSearchColumn = array();
        if ($arrPostColumn && count($arrPostColumn) > 0) {
          foreach($arrPostColumn as $column) {
            if ( ($column['searchable']) && ($column['search']['value'] != '') && ($column['data'] != '') && ($column['search']['value'] != '0')) {
              // $arrSearchColumn[] = " CAST(".$column['data']." AS character varying) ilike '%".$column['search']['value']."%' ";
              $strKeyWord = str_replace("[keyword]", $column['search']['value'], $this->dataGridSQLSearchPattern);             
              $arrSearchColumn[] = " CAST(".$column['data']." AS character varying) ilike '".$strKeyWord."' ";
            }
          }
        }
        if (!empty($arrSearchColumn)) $strSearch .= ' AND '.implode(' AND ', $arrSearchColumn);

        $strOrder = '';
        if ($this->orderDataIndex != '') {
          $strOrder .= " ORDER BY $this->orderDataIndex";
          if ($this->orderDir != '') $strOrder .= " ".$this->orderDir;
        }

        $strLimit = '';
        if ( $this->pagingLenght > 0 ) {
          $strLimit .= " LIMIT ".$this->pagingLenght;
          if ( $this->pagingStart >= 0 ) $strLimit .= " OFFSET ".$this->pagingStart;
        }

        $queryString = " SELECT * FROM ($strSQL) AS tmpQuery WHERE 1=1 ".$strSearch.$strOrder.$strLimit;

        $arrData = array();
        if ($res = $this->ci->db->query($queryString)) {
          $arrData = $res->result_array();

          $this->dataset['data'] = $arrData;
        }

        $queryString = " SELECT * FROM ($strSQL) AS tmpQuery WHERE 1=1 ".$strSearch.$strOrder;

        if ($res = $this->ci->db->query($queryString)->result_array())
          $this->dataset['recordsFiltered'] = count($res);
    }


    return true;
  }

  private function _generateTableHTML() {

    $strCaption = ($this->ci->lang->line($this->caption) == '') ? $this->caption : $this->ci->lang->line($this->caption);
    
    if ($strCaption == "") {
      $strResult ="
      <div class='row'>
      <article class='col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable'>
      <div class='jarviswidget jarviswidget-sortable' id='widget-id-".$this->dataGridID."' data-widget-editbutton='true' data-widget-deletebutton='false' data-widget-fullscreenbutton='true' role='widget' style='margin: 0px!important'>
        <header role='heading' style='height: 1px; border-top: none;'>
        </header>
        <div role='content'>
          <div class='jarviswidget-editbox'></div>
          <div class='widget-body no-padding' style='margin-top:-2px;'>
      <div class='table-responsive'>";
    }
    else {
      $strResult ="
      <div class='row'>
      <article class='col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable'>
      <div class='jarviswidget jarviswidget-sortable' id='widget-id-".$this->dataGridID."' data-widget-editbutton='true' data-widget-deletebutton='false' data-widget-fullscreenbutton='true' role='widget' style=''>
        <header role='heading'>
            <div class='jarviswidget-ctrls' role='menu'>
                <a href='javascript:void(0);' class='button-icon' rel='tooltip' data-placement='bottom' title='' data-original-title='Reload ".$this->caption."' id='reload_dataTable_".$this->dataGridID."'><i class='fa fa-refresh'></i></a>
                <a href='javascript:void(0);' class='button-icon jarviswidget-toggle-btn' rel='tooltip' title='' data-placement='bottom' data-original-title='Collapse'>
                    <i class='fa fa-minus '></i></a>
                <a href='javascript:void(0);' class='button-icon jarviswidget-fullscreen-btn' rel='tooltip' title='' data-placement='bottom' data-original-title='Fullscreen'>
                    <i class='fa fa-expand '></i></a>
            </div>
          <span class='widget-icon'> <i class='fa fa-table'></i> </span>
          <h5>".$strCaption."</h5>
          <span class='jarviswidget-loader' style='display: none;'><i class='fa fa-refresh fa-spin'></i></span>
        </header>
        <div role='content'>
          <div class='jarviswidget-editbox'></div>
          <div class='widget-body no-padding' style='margin-top:-2px;'>
      <div class='table-responsive'>";
    }

    $arrTableHeader = $this->getDepthLevelHeaderTable();
    $strResult .= "<table id='".$this->dataGridID."' cellspacing=0 width='100%' class='table table-striped table-hover table-bordered dataTable'><thead>";
    
    foreach ($arrTableHeader AS $row => $columns) {
      $strResult .= "<tr>";
      foreach ($columns AS $column) {
        $arrHeaderAttr = array();
        if (isset($column['headerAttr'])) {
          foreach ($column['headerAttr'] AS $prop => $val) {
            $arrHeaderAttr[] = $prop."=".$val;
          }
        }

        $titleHeader = ($this->ci->lang->line($column['title']) == '') ? $column['title'] : $this->ci->lang->line($column['title']);
        $strResult .= "<th ".implode(' ', $arrHeaderAttr).">".$titleHeader."</th>";
      }
      $strResult .= "</tr>";
    }

    if ($this->hasColumnFilter) {

      $strResult .= "<tr>";
      $counter = 0;

      $this->ci->load->helper('form');
      foreach ($this->column AS $col) {

        foreach ($arrTableHeader AS $row => $columns) {
          foreach ($columns AS $column) {
            if ($col['dataIndex'] == $column['dataIndex']) {
              if (!isset($column['headerAttr']['colspan']) || $column['headerAttr']['colspan'] == 1) {
                $strContent = (isset($this->arrColumnFilter[$col['dataIndex']]['type'])) ? $this->arrColumnFilter[$col['dataIndex']]['type'] : '';
                $arrInputAttr = array(
                'id' => "filter_".$col['dataIndex']."_".$this->dataGridID,
                'name' => "filter_".$col['dataIndex']."_".$this->dataGridID,
                'class' => "form-control input-xs"
                );

                switch ($strContent) {
                  case 'select' :
                    $strContent = form_dropdown($arrInputAttr, $this->arrColumnFilter[$col['dataIndex']]['dataOption'], '', '');
                    break;
                  case 'text' :
                    $strContent = form_input($arrInputAttr, '', '');;
                    break;
                  case 'date' :
                    $strContent = form_input($arrInputAttr, '', '');;
                    break;
                }
                $strResult .= "<th >" . $strContent . "</th>";

                if ($strContent != '')
                  $this->arrColumnFilter[$col['dataIndex']]['indexColumn'] = $counter;
                $counter++;

              }

              break;
            }
          }
        }
      }

      $strResult .= "</tr>";

    }

    $strResult .= "</thead><tbody></tbody><tfoot></tfoot></table>";
    $strResult .= "</div></div></div></div></article></div>";

    return $strResult;
  }

  private function _generateButtons() {

    $strResult = "<div class='text-".$this->actionButtonAlign."'>";
    foreach ($this->buttons AS $button) {

      $strValueButton = ($this->ci->lang->line($button['value']) == '') ? $button['value'] : $this->ci->lang->line($button['value']);
      if ($button['special'])
        $strResult .= "<button class='".$button['class']."' name='".$button['name']."' id='".$button['id']."_".$this->dataGridID."' type='".$button['type']."' disabled>".$strValueButton."</button>&nbsp;";
      else
        $strResult .= "<button class='".$button['class']."' name='".$button['name']."' id='".$button['id']."_".$this->dataGridID."' type='".$button['type']."'>".$button['value']."</button>&nbsp;";
    }
    $strResult .= '</div>';
    $this->strActionButton = $strResult;
    //return $strResult;
  }

  private function  _generateJS() {
    $strResult = '';
    $initScriptJS = "";
    $initScriptCSS = "";
    $strInitFixedHeader = '';
    $strInitResponsive = '';
    $strInitFixedColumn = '';
    $strInitFixedColumnCallback = '';
    $strInitColumnFilter = '';
    $strInitRowCallback = '';
    $strInitExportButton = '';
    $strInitPaging = '';
    $strInitInfo = '';
    $indexColumnOrder = 0;
    $OrderDir = 'asc';

    if ($this->hasNumberingColumn) $indexColumnOrder++;
    if ($this->hasCheckBoxColumn) $indexColumnOrder++;

    $arrColumn = array();

    $intCol = $indexColumnOrder;
    foreach ($this->column AS $column) {
      if (!isset($column['headerAttr']['colspan'])) {
        $arrColumnProperties = array();

        if (!isset($column['dataIndex'])) $arrColumnProperties[] = " dataProp: null,render: function(o) { return ''}";
        else if (is_null($column['dataIndex']) || empty($column['dataIndex'])) $arrColumnProperties[] = " dataProp: null,render: function(o) { return ''}";
        else $arrColumnProperties[] = "data: '".$column['dataIndex']."'";

        if (isset($column['colProperties'])) $arrColumnProperties[] = $column['colProperties']; //"width: '".$column['headerWidth']."'";

        if (isset($column['colRenderer']))
          $arrColumnProperties[] = "render :  function ( data, type, row, meta ) { ".$this->parseRenderString($column['colRenderer'])."}";

        $arrColumn[] = "{ ".implode(',', $arrColumnProperties)." }";

        if (isset($this->arrSummaryDataIndex[$column['dataIndex']]))
          $this->arrSummaryColIndex[] = $intCol;

        $intCol++;
      }
    }

    // load datatables jquery javascript
    $initScriptJS .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesJSPath);
    $initScriptJS .= $this->_generateLoadJSString(base_url().'assets/'.'js/datatables/moment.min.js');
    $initScriptJS .= $this->_generateLoadJSString(base_url().'assets/'.'js/datatables/datetime-moment.js');
    $initScriptJS .= $this->_generateLoadJSString(base_url().'assets/'.'js/datatables/dataTables.keyTable.min.js');
    $initScriptJS .= $this->_generateLoadJSString(base_url().'assets/'.'js/datatables/fnSetFilteringDelay.js');
    
    $initScriptJS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $initScriptJS);
    $initScriptJS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $initScriptJS);
        
    // load bootstrap style if use bootstrap
    if ($this->bootstrapStyle) {
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesBootstrapJSPath);
      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTablesBootstrapCSSPath);
      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTableskeyTableCSSPath);
    }

    // fixed header script if use fixed header
    if ($this->useFixedHeader) {
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesFixedHeaderJSPath);
      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTablesFixedHeaderCSSPath);

      $strInitFixedHeader = "new $.fn.dataTable.FixedHeader( tableDt_".$this->dataGridID." );";
    }

    // fixed column script if use fixed column
    if ($this->useFixedColumn) {
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesFixedColumnJSPath);
      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTablesFixedColumnCSSPath);

      // $strInitFixedColumn = "scrollX:true, scrollCollapse:true, fixedColumns:{leftColumns:".$this->fixedColumProp['left'].", rightColumns:".$this->fixedColumProp['right']."},";
       $strInitFixedColumn = "scrollX:true, scrollCollapse:true,";
       $strInitFixedColumnCallback = "new $.fn.dataTable.FixedColumns(tableDt_".$this->dataGridID.", {leftColumns:".$this->fixedColumProp['left'].", rightColumns:".$this->fixedColumProp['right']."});";
    }
    
    // fixed header script if use fixed header
    if ($this->useResponsivePlugin) {
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesResponsiveJSPath);
      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTablesResponsiveCSSPath);

      $strInitResponsive = "responsive : true,";
    }
    
    $strInitExportButtonStyle = '';
    $strInitExportButton = 'buttons:[],';
    if ($this->showExportButton) {
      if (in_array('csv', $this->exportButtonType)) {

      }
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesButtonJSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesButtonFlashJSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesButtonHtml5JSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesButtonPrintJSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesJsZipJSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesPdfMakeJSPath);
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesPdfFontJSPath);

      $initScriptCSS .= $this->_generateLoadCSString(base_url().'assets/'.$this->dataTablesButtonCSSPath);

      $strInitExportButton = "buttons: ['".implode("','", $this->exportButtonType)."'],";

      if ($this->bootstrapStyle) {
        $strInitExportButtonStyle = "
          jQuery('#button-export-place_".$this->dataGridID."').find('a').each(function() {
            $(this).removeClass('dt-button');
            $(this).addClass('btn btn-sm btn-default');
          });

          jQuery('#button-export-place_".$this->dataGridID." .dt-buttons:eq(0)').addClass('pull-right');
        ";
      }
    }

    // handle jquery autocomplete
    $strAutoCompleteSourceList = '';
    $strInitAutoCompleteCallback = '';
    if (!empty($this->autoCompleteSourceList)) {
      $strGroupingAuJS = '
          _renderItem: function( ul, item ) {
            return $( "<li>" )
              .append( "<a>" + item.value + " : " + item.label + "</a>" )
              .appendTo( ul );
          }';

      // $initScriptJS .= "
      //   var style = document.createElement('link');
      //   style.rel = 'stylesheet';
      //   style.type = 'text/css';
      //   style.href = '".base_url()."assets/css/smartadmin/smartadmin-production-plugins.min.css';
      //   jQuery('head').append(style);
      //   ";

      //$initScriptCSS = $this->_generateLoadCSString(base_url()."assets/css/smartadmin/smartadmin-production-plugins.min.css");
      $initScriptCSS = $this->_generateLoadCSString(base_url()."assetsvendors/base/vendors.bundle.css");

      foreach ($this->autoCompleteSourceList AS $idx => $arrAC) {
        $strAutoCompleteSourceList .= $arrAC['dataList'];

        $strInitAutoCompleteCallback .= '
        $.widget( "custom.autocomplete", $.ui.autocomplete, {
          _create: function() {
            this._super();
            this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
          },'.$strGroupingAuJS.'
        });

        $( ".auto_complete_'.$idx."_".$this->dataGridID.'" ).each(function() {
          $( this ).autocomplete({
            minLength: '.$arrAC['minLength'].',
            source: function( request, response ) {
              var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
              response( $.grep( '.$idx.'_options_'.$this->dataGridID.', function( data ) {
                return (matcher.test( data.label ) || matcher.test( data.value ) || matcher.test( data ));
              }) );
            },
            focus: function( event, ui ) {
              $( this ).val( ui.item.value );
              return false;
            },
            select: function( event, ui ) {
              $( this ).val( ui.item.value );
              $( this ).next().html( ui.item.label );
            }
          })
        });';

      }
    }

    $strRowOrderingJs = '';
    $strRowCallbackOrdering = '';
    if ($this->rowOrderingParams['url'] != '') {
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/'.$this->dataTablesRowOrderingJSPath);
      $strRowOrderingJs = "
        jQuery('#".$this->dataGridID."').dataTable()
          .rowReordering({
            sURL : '".$this->rowOrderingParams['url']."',
            sRequestType : '".$this->rowOrderingParams['request_type']."',
            iIndexColumn : ".$this->rowOrderingParams['indexColumn']."
          });
      ";

      $strRowCallbackOrdering = "
        $(row).attr('id', data.".$this->rowOrderingParams['dataIndex'].");
        $(row).attr('data-position', index);
        $(row).attr('title', 'drag n drop for ordering');
        $(row).attr('style', 'cursor:move;');
      ";
    }

    if ($this->hasNumberingColumn) {
      $attribute = "$('td:eq(".$this->columnNumberingIndex.")', row).css('text-align', 'right');";
      $strInitRowCallback .= "
        var api = this.api();
        var info = api.page.info();
        var page = info.page;
        var length = info.length;
        var start = (page * length + (index +1));

        $('td:eq(".$this->columnNumberingIndex.")', row).html(start);
        $attribute";
    }

    $arrOrderColumn[] = "[$indexColumnOrder, '$OrderDir']";
    if (!empty($this->defaultOrder)) {
      $arrOrderColumn = array();
      //foreach($this->defaultOrder AS $order) {
      //$order = $this->defaultOrder;
      $indexColumnOrder = 0;
      if ($this->hasNumberingColumn) $indexColumnOrder++;
      if ($this->hasNumberingColumn) $indexColumnOrder++;
      $OrderDir = 'asc';

      if (isset($this->defaultOrder['dataIndex'])){
        $counter = 0;
        foreach ($this->column AS $column) {
          if (isset($column['dataIndex']) && ($column['dataIndex'] == $this->defaultOrder['dataIndex']) )
            $indexColumnOrder = $counter;
          $counter++;
        }
      }
      if (isset($this->defaultOrder['dir']) && ($this->defaultOrder['dir'] == 'asc' || $this->defaultOrder['dir'] == 'desc') )
        $OrderDir = $this->defaultOrder['dir'];

      $arrOrderColumn[] = "[$indexColumnOrder, '$OrderDir']";
      //}
    }
    $strOrder = "order: [".implode(",", $arrOrderColumn)."],";

    $strDomSearchFilter = ($this->showSearchFilter) ? 'f' : '';
    $strSDOMTable = "sDom: \"<'dt-toolbar'<'col-xs-12 col-sm-5'$strDomSearchFilter><'col-sm-2 col-xs-12'l><'#button-export-place_".$this->dataGridID.".col-sm-5 col-xs-6 hidden-xs'B>r>\"+
              \"t\"+
              \"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>\"";
    if (!empty($this->buttons)) {
      if (strtolower($this->actionButtonPosition) == 'top') {
        $strButtonSdom = "<'#datagrid_actionButton_" . $this->dataGridID . ".dt-toolbar dt-toolbar-buttons'>";
        $strSDOMTable = "
          sDom: \"<'dt-toolbar'<'col-xs-12 col-sm-5'$strDomSearchFilter><'col-sm-2 col-xs-12'l><'#button-export-place_".$this->dataGridID.".col-sm-5 col-xs-6 hidden-xs'B>r>\"+
                \"$strButtonSdom\" +
                \"t\"+
                \"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>\"";
      }
      else {
        $strButtonSdom = "<'#datagrid_actionButton_" . $this->dataGridID . ".dt-toolbar-footer dt-toolbar-footer-buttons'>";
        $strSDOMTable = "
          sDom: \"<'dt-toolbar'<'col-xs-12 col-sm-5'$strDomSearchFilter><'col-sm-2 col-xs-12'l><'#button-export-place_".$this->dataGridID.".col-sm-5 col-xs-6 hidden-xs'B>r>\"+
                \"t\"+\"$strButtonSdom\"+
                \"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>\"";
      }
    }

    $strInitCheckbox = '';
    if ($this->hasCheckBoxColumn) {
      $strInitCheckbox = "

        jQuery('#checkBox_checkAll_".$this->dataGridID."').on( 'click',function () {
          if (jQuery(this).is(':checked')) {
            jQuery('#".$this->dataGridID." tbody input.checkBox_".$this->dataGridID.":checkbox').each(function() {
              if (!jQuery(this).is(':checked')) {
                jQuery(this).click();
              }
            });
          }
          else{
            jQuery('#".$this->dataGridID." tbody input.checkBox_".$this->dataGridID.":checkbox').each(function(el) {
              if (jQuery(this).is(':checked')) {
                jQuery(this).click();
              }
            });
          }
        });

        jQuery('#".$this->dataGridID." tbody').on( 'click', 'input.checkBox_".$this->dataGridID.":checkbox', function () {
            if (jQuery(this).is(':checked')) {
              var tmp= [jQuery(this).val() ];

              if (selectedCheckBox_".$this->dataGridID.".indexOf(jQuery(this).val()) == -1)
                selectedCheckBox_".$this->dataGridID.".push(jQuery(this).val());

              jQuery(this).closest( 'tr' ).addClass('active');
              jQuery(this).closest( 'tr' ).addClass('selected');
            }
            else {
              if (selectedCheckBox_".$this->dataGridID.".indexOf(jQuery(this).val()) != -1)
                selectedCheckBox_".$this->dataGridID.".splice(selectedCheckBox_".$this->dataGridID.".indexOf(jQuery(this).val()),1);

              jQuery(this).closest( 'tr' ).removeClass('active');
              jQuery(this).closest( 'tr' ).removeClass('selected');
            }


            jQuery('#".$this->dataGridID."').DataTable().rows().data().each(function(data, index) {
              prop = data.".$this->checkBoxColumnIndex.";
              var isSelected = jQuery('#checkBox_".$this->dataGridID."_'+index).is(':checked');
              var isExists = selectedRowData_".$this->dataGridID.".hasOwnProperty(prop);

              if ( isExists && !isSelected)
                delete selectedRowData_".$this->dataGridID."[prop];
              else if (isSelected && !isExists)
                selectedRowData_".$this->dataGridID."[data.".$this->checkBoxColumnIndex."] = data;
            });

            checkStateCheckAllCheckBox();

        });";
    }

    if (!empty($this->buttons))
      $initScriptJS  .= $this->_generateLoadJSString(base_url().'assets/js/jquery.redirect.js');

    $strInitSpecialButton = '';
    if ($this->hasSpecialButton) {
      $strInitSpecialButton = "
        jQuery('#".$this->dataGridID." tbody').on( 'click', 'input.checkBox_".$this->dataGridID.":checkbox', function () {
          if (parseFloat(jQuery('input.checkBox_".$this->dataGridID.":checkbox:checked').length) > 0) {
            jQuery('.buttonSpecial_".$this->dataGridID."').removeAttr('disabled');
          }
          else {
            jQuery('.buttonSpecial_".$this->dataGridID."').attr('disabled', 'disabled');
          }
        });";
    }

    foreach($this->buttons AS $btn) {

        $strAction = "
          var postInput = changedInputRowData_".$this->dataGridID.";
          var params = {'postCheckBox' : selectedCheckBox_".$this->dataGridID.", 'postInput' : postInput, 'postExtra' : {".implode(",", $this->dataGridExtraParams)."}}
        ";

        switch (strtolower($btn['clientAction'])) {
          case 'open':
            # code...
            $strAction .= "jQuery.redirect('".$btn['serverAction']."', params, 'POST', '_blank')";
            break;
          case 'redirect':
            # code...
            $strAction .= "jQuery.redirect('".$btn['serverAction']."', params, 'POST')";
            break;
          # post
          default:
            $strAction .= "
//            alert(JSON.stringify(params));
            jQuery.ajax({
              url : '".$btn['serverAction']."',
              type : 'POST',
              data : params,
              dataType: 'json',
              beforeSend: function() {
                 $('#".$this->dataGridID."_processing').show();
              },
              complete: function(){
                $('#".$this->dataGridID."_processing').hide();
              },
              success : function(respond) {
                if (respond.success == '1') {
                  jQuery.smallBox({
                    title : 'Success',
                    sound : false,
                    content : respond.message,
                    color : '#739E73',
                    timeout: 5000,
                    icon : 'fa fa-check',
                  });
                  changedInputRowData_".$this->dataGridID." = {};
                  selectedCheckBox_".$this->dataGridID." = [];
                  jQuery('#".$this->dataGridID."').DataTable().ajax.reload();
                  ".$btn['jsAfterSave']."
                }
                else if (respond.success == 0) {
                  $.smallBox({
                    title : 'Error',
                    sound : false,
                    content : respond.message,
                    color : '#C46A69',
                    timeout: 5000,
                    icon : 'fa fa-warning shake animated'
                  });
                }
              }
            });
            ";
            # code...
            break;
        }

        $strInitSpecialButton .= "
          jQuery('#".$btn['id']."_".$this->dataGridID."').on('click', function() {
            ".$strAction."
          });";
      //}
    }

    $strJsButtonDelete = '';
    if (!empty($this->rowButtonDelete)) {
      $arrDataKeys = explode(',', $this->rowButtonDelete['dataIndex']);

      $arrDataTemp = array();
      foreach($arrDataKeys AS $key) {
        $arrDataTemp[] = "'".$key."' : data.$key";
      }

      $strData = "{ 'tableName' : '".$this->rowButtonDelete['tableName']."',";
      if (!empty($this->dataGridExtraParams)) $strData .= implode(",", $this->dataGridExtraParams).",";
      $strData .= "'dataIndex' : {".implode(',', $arrDataTemp)."}}";

      $strJsButtonDelete = "
        if (typeof alertify === 'undefined') {
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = '".base_url()."assets/js/alertify.min.js';
          jQuery('head').append(script);

          var style = document.createElement('link');
          style.rel = 'stylesheet';
          style.type = 'text/css';
          style.href = '".base_url()."assets/css/alertify/alertify.min.css';
          jQuery('head').append(style);

          var style = document.createElement('link');
          style.rel = 'stylesheet';
          style.type = 'text/css';
          style.href = '".base_url()."assets/css/alertify/alertify.bootstrap.min.css';
          jQuery('head').append(style);
        }

       jQuery('#".$this->dataGridID." tbody').on( 'click', 'button.btn-delete-".$this->dataGridID."', function () {
          var data = jQuery('#".$this->dataGridID."').DataTable().row( $(this).parents('tr') ).data();

          alertify.confirm('Delete', 'Delete This Data ?',
          function(){
            jQuery.ajax({
                url : '".current_url()."?ajax".$this->dataGridID."=1&action=deleteRowData',
                type : 'POST',
                data : $strData,
                dataType: 'json',
                beforeSend: function() {
                   $('#".$this->dataGridID."_processing').show();
                },
                complete: function(){
                  $('#".$this->dataGridID."_processing').hide();
                },
                success : function(respond) {
                  if (respond.success == '1') {
                    jQuery.smallBox({
                      title : 'Success Deleting Data',
                      sound : false,
                      content : '<i class=\"fa fa-check\"></i> <i>' + respond.message + '</i>',
                      color : '#009d4e',
                      timeout: 5000,
                      iconSmall : 'fa fa-times fa-2x fadeInRight animated'
                    });
                    jQuery('#".$this->dataGridID."').DataTable().ajax.reload();
                  }
                  else if (respond.success == 0) {
                    $.smallBox({
                      title : 'Error Deleting Data',
                      sound : false,
                      content : '<i class=\"fa fa-times\"></i> <i>' + respond.message + '</i>',
                      color : '#9d0000',
                      timeout: 5000,
                      iconSmall : 'fa fa-times fa-2x fadeInRight animated'
                    });
                  }
                }
              });
          },
          function(){
          });
       } );";
    }
   $strJsButtonEdit = '';

    if (!empty($this->rowButtonEdit)) {
      $strActionJs = '';
      if ($this->rowButtonEdit['action'] == 'form') {
        $strActionJs = "
          if ($('#".$this->rowButtonEdit['destination']."').length) {
            $('#".$this->rowButtonEdit['destination']."').find(':input').each(function(){
              if (data.hasOwnProperty($(this).attr('name'))) {

                //var type = this.tagName != 'INPUT' ? this.tagName : this.type;
                var type = $(this).prop('tagName') != 'INPUT' ? $(this).prop('tagName') : $(this).attr('type');
                var elName = $(this).attr('name');
                type = type.toLowerCase();

                if (type == 'select') {
                    $(this).val(data[$(this).attr('name')]).trigger('change');
                }
                else if (type == 'checkbox') {
                  if ( (data[$(this).attr('name')] == 't') || (data[$(this).attr('name')] == '1') )
                    $(this).attr('checked', 'checked')
                  else
                    $(this).removeAttr('checked');
                }
                else if (type == 'radio') {

                  if ( $('#'+elName+'_".$this->rowButtonEdit['destination']."_'+data[$(this).attr('name')]).length)
                    $('#'+elName+'_".$this->rowButtonEdit['destination']."_'+data[$(this).attr('name')]).prop('checked', 'checked').trigger('click');
                }
                else
                    $(this).val(data[$(this).attr('name')]).trigger('change');

                if ($(this).attr('editor-id-dest') !== undefined) {
                  var editorEl = $(this).attr('editor-id-dest');
                  $('#'+editorEl).code(data[$(this).attr('name')]);
                }

              }
            })

            // set focus
            $('#".$this->rowButtonEdit['destination']."').find(':input:visible:first').focus();
          }
          else {
            $.smallBox({
              title : 'Error',
              sound : false,
              content : 'Form with id : ".$this->rowButtonEdit['destination']." NOT FOUND',
              color : '#C46A69',
              timeout: 3000,
              icon : 'fa fa-warning shake animated'
            });
          }
        ";
      }
      else if ($this->rowButtonEdit['action'] == 'url') {
        $strHiddenForm = "";
        if  (is_array($this->rowButtonEdit['dataIndex'])) {
          $arrParams = array();
          foreach($this->rowButtonEdit['dataIndex'] AS $key => $val) {
            $arrParams[] = $key."='+data.".$val;
          }
          $strParams = implode("+'&",$arrParams);
          $strHiddenForm .= '<input type="hidden" name="'.$key.'" value="'."'+".'data.'.$val."+'".'" />';
        }
        else {
          $strParams = $this->rowButtonEdit['dataIndex']."='+data.".$this->rowButtonEdit['dataIndex'];
          $strHiddenForm .= '<input type="hidden" name="'.$this->rowButtonEdit['dataIndex'].'" value="'."'+".'data.'.$this->rowButtonEdit['dataIndex']."+'".'" />';
        }
        
        $strUrl = site_url($this->rowButtonEdit['destination']) . "?" . $strParams;
        if ($this->rowButtonEdit['windowType'] == 'popup') {
          $strActionJs = "$('#remoteModal_" . $this->dataGridID . "').modal();";
          $strActionJs .= "$('.modal-content .modal-body', '#remoteModal_" . $this->dataGridID . "').html('<iframe id=\"frame_remoteModal_". $this->dataGridID."\" width=\"100%\" height=\"500px\" location=0 src=\"".$strUrl."+'\" style=\"border:none;\"/>');";
          $strActionJs .= "$('#reload_remoteModal_" . $this->dataGridID . "').on('click', function() {
            $( '#frame_remoteModal_". $this->dataGridID."' ).attr( 'src', function ( i, val ) { return val; });

           });";
        }
        else if ($this->rowButtonEdit['windowType'] == 'window') {
          if (!isset($this->rowButtonEdit['windowProp']))
            $this->rowButtonEdit['windowProp'] = 'width=800,height=600, scrollbars=yes';
          $strActionJs = "window.open('".$strUrl.", '#remoteModal_" . $this->dataGridID . "', '".$this->rowButtonEdit['windowProp']."');";
        }
        else if ($this->rowButtonEdit['windowType'] == 'default') {
          $strActionJs = "
            var curForm = $(this).find('form');
            if (!curForm.length) {
              $(this).append(
                $('<form />', { action: '".site_url($this->rowButtonEdit['destination'])."', method: 'GET', target: '_blank' })
                  .append('".$strHiddenForm."')
              );
              curForm = $(this).find('form');
            }
            curForm[0].submit();";
        }
      }

      $strJsButtonEdit .= "
       jQuery('#".$this->dataGridID." tbody').on( 'click', 'button.btn-edit-".$this->dataGridID."', function () {
          var data = jQuery('#".$this->dataGridID."').DataTable().row( $(this).parents('tr') ).data();
          $strActionJs
       });";
    }

    $strJSButtonModal = '';
    if ($this->hasButtonModal) {

      $counter = 1;
      foreach ($this->arrButtonModal AS $btn) {

        if  (is_array($btn['dataIndex'])) {
          $arrParams = array();
          foreach($btn['dataIndex'] AS $key => $val) {
            $arrParams[] = $key."='+data.".$val;
          }
          $strParams = implode("+'&",$arrParams);
        }
        else
          $strParams = $btn['dataIndex']."='+data.".$btn['dataIndex'];

        $strUrl = site_url($btn['url']) . "?" . $strParams;

        $strJSButtonModal .= "
          jQuery('#".$this->dataGridID." tbody').on( 'click', 'button.btn-showModal-".$this->dataGridID."-".$counter."', function () {
            var data = jQuery('#".$this->dataGridID."').DataTable().row( $(this).parents('tr') ).data();
            $('#remoteModal_" . $this->dataGridID ."').modal();
            $('.modal-content .modal-body', '#remoteModal_" . $this->dataGridID ."').html('<iframe width=\"100%\" height=\"500px\" src=\"".$strUrl."+'\" style=\"border:none;\"/>');
         });";
        $counter++;
      }
    }

    $strJSRowDetail = '';
    if ($this->hasRowDetail) {
      $strJSRowDetail = "
        var detailRows_".$this->dataGridID." = [];
        $('#".$this->dataGridID." tbody').on( 'click', 'tr td.rowDetail-control', function () {
            var tr = $(this).closest('tr');
            var row = jQuery('#".$this->dataGridID."').DataTable().row( tr );
            var idx = $.inArray( tr.attr('id'), detailRows_".$this->dataGridID."  );

            if ( row.child.isShown() ) {
                tr.removeClass( 'details' );
                row.child.hide();

                // Remove from the 'open' array
                detailRows_".$this->dataGridID.".splice( idx, 1 );
            }
            else {
                tr.addClass( 'details' );
                row.child( format( row.data() ) ).show();

                // Add to the 'open' array
                if ( idx === -1 ) {
                    detailRows_".$this->dataGridID.".push( tr.attr('id') );
                }
            }
        } );

        // On each draw, loop over the `detailRows` array and show any child rows
        tableDt_".$this->dataGridID.".on( 'draw', function () {
            $.each( detailRows_".$this->dataGridID.", function ( i, id ) {
                $('#'+id+' td.rowDetail-control').trigger( 'click' );
            } );
        } );";

    }

    $strJsLoadColumnFilter = '';
    $strJsColumnFilter = '';
    if ($this->hasColumnFilter) {
      $strJsColumnFilter = 'orderCellsTop : true,';
      $strInitColumnFilter = "var api = this.api();
      api.columns().every(function (index) {";
      foreach($this->arrColumnFilter AS $dataIndex => $filter) {
        $strInitColumnFilter .= "
          if (index == ".$filter['indexColumn'].") {
            var column = this;

            $('#filter_".$dataIndex."_".$this->dataGridID."').on('change', function () {
              var val = $(this).val();
              column.search(val ? val : '', false, false).draw();
            });
          }";

        if (($filter['type'] == 'select') && (empty($filter['dataOption']))) {
          $strJsLoadColumnFilter = "
            if ($('#filter_".$dataIndex."_".$this->dataGridID." option').length) {
              var p = lastAjaxJson.dataFilter_" . $dataIndex . "_" . $this->dataGridID.";
              for (var key in p) {
                if (p.hasOwnProperty(key) && (key != '')) {
                  if (!$('#filter_".$dataIndex."_".$this->dataGridID." option[value=\"'+key+'\"]').length)
                    $('#filter_".$dataIndex."_".$this->dataGridID."').append('<option value=\"'+key+'\">'+key+'</option');
                }
              }
            }";
        }
      }

      $strInitColumnFilter .= "});";
    }

    $strInitRowSummary = '';
    if ($this->hasRowSummary) {
      $strInitRowSummary .="
            var api = this.api(), data;

            var total = new Array;
            var pageTotal = new Array;
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$.,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };";

      foreach ($this->arrSummaryColIndex AS $col ) {
        $strInitRowSummary .= "
            // Total over all pages
            total[".$col."] = api
                .column( ".$col." )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total over this page
            pageTotal[".$col."] = api
                .column( ".$col.", { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( ".$col." ).footer() ).html(
                '$'+pageTotal[".$col."] +' ( $'+ total[".$col."] +' total)'
            );";
      }

      $strInitRowSummary .="
            jQuery('#".$this->dataGridID." tfoot').html('');
            var arrColumns = api.columns();
            var strFooterTotal = '';
            for (i=0; i< arrColumns[0].length; i++){
              var val = (pageTotal[i] === undefined) ? '' : pageTotal[i];
              strFooterTotal += '<td class=\"text-center\">'+val+'</td>';
            }

            jQuery('#".$this->dataGridID." tfoot').append('<tr>'+strFooterTotal+'</tr>');
      ";

    }

    $strInitModalWindowJS = '';
    if ($this->hasModalWindow) {
      $strInitModalWindow = '
        <div class="modal fade" id="remoteModal_'.$this->dataGridID.'" tabindex="-1" role="dialog" aria-labelledby="remoteModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header bg-default clearfix no-padding">
                  <div class="jarviswidget-ctrls" role="menu">
                    <a href="javascript:void(0);" class="button-icon" rel="tooltip" title="reload" id="reload_remoteModal_'.$this->dataGridID.'"><i class="fa fa-refresh"></i></a>
                    <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
                  </div>
                </div>
                <div class="modal-body" style="padding: 5px !important;">
                </div>
              </div>
          </div>
        </div>';

      $strInitModalWindow = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strInitModalWindow);
      $strInitModalWindow = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strInitModalWindow);

      $strInitModalWindowJS = "jQuery('body').append('" . $strInitModalWindow . "');";
    }

    $strInitialSearch = '';
    if ($this->dataGridInitialSearch != '') {
      $strInitialSearch = "search: {search: '".$this->dataGridInitialSearch."'},";
    }

    if (!$this->showPaging) $strInitPaging = 'paging:false,';
    if (!$this->showInfo) $strInitInfo = 'info:false,';
    $strDtJS = "
        function format ( d ) {
            // `d` is the original data object for the row
            return '".$this->strRowDetailFormater."';
        };";

    if (empty($this->dataGridLoadEvent))
      $strDtJS .= "
      jQuery(document).ready(function(){";
    else
      $strDtJS .= "
        jQuery('".$this->dataGridLoadEvent['element']."').on('".$this->dataGridLoadEvent['eventTrigger']."', function(){
          if (! $.fn.DataTable.isDataTable( '#".$this->dataGridID."' )) {";

    //add by Dedy
    $strInitInputText = "jQuery('#".$this->dataGridID."').find('td').each(function() {
            $(this).has('input:text').addClass('no-padding');
          });";
    $strDtJS .= "
          $strInitModalWindowJS
          $strAutoCompleteSourceList
          var selectedCheckBox_".$this->dataGridID." = [];
          var selectedRowData_".$this->dataGridID." = {};
          var changedInputRowData_".$this->dataGridID." = {};
          //$.fn.dataTable.moment.format('YYYY-MM-DD');
          var tableDt_".$this->dataGridID." = jQuery('#".$this->dataGridID."').dataTable( {
            'scrollY' : '".$this->dataTableScrollY."',
            'scrollCollapse' : 'true',
            'paging' : 'false',
            $strSDOMTable,
            $strJsColumnFilter
            $strInitInfo
            $strInitPaging
            oLanguage: {
              sProcessing: '<div class=\"col-xs-12\" style=\"height:100%;\"><div class=\"loader\"></div></div>',
              sSearch : '<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>',
              sLengthMenu: ' _MENU_ ',
            },
            $strInitResponsive
            processing: true,
            serverSide: true,
            ajax: { 'url' : '".current_url()."?ajax".$this->dataGridID."=1',
                    'type' : 'POST',
                    'data': function ( d ) {
                        return $.extend( {}, d, {
                          'selectedCheckbox' : JSON.stringify(selectedCheckBox_".$this->dataGridID."),
                          'selectedRowData' : JSON.stringify(selectedRowData_".$this->dataGridID."),
                          'changedInputRowData' : JSON.stringify(changedInputRowData_".$this->dataGridID."),
                          ".implode(',', $this->dataGridExtraParams)."
                        });
                      }
            },
            deferRender: true,
            searching: true,            
            ordering: '".$this->enableOrdering."',
            columns: [".implode(',', $arrColumn)."],
            lengthMenu: [[10, 25, 50, 100, '-1'], [10, 25, 50, 100, 'All']],
            pageLength: ".$this->pagingLenght.",
            $strInitFixedColumn
            $strInitialSearch
            initComplete: function () {
              ".$this->strInitCompleteCustom."
              $strInitFixedHeader
              $strInitColumnFilter
              $strInitExportButtonStyle
            },
            $strInitExportButton
            $strOrder
            rowCallback: function( row, data, index ) {
              $strInitRowCallback
              $strRowCallbackOrdering
            },
            createdRow: function( row, data, dataIndex ) {
              jQuery(row).find('select[row-data-index]').each(function() {
                var dataProp = jQuery(this).attr('row-data-index');
                jQuery(this).val(data[dataProp]);
              })
            },
            drawCallback: function(settings) {
              var api = this.api();
              var lastAjaxParams = api.ajax.params();
              var lastAjaxJson = api.ajax.json();

              var selectedCheckbox = JSON.parse(lastAjaxParams.selectedCheckbox);
              selectedCheckBox_".$this->dataGridID." = selectedCheckbox;

              var changedInputRowData = JSON.parse(lastAjaxParams.changedInputRowData);

              //alert(JSON.stringify(changedInputRowData));
              changedInputRowData_".$this->dataGridID." = changedInputRowData;

              jQuery('#".$this->dataGridID." tbody input.checkBox_".$this->dataGridID.":checkbox').each(function() {
                if (selectedCheckbox.indexOf(jQuery(this).val()) >= 0)
                  jQuery(this).click();
              });

              checkStateCheckAllCheckBox();

              jQuery.each(changedInputRowData_".$this->dataGridID.", function(i, l){
                var rowIndex = jQuery('#'+i).parents('tr:first');
                var row = jQuery('#".$this->dataGridID."').DataTable().row(rowIndex).node();

                //jQuery(row).addClass('active selected');

                if (jQuery('#'+i)) jQuery('#'+i).val(l);
              });

              $strJsLoadColumnFilter
              $strInitAutoCompleteCallback
              $strInitInputText              
              ".$this->drawCallbackCustom."
            },
            footerCallback : function ( row, data, start, end, display ) {
              $strInitRowSummary
            },
            autoWidth : true
          });
          $strInitFixedColumnCallback
          $strRowOrderingJs

          jQuery('#datagrid_actionButton_" . $this->dataGridID . "').html(\"".$this->strActionButton."\");

          $strInitCheckbox
          $strInitSpecialButton

          function checkStateCheckAllCheckBox() {
            // if all checkbox checked then checked check all checkbox
            var allCheck = true;
            jQuery('#".$this->dataGridID." tbody input.checkBox_".$this->dataGridID.":checkbox').each(function(el) {
              if (!jQuery(this).is(':checked')) {
                allCheck = false;
              }
            });

            if (allCheck) {
              jQuery('#checkBox_checkAll_".$this->dataGridID."').attr('checked', 'checked');
            }
            else {
              jQuery('#checkBox_checkAll_".$this->dataGridID."').removeAttr('checked');
            }
          }

          $strJsButtonDelete
          $strJsButtonEdit
          $strJSButtonModal
          $strJSRowDetail
          $('#".$this->dataGridID."_filter input').removeClass('input-sm');

          jQuery('#".$this->dataGridID." tbody').on( 'change autocompletechange', ':input', function () {
            if (!jQuery(this).hasClass('checkBox_".$this->dataGridID."')) {
              var rowIndex = $(this).parents('tr:first');
              var data = jQuery('#".$this->dataGridID."').DataTable().row(rowIndex).data();
              var row = jQuery('#".$this->dataGridID."').DataTable().row(rowIndex).node();
              var rowCounter = jQuery('#".$this->dataGridID."').DataTable().row(rowIndex).index();

              var dataProp = jQuery(this).attr('row-data-index');
              var elVal = jQuery(this).val();
              var dataVal = data[dataProp];

              if ( jQuery(this).attr('type') == 'checkbox') {
                if (dataVal == 'f') dataVal = 0;
                else if (dataVal == 't') dataVal = 1;
                if (jQuery(this).is(':checked')) elVal = 1;
                else elVal = 0;
              }
              else if (jQuery(this).attr('type') == 'radio') {
              }

              //alert(dataVal + ' ' + elVal );

              var hasChangedData = false;
              if ( dataVal != elVal) {
                changedInputRowData_".$this->dataGridID."[jQuery(this).attr('id')] = elVal;
                hasChangedData = true;
              }
              else {
                var elProp = jQuery(this).attr('id');
                if (changedInputRowData_".$this->dataGridID.".hasOwnProperty(elProp)) {
                  delete changedInputRowData_".$this->dataGridID."[elProp];
                }
              }

              if (!hasChangedData) {
                jQuery(row).find(':input').each(function() {
                  if (jQuery(this).attr('row-data-index')) {
                    var dataCell = (data[jQuery(this).attr('row-data-index')]) ? data[jQuery(this).attr('row-data-index')] : '';
                    var inputVal = jQuery(this).val();
                     if ( jQuery(this).attr('type') == 'checkbox') {
                        if (dataCell == 'f') dataCell = 0;
                        else if (dataCell == 't') dataCell = 1;
                        if (jQuery(this).is(':checked')) inputVal = 1;
                        else inputVal = 0;
                      }
                      else if (jQuery(this).attr('type') == 'radio') {
                      }

                    if (dataCell != inputVal) {
                      hasChangedData = true;
                      return false;
                    }
                  }
                });
              }

              //alert(hasChangedData);
              if (jQuery('#checkBox_".$this->dataGridID."_'+rowCounter).length) {
                jQuery('#checkBox_".$this->dataGridID."_'+rowCounter).prop('checked', !hasChangedData).trigger('click');
              } else {
                if (!hasChangedData)
                  jQuery(row).removeClass('active selected');
                else
                  jQuery(row).addClass('active selected');
              }

              // post all hidden element
              jQuery(row).find(':input').each(function() {
                if ( jQuery(this).attr('type') == 'hidden') {
                  if (hasChangedData) {
                    changedInputRowData_".$this->dataGridID."[jQuery(this).attr('id')] = jQuery(this).val();
                  }
                  else {
                    var elProp = jQuery(this).attr('id');
                    if (changedInputRowData_".$this->dataGridID.".hasOwnProperty(elProp)) {
                      delete changedInputRowData_".$this->dataGridID."[elProp];
                    }
                  }
                }
              });
            }
          });
          tableDt_".$this->dataGridID.".fnSetFilteringDelay(1000);

          $('#reload_dataTable_".$this->dataGridID."').on('click', function() {
            jQuery('#".$this->dataGridID."').DataTable().ajax.reload();
          })
";

    if (!empty($this->dataGridLoadEvent))
      $strDtJS .= "}";

    $strDtJS .= "});";

    $strResult .= "<script type='text/javascript'>";
    //added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
    $strResult .= "
    jQuery.ajaxSetup({cache:true});";
    //end---added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
    
    $strResult .= $initScriptCSS.$initScriptJS.$strDtJS;
    $strResult .= "
    </script>";
    // minified generated JS script
    //$strResult = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strResult);
    //$strResult = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strResult);

    return $strResult;
  }

  private function _generateLoadCSString($cssPath) {
    if (empty($cssPath)) return '';
    return "
     var isScriptCSSLoaded = false;
     for(var i = 0; i < document.styleSheets.length; i++){
        if(document.styleSheets[i].href=='".$cssPath."'){
            isScriptCSSLoaded=true;
            break;
        }
     }

      if (!isScriptCSSLoaded) {
        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '".$cssPath."';
        jQuery('head').append(style);
      }
      ";
  }

  private function _generateLoadJSString($jsPath) {
    if (empty($jsPath)) return '';
    return "

      var scriptsJSLoaded = document.getElementsByTagName('script');
      var isScriptJsLoaded = false;
      for(var i = 0; i < scriptsJSLoaded.length; i++) {
         if(scriptsJSLoaded[i].getAttribute('src') == '".$jsPath."')
          isScriptJsLoaded = true;
      }

      if (!isScriptJsLoaded) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '".$jsPath."';
        jQuery('head').append(script);
      }";
  }

  private function parseRenderString($strRenderer, $strDataVar = 'data', $strRowData = 'row') {
    // replace {{ counter }} with meta.row
    $result = str_replace("{{counter}}", '\'+meta.row+\'', $strRenderer);
    // replace {{ value }} with data
    //$result = str_replace("{{value}}", "\''+data+'\'", $result);
    $result = str_replace("{{value}}", "'+data+'", $result);
    preg_match_all('/\{{([A-Za-z0-9-_ ]+?)\}}/', $result, $arrMatch);

    foreach($arrMatch[1] AS $idx => $varMeta) {
      //$result = str_replace("{{".trim($varMeta)."}}", "\''+".$strRowData.'.'.$varMeta."+'\'", $result);
      
      //added by Dedy, tambah variable data_json yang menyimpan seluruh info json yang di encode
      if ($varMeta == 'row') {
        $result = str_replace("{{row}}", "'+JSON.stringify(row).replace(/\\\"/g, \"&quot;\") +'", $result);
      }
      else {
        $result = str_replace("{{".trim($varMeta)."}}", "'+".$strRowData.'.'.$varMeta."+'", $result);
      }
    }
    

    $strResult = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $result);
    $strResult = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strResult);

    return $strResult;
  }

  private function getDepthLevelHeaderTable() {
    $deepLevel=1;
    $arrTableHeader = array();

    foreach ($this->column AS $column) {
      if (isset($column['headerAttr']['colspan']) && !isset($column['headerAttr']['rowspan']))
        $column['headerAttr']['rowspan'] =  1;
      if (isset($column['headerAttr']['rowspan'])) {
        $deepLevel--;
        if ($deepLevel < 1) $deepLevel = 1;
      }

      $arrTableHeader[$deepLevel][] = $column;

      if (isset($column['headerAttr']['colspan'])) {
        $deepLevel++;
      }
    }

    return $arrTableHeader;
  }

  private function stringStartsWith($haystack, $needle)
  {
       $length = strlen($needle);
       return (substr($haystack, 0, $length) === $needle);
  }

  private function stringEndsWith($haystack, $needle)
  {
      $length = strlen($needle);
      if ($length == 0) {
          return true;
      }

      return (substr($haystack, -$length) === $needle);
  }
}
