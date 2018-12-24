<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Form {

  public $formID;
  public $formAction;
  public $formMultipart = false;
  public $method = 'POST';
  public $caption = 'Form Input';
//  public $dateFormat = 'yy-mm-dd';
  public $dateFormat = 'dd-mm-yy';
  // basic or horizontal
  public $formStyle = 'inline';
  //top, bottom
  public $formButtonPosition = 'bottom';
  public $hasNavigationHeader = false;
  public $navigationHeaderListUrl = 'dataList';
  public $navigationTableSource = array('table' => '', 'key' => '');  
  public $disableFormJS = false;
  public $detailMaxHeight = 250;

  private $ci;
  private $tabs = array();
  private $fieldset = array();
  private $activeFieldset = '';
  private $activeTab= '';
  private $formDetail = array();
  private $activeFormDetail = array();
  private $formDetailNewRow = array();

  private $module;
  private $controller_name;
  private $method_name;
  private $blankObject = 0;


  private $resultString = '';
  private $formObject = '';

  private $formActionButton = array();
  private $headingActionButton = array();
  private $arrDateElement = array();
  private $arrTimeElement = array();
  private $arrAutoCompleteElement = array();
  private $arrAutoCompleteElementDetail = array();
  private $arrSelectElement = array();
  private $arrEditorElement= array();

  private $validationRules = array();
  private $validationMessages = array();
  private $arrDefaultValidation = array('required', 'remote', 'minlength', 'maxlength', 'rangelength', 'min', 'max', 'range', 'email', 'url', 'date', 'dateISO', 'number', 'digits', 'creditcard', 'equalTo');

  public function __construct($arrOptions) {
    $this->ci = &get_instance();
    $this->ci->load->helper('form');

    // check options action and id form
    if (isset($arrOptions['action']))
      $this->formAction = $arrOptions['action'];
    if (isset($arrOptions['id']))
      $this->formID = $arrOptions['id'];
    $this->module = "";//$this->ci->router->fetch_module();
    $this->controller_name = $this->ci->router->class;
    $this->method_name = $this->ci->router->fetch_method();
  }

  public function addTab($name, $id="", $attribute=array()) {
    $counter = count($this->tabs)+1;
    if ($id == '') $id = 'Tab_'.$counter.'_'.$this->formID;
    else $id = $id.'_'.$this->formID;
    if (isset($this->tabs[$id])) {
      show_error('tab with id '.$id.' already exists');
      exit();
    }

    $this->tabs[$id] = array('name' => $name);
    $this->activeTab = $id;
    $this->activeFieldset = '';
    $this->formObject[$id] = array('string' => '');
  }

  public function addFieldSet($name, $intNumberOfColumns = 1, $id="", $attribute=array()) {
    if ($id == '') $id = $name.'_'.$this->formID;

    if (isset($this->fieldSets[$id])) {
      show_error('fieldset with id '.$id.' already exists');
      exit();
    }

    $this->fieldset[$id] = array('column' => $intNumberOfColumns);
    $this->activeFieldset = $id;

    $arrDefaultAttr = array('id' => $id);
    $arrFieldsetAttr = array_merge($attribute, $arrDefaultAttr);

    $legend_text = ($this->ci->lang->line($name) == '') ? $name : $this->ci->lang->line($name);

    $strResult = form_fieldset($legend_text, $arrFieldsetAttr);
    $this->formObject[$id] = array('string' => $strResult);
    if (!empty($this->tabs)) $this->tabs[$this->activeTab]['element'][$id] = $this->formObject[$id]['string'];

  }

  public function addDetail($name, $intDefaultRows = 3, $bolHasDeleteButton = true, $bolHasAddMoreButton = true, $hasSequenceColumn=true) {
    $this->formDetail[$name] = array(
      'header' => array(),
      'input' => array(),
      'footer' => array(),
      'intDefaultRows' => $intDefaultRows,
      'hasSequenceColumn' => $hasSequenceColumn,
      'bolHasDeleteButton' => $bolHasDeleteButton,
      'bolHasAddMoreButton' => $bolHasAddMoreButton,
      'arrData' => array(),
      'dataMapping' => array(),
      'strJSAfterAdd' => array(),
      'strJSAfterDelete' => array(),
    );

    $this->activeFormDetail = $name;
    //$this->addFormDetail($name, $arrHeader, $arrInput, $arrData, $intDefaultRows, $bolHasDeleteButton, $bolHasAddMoreButton);
  }

  public function addDetailHeader($headerIndex, $headerTitle, $headerProp='') {
    $this->formDetail[$this->activeFormDetail]['header'][] = array(
      'headerIndex' => $headerIndex,
      'headerTitle' => $headerTitle,
      'headerProp' => $headerProp,
    );
  }

  //addDetailInput($inputType, $inputName, $headerIndex, $dataIndex='', $inputProp=array(), $inputDataType='string', $htmlBefore='', $htmlAfter='')
  public function addDetailInput($inputType, $inputName, $headerIndex, $dataIndex='', $inputProp=array(), $inputDataType='string', $htmlBefore='', $htmlAfter='', $renderType='content' /* content or footer */, $arrDataOption = array(), $indexDataGroup = 'category') {
    // langsung grouping by header index
    $this->formDetail[$this->activeFormDetail]['input_'.$renderType][$headerIndex][] = array(
      'inputType' => $inputType,
      'inputName' => $inputName,
      'inputProp' => $inputProp,
      'inputDataType' => $inputDataType,
      'dataIndex' => $dataIndex,
      'htmlBefore' => $htmlBefore,
      'htmlAfter' => $htmlAfter,
      'arrDataOption' => $arrDataOption,
      'indexDataGroup' =>$indexDataGroup
    );

    if ($inputType == 'autocomplete' || $inputType == 'autoComplete') {
      if (!empty($arrDataOption)) {

        $strDataList = 'var '.$inputName.'_detail_options_'.$this->formID.' = ';
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
        //END of LOGIC untuk data dari fungsi generateList dengan tipe array value-text

        foreach ($arrDataOption AS $key =>$valueOpt) {
          if (is_array($valueOpt)) {
            if ($indexDataGroup != '') {
              if (!isset($valueOpt[$indexDataGroup])) {
                show_error('no data with index ' . $indexDataGroup. ' on auto complete '.$name);
                exit();
              }
            }

            $arrDefaultOptions = array('value' => $key);
            $arrTempOptions[] = array_merge($valueOpt, $arrDefaultOptions);
          }
          else {
            $indexDataGroup = '';
            $arrTempOptions[] = array('value' => $key, 'label' => $valueOpt);
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
        $strDataList .= json_encode($arrTempOptions);


        $this->arrAutoCompleteElementDetail[$inputName] = array('source' => $inputName.'_detail_options_'.$this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);

      }
        // $this->arrAutoCompleteElement[$name] = array('source' => $name.'_options_'.$this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);

    }

  }  

  public function addDetailScriptAfterAddRow($strJS) {
    $this->formDetail[$this->activeFormDetail]['strJSAfterAdd'][] = $strJS;
  }

  public function addDetailScriptAfterDeleteRow($strJS) {
    $this->formDetail[$this->activeFormDetail]['strJSAfterDelete'][] = $strJS;
  }

  public function bindDetailData($arrData) {
    $this->formDetail[$this->activeFormDetail]['arrData'] = $arrData;
    if (!empty($arrData))
      $this->formDetail[$this->activeFormDetail]['intDefaultRows'] = count($arrData);
  }

  public function renderFormDetail() {
    foreach ($this->formDetail AS $name => $propDetail) {
      $elName = 'detail_'.$name.'_'.$this->formID;

      $strStyleHeight = ($this->detailMaxHeight > 0) ? 'style="height: '.$this->detailMaxHeight.'px;"' : '';
      $strResult = '
        <div class="table-responsive" '.$strStyleHeight.'>
          <table class="table table-condensed table-hover table-bordered table-striped" id="table_'.$elName.'">
            <thead>
              <tr>
                <th style="width:30px;">No</th>';

      // to do : handle rowspan n colspan
      foreach ($propDetail['header'] AS $header) {
        $strResult .= "<th ".$header['headerProp'].">".$header['headerTitle']."</th>";
      }

      if ($propDetail['bolHasDeleteButton']) {
        $strResult .= '<th style="width:30px;">&nbsp;</th>';        
      }

      $strResult .= "
              </tr>
            </thead>";

      if (isset($propDetail['input_content'])) $strResult .= '<tbody>';

      if (!isset($this->formDetailNewRow[$name])) $this->formDetailNewRow[$name] = '';

      for ($i=0; $i<$propDetail['intDefaultRows']; $i++) {
        $isInitStrNewRowJS = false;
        $rowSequence = ($i+1);
        $strResult .= '<tr id="rowDetail_'.$name.'_'.$this->formID.'_'.$rowSequence.'">';
        if (!$isInitStrNewRowJS)
          $strNewRowJS = '<tr id="rowDetail_'.$name.'_'.$this->formID.'_{{counter}}">';
        
        if ($propDetail['hasSequenceColumn']) {          
          $strHiddenDeleted = $this->addHidden('isDeleted_'.$name.'_'.$rowSequence, 0, '', true);
          $strResult .= '<td style="width:30px;" class="text-right"><span id="counterSequence_'.$name.'_'.$rowSequence.'">'.$rowSequence.'</span>'.$strHiddenDeleted.'</td>';

          $strHiddenDeleted = $this->addHidden('isDeleted_'.$name.'_{{counter}}', 0, '', true);       
          if (!$isInitStrNewRowJS)   
            $strNewRowJS .= '<td style="width:30px;" class="text-right"><span id="counterSequence_'.$name.'_{{counter}}">'.$rowSequence.'</span>'.$strHiddenDeleted.'</td>';
        }

        foreach ($propDetail['header'] AS $header) {
          if (isset($propDetail['input_content'][$header['headerIndex']])) {
            $arrInputColumn = $propDetail['input_content'][$header['headerIndex']];
            $arrStrInputColumn = array();
            $arrStrInputColumnJS = array();
            foreach ($arrInputColumn AS $column) {
              $colummProp = $column;
              $colummProp['inputName'] = $colummProp['inputName'].'_'.($i+1);

              // auto complete
              if (isset($this->arrAutoCompleteElementDetail[$column['inputName']])) {
                $this->arrAutoCompleteElement[$colummProp['inputName']] = $this->arrAutoCompleteElementDetail[$column['inputName']];
              }

              $colummProp['value'] = (isset($propDetail['arrData'][$i][$column['dataIndex']])) ? $propDetail['arrData'][$i][$column['dataIndex']] : '';

              if (isset($colummProp['inputProp']['defaultValue']) && ($colummProp['value'] == ''))
                $colummProp['value'] = $colummProp['inputProp']['defaultValue'];

              $arrStrInputColumn[] = $this->mappDetailInputToForm($colummProp);
              if (!$isInitStrNewRowJS) {
                $colummProp = $column;
                $colummProp['inputName'] = $column['inputName'].'_{{counter}}';
                $colummProp['value'] = '';
                if (isset($colummProp['inputProp']['defaultValue']) && ($colummProp['value'] == ''))
                  $colummProp['value'] = $colummProp['inputProp']['defaultValue'];

                $arrStrInputColumnJS[] = $this->mappDetailInputToForm($colummProp, false);              
              }
  
            }
            $strResult .= '<td>'.implode('',$arrStrInputColumn).'</td>';
            if (!$isInitStrNewRowJS)
              $strNewRowJS .= '<td>'.implode('',$arrStrInputColumnJS).'</td>';
          }
          else {
            $strResult .= '<td>&nbsp;</td>';
            if (!$isInitStrNewRowJS)
              $strNewRowJS .= '<td>&nbsp;</td>';
          }

        }
   
        if ($propDetail['bolHasDeleteButton']) {
          $strResult .= '
          <td style="width:30px;">
            <button type="button" class="btn btn-danger btn-xs" onclick="javascript:deleteRowDetail(\''.$this->formID.'\', \''.$this->activeFormDetail.'\', '.$rowSequence.');">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </td>';            
          if (!$isInitStrNewRowJS) 
            $strNewRowJS .= '
              <td style="width:30px;">
                <button type="button" class="btn btn-danger btn-xs" onclick="javascript:deleteRowDetail(\''.$this->formID.'\', \''.$this->activeFormDetail.'\', {{counter}});">
                  <span class="glyphicon glyphicon-remove"></span>
                </button>
              </td>';        
        }

        $strResult .= '</tr>';    
        if (!$isInitStrNewRowJS) {    
          $strNewRowJS .= '</tr>';        
          if ($this->formDetailNewRow[$name] == '') $this->formDetailNewRow[$name] = $strNewRowJS;
          $isInitStrNewRowJS = true;
        }
      }

      if (isset($propDetail['input'])) $strResult .= '</tbody>';

      $strResult .= '<tfoot>';

      if ($propDetail['bolHasAddMoreButton']) {
        $colSpan = count($this->formDetail[$this->activeFormDetail]['header']);
        if ($propDetail['bolHasDeleteButton']) $colSpan++;
        if ($propDetail['hasSequenceColumn']) $colSpan++;

        $strResult .= '
          <tr>
            <td colspan='.$colSpan.'>
              <button class="btn btn-sm btn-default btn btn-primary" name="btnAddNew" id="btnAddNew_'.$name.'" type="button" onclick="javascript:addRowDetail(\''.$this->formID.'\', \''.$this->activeFormDetail.'\');">
                <i class="fa fa-plus"></i> Tambah Data
              </button>
            </td>
          </tr>';
      }

      $strResult .= '</tfoot>';

      $strResult .= '
          <input type="hidden" id="numShow_'.$this->activeFormDetail.'" name="numShow_'.$this->activeFormDetail.'" value="'.$i.'"/>
          <input type="hidden" id="numSequence_'.$this->activeFormDetail.'" name="numSequence_'.$this->activeFormDetail.'" value="'.$i.'"/>
      </table></div>';
      $this->formObject[$elName] = array('string' => $strResult, 'title' => $name);
      
      if (!empty($this->fieldset)) 
        $this->fieldset[$this->activeFieldset]['element'][$elName] = $this->formObject[$elName]['string'];

    }

  }

  private function mappDetailInputToForm($detailInput, $generateValidation=true) {
    /*
      'inputType' => $inputType,
      'inputName' => $inputName,
      'inputProp' => $inputProp,
      'dataIndex' => $dataIndex,
      'htmlBefore' => $htmlBefore,
      'htmlAfter' => $htmlAfter,    
    */
    $strResult = '';
    switch ($detailInput['inputType']) {
      case 'label':
      case 'text':
      case 'autocomplete':
      case 'autoComplete':
      case 'checkbox':

        // addFormObject($type, $title, $name, $value, $arrAttribute, $dataType, $bolRequired = true, $bolEnable = true, $htmlBefore='', $htmlAfter='', $renderLabel = true, $serverAction="", $jsFunction = '',$intInputWidth=12, $arrDataOption=array(), $indexDataGroup='category', $useInDetail = false)
        $strResult = $this->addFormObject($detailInput['inputType'], '', $detailInput['inputName'], $detailInput['value'], $detailInput['inputProp'], $detailInput['inputDataType'], false, true, $detailInput['htmlBefore'], $detailInput['htmlAfter'],  false, '', '', 12, $detailInput['arrDataOption'], $detailInput['indexDataGroup'], true, $generateValidation);
        # code...
        break;
      case 'hidden' : 
        $strResult = $this->addHidden($detailInput['inputName'], $detailInput['value'], '', true);
        break;
      default:
        # code...
        break;
    }

    return $strResult;
  }


  public function addHidden($name, $value='', $strAttr='', $useInDetail = false) {
    if (isset($this->formObject[$name])) {
      show_error('input with id ' . $name . ' already exists');
      exit();
    }
    $strResult = '<input type="hidden" name="'.$name.'" id="'.$name.'_'.$this->formID.'" value="'.html_escape($value)."\" ".$strAttr." />\n";

    if ($useInDetail) return $strResult;
    $this->formObject[$name] = array('string' => $strResult);
  }

  public function addBlank()
  {
    $this->blankObject++;
    $this->addFormObject('blank', "", "blankLabel".$this->blankObject, "", array(), 'string', false, true);

  }

  public function addLabel($title, $name, $value="") {
    $this->addFormObject('label', $title, $name, $value, array(), 'string', false, true, '', '', true, "", '',9);
  }

  public function addInput($title, $name, $value="", $arrAttribute=array(), $dataType="string", $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('text', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction ,$intInputWidth);
  }

  // $arrDataOption = array('key' =>  'value');
  // $arrDataOption = array('key' =>  array('label' => '', 'category'=> '',...);
  public function addInputAutoComplete($title, $name, $arrDataOption = array(), $value , $indexDataGroup = 'category', $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true,$intInputWidth=12, $jsFunction = ''){
    $this->addFormObject('autoComplete', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction ,$intInputWidth, $arrDataOption, $indexDataGroup);
  }

  public function addFile($title, $name, $value="", $arrAttribute=array(), $dataType="string", $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('file', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction ,$intInputWidth);
  }

  public function addTextArea($title, $name, $value="", $arrAttribute=array(), $dataType="string", $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('textArea', $title, $name, $value, $arrAttribute, $dataType, $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction ,$intInputWidth);
  }

  public function addSelect($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('select', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction ,$intInputWidth, $arrDataOption, '');
  }

  public function addCheckBox($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true,$htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('checkBox', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
  }

  public function addCheckBoxInline($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('checkBoxInline', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
  }

  public function addRadio($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true,  $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('radio', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
  }

  public function addRadioInline($title, $name, $arrDataOption = array(), $value, $arrAttribute = array(), $bolRequired = true, $bolEnable = true, $htmlBefore="", $htmlAfter="", $renderLabel = true, $intInputWidth=12, $jsFunction = '') {
    $this->addFormObject('radioInline', $title, $name, $value, $arrAttribute, '', $bolRequired, $bolEnable, $htmlBefore, $htmlAfter, $renderLabel, "", $jsFunction, $intInputWidth, $arrDataOption);
  }

  public function addEditor($title, $name, $value, $bolRequired = true, $bolEnable = true, $renderLabel = true, $intInputWidth=12) {
    $this->addFormObject('editor', $title, $name, $value, array(), '', $bolRequired, $bolEnable, '', '', $renderLabel, "", '', $intInputWidth, array());
  }

  public function addSubmit($name, $value, $arrAttribute, $bolEnable = true, $htmlBefore="", $htmlAfter="", $serverAction = "")
  {
    $this->addCommonButton("submit", $name, $value, $arrAttribute, $bolEnable, $htmlBefore , $htmlAfter, $serverAction);
  }

  public function addReset($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = null)
  {
    $this->addCommonButton("reset", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore , $htmlAfter, "");
  }

  public function addButton($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = null)
  {
    $this->addCommonButton("button", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore , $htmlAfter, "");
  }
  // public function addHeadingButton($name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = null)
  // {
  //   $this->addHeadingButton("button", $name, $value, $arrAttribute, $bolEnabled, $htmlBefore , $htmlAfter, "");
  // }

  function addLiteral($title, $name, $literalValue, $renderLabel = true, $arrLabelAttribute = null) {
    $this->addFormObject('literal', $title, $name, $literalValue, array(), 'string', false, true, '', '', $renderLabel);
  }

  public function addValidationRule($idElement, $arrRule, $arrMessage) {
    if (isset($this->validationRules[$idElement]))
      $this->validationRules[$idElement] = array_merge($this->validationRules[$idElement], $arrRule);
    else
      $this->validationRules[$idElement] = $arrRule;

    if (isset($this->validationMessages[$idElement]))
      $this->validationMessages[$idElement] = array_merge($this->validationMessages[$idElement], $arrMessage);
    else
      $this->validationMessages[$idElement] = $arrMessage;
  }

  public function render() {
    $strMultipart = ($this->formMultipart) ? 'enctype="multipart/form-data"' : '';
    
    if (!$this->disableFormJS){
        $this->resultString .= $this->generateJSString();    
    }
      
    $strCaption = ($this->ci->lang->line($this->caption) == '') ? $this->caption : $this->ci->lang->line($this->caption);
    
    if (!$this->ci->input->get_post('is-nav-ajax')) {
        
    $this->resultString .= '
      <div class="row">
        <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

          <!-- Widget ID (each widget will need unique ID)-->
          <div class="jarviswidget" id="wid-id-'.$this->formID.'" data-widget-colorbutton="false" data-widget-editbutton="true" data-widget-deletebutton="false">
            <header role="heading">
              <!--<span class="widget-icon"> <i class="fa fa-pencil-square-o"></i> </span>-->
              <h5>'.$strCaption.'</h5>
              ';
    
    $this->resultString .= $this->renderHeadingButton();          
    $this->resultString .= '        
            </header>
            <div>
              <div class="jarviswidget-editbox"></div>

              <div class="alert alert-success fade in" id="'.$this->formID.'_success_alert" style="display:none;">
                <button class="close" data-dismiss="alert"> × </button>
                  <i class="fa-fw fa fa-check shake animated"></i>
                <strong>Success</strong>
              </div>

              <div class="alert alert-danger fade in" id="'.$this->formID.'_error_alert" style="display:none;">
                <button class="close" data-dismiss="alert"> × </button>
                  <i class="fa-fw fa fa-times shake animated"></i>
                <strong>Error</strong>
              </div>

              <!-- widget content -->
              <div class="widget-body">';
    }

    $this->resultString .= '        
                <form name="'.$this->formID.'" class="form-horizontal" '.$strMultipart.' id="'.$this->formID.'" action="'.$this->ci->config->site_url($this->formAction).'" method="'.$this->method.'" novalidate >
                <div class="alert alert-danger" id="alert_'.$this->formID.'" role="alert" style="display:none;"></div>';
    
          
    if ($this->formButtonPosition == 'top') 
      $this->resultString .= $this->renderFormButton();                

    $this->resultString .= $this->renderFormObject();
    if ($this->formButtonPosition == 'bottom') 
      $this->resultString .= $this->renderFormButton();                

    $this->resultString .='
                </form>';

    if (!$this->ci->input->get_post('is-nav-ajax')) {
      $this->resultString .='
              </div>
            </div>
          </div>
        </article>
      </div>';
    }

    if ($this->ci->input->get_post('is-nav-ajax')) {
      echo $this->resultString;
      exit();
    }
    return $this->resultString;
  }

  private function addCommonButton($buttonType, $name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = "")
  {
    $strResult = '';
    $arrAttr = array('name' => $name."_".$this->formID, 'id' => $name."_".$this->formID, 'value' => $value);
    $arrInputAttr = array_merge($arrAttribute, $arrAttr);

    $value = ($this->ci->lang->line($value) == '') ? $value : $this->ci->lang->line($value);

    if ($buttonType == 'button') {
      $this->addFormObject('button', '', $name, $value, $arrInputAttr, 'string', false, $bolEnabled, $htmlBefore, $htmlAfter, false, $serverAction);
    }else {
      switch($buttonType) {
        case 'submit' :
          $strJS = "onclick=javascript:$('input[name=submitButton_".$this->formID."]').val('".$name."');$('input[name=submitAction_".$this->formID."]').val('".$serverAction."');";
          $arrInputAttr['class'] = 'btn btn-sm btn-primary btn-submit-form';
          $arrInputAttr['type'] = 'submit';
          $content = '<i class="fa fa-save"></i> '.$value;
          $strResult .= form_button($arrInputAttr, $content, $strJS);
          break;
        case 'reset' :
          $arrInputAttr['class'] = 'btn btn-sm btn-success';
          $arrInputAttr['type'] = 'reset';
          $content = '<i class="fa fa-refresh"></i> '.$value;
          $strJSString = 'onclick="$(\'#'.$this->formID.'\')[0].reset(); $(\'#'.$this->formID.' input:hidden\').val(\'\');$(\'#'.$this->formID.' input:checkbox\').removeAttr(\'checked\'); $(\'#'.$this->formID.' input:radio\').removeAttr(\'checked\');"';

          $strResult .= form_button($arrInputAttr, $content, $strJSString);
          break;
      }
      $this->formActionButton[$arrAttr['name']] = $strResult;

    }
  }

  public function addHeadingButton($buttonType, $name, $value, $arrAttribute, $bolEnabled = true, $htmlBefore="", $htmlAfter="", $serverAction = "")
  {
    $strResult = '';
    $arrAttr = array(
      'name' => $name."_".$this->formID, 
      'id' => $name."_".$this->formID, 
      'form-target' => $this->formID,
      'action-target' => site_url($this->controller_name.'/'.$this->method_name)
    );

    $arrInputAttr = array_merge($arrAttribute, $arrAttr);

    $value = ($this->ci->lang->line($value) == '') ? $value : $this->ci->lang->line($value);
    $strResult .= '<span '._parse_form_attributes($arrInputAttr, array()).'>'.$value.'</span>';

    $this->headingActionButton[$arrAttr['name']] = $strResult;

  }

  private function addFormObject($type, $title, $name, $value, $arrAttribute, $dataType, $bolRequired = true, $bolEnable = true, $htmlBefore='', $htmlAfter='', $renderLabel = true, $serverAction="", $jsFunction = '',$intInputWidth=12, $arrDataOption=array(), $indexDataGroup='category', $useInDetail = false, $generateValidation = true)
  {
    if ($type != 'blank') {
      if (isset($this->formObject[$name])) {
        show_error('input with id ' . $name . ' already exists');
        exit();
      }
    }

    $strHelpBlock = '';
    if (isset($arrAttribute['helpblock']) ) {
      $strHelpBlock = $arrAttribute['helpblock'];
      unset($arrAttribute['helpblock']);
    }

    if (!isset($arrAttribute["class"]))
    {
      if ($type == 'select')
        $arrAttribute["class"] = "select2-selection__rendered";
      else
        $arrAttribute["class"] = 'form-control m-input';
    }

    if (!isset($arrAttribute["autocomplete"])){
      $arrAttribute["autocomplete"] = 'off';
    }


    $arrAttr = array(
      'name' => $name,
      'id' => $name.'_'.$this->formID,
      'class' => $arrAttribute["class"],
      'value' => $value);

    $arrInputAttr = array_merge($arrAttribute, $arrAttr);
    if (!$bolEnable) $arrInputAttr['disabled'] = 'true';

    if ($bolRequired) $arrInputAttr['required'] = 'required';

    $titleLabel = ($this->ci->lang->line($title) == '') ? $title : $this->ci->lang->line($title);

    $groupWidth = ($type == 'literal') ? 8 : 12;
    $strClassLabel = (!$renderLabel) ? 'sr-only': 'col-sm-12 control-label';
    $strResult = '
      <div class="form-group m-form__group">
        <label for="example_input_full_name">'.$titleLabel.'</label>
        <div class="col-sm-'.$groupWidth.'">';

    if (strtolower($this->formStyle) == 'basic') {
      $strResult = '
        <div class="form-group m-form__group col-sm-12 col-xs-12">
          <label for="example_input_full_name">'.$titleLabel.'</label>';

    }

    $strLabelBefore = ($htmlBefore != '') ? '<div class="input-group-addon">'.$htmlBefore.'</div>' : '';
    $strLabelAfter = ($htmlAfter != '') ? '<div class="input-group-addon">'.$htmlAfter.'</div>' : '';


    //$strInputGroupClass = ( ($strLabelBefore != '') || ($strLabelAfter != '') || ($dataType == 'date') || ($dataType == 'time')) ? "input-group col-md-".$intInputWidth : "";

    $strInputGroupClass = "input-group col-sm-".$intInputWidth." col-sm-".$intInputWidth." col-xs-12";

    $useInputGroup = true;
    $strInputGroup = '<div class="input-group-validation '.$strInputGroupClass.'">';

    $strFormElement = '';

    switch ($type) {
      case 'button' :
        if (isset($arrAttribute['class'])) $arrInputAttr['class'] = $arrAttribute['class'];
        else $arrInputAttr['class'] = 'btn btn-sm btn-default';
        $value = ($this->ci->lang->line($value) == '') ? $value : $this->ci->lang->line($value);
        $strResult .= form_button($arrInputAttr, $value, '');
        if ($useInDetail) return $strFormElement;
        break;

      case 'literal' :
        $strFormElement .= ($this->ci->lang->line($value) == '') ? $value : $this->ci->lang->line($value);
        if ($useInDetail) return $strFormElement;
        break;

      case 'blank' :
        $strFormElement .= $value;
        break;

      case 'label' :
        $strFormElement .= '<p class="form-control-static" id="'. $name.'_'.$this->formID.'">'.$value.'</p>';
        if ($useInDetail) return $strFormElement;
        break;

      case 'text' :
        switch($dataType) {
          case "date" :
            $this->arrDateElement[$name] = array('name' => $name, 'datePickerOption' => array());
            if (isset($arrAttribute['datePickerOption'])) {
              $this->arrDateElement[$name]['datePickerOption'] = $arrAttribute['datePickerOption'];
              // handle error php > 5.3
              unset($arrInputAttr['datePickerOption']);
            }

            $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
            $strFormElement .= '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
            if ($useInDetail) return $strFormElement;

            if ($generateValidation)
              $this->addValidationRule($name.'_'.$this->formID, array('date' => 'true'), array('date' => 'please input valid date'));
            break;
          case "time" :
            //$arrInputAttr['readonly'] = 'readonly';
            $this->arrTimeElement[$name] = $name;
            $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
            $strFormElement .= '<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>';
            //$this->addValidationRule($name, array('date' => 'true'), array('date' => 'please input valid date'));
            if ($useInDetail) return $strFormElement;
            break;
          case "numeric" :
            $arrInputAttr['class'] = 'form-control input-sm text-right';
            $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
            if ($generateValidation)
              $this->addValidationRule($name.'_'.$this->formID, array('number' => 'true'), array('date' => 'please input valid number'));
            if ($useInDetail) return $strFormElement;
            break;
          case "password" :
            $strFormElement .= form_password($arrInputAttr, $value, $jsFunction);
            if ($useInDetail) return $strFormElement;            
            break;
          default :
            $arrInputAttr['type'] = ($dataType == 'string') ? 'text' : $dataType;
            $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);
            if ($useInDetail) return $strFormElement;
            break;
        }
        break;

      case 'autoComplete' :
        $arrInputAttr['list'] = 'list_'.$name.'_'.$this->formID;

        $strLabelAutoCompleteBefore = ($htmlBefore != '') ? '<div class="input-group-addon" style="visibility: hidden;">'.$htmlBefore.'</div>' : '';
        $strLabelAutoCompleteAfter = ($htmlAfter != '') ? '<div class="input-group-addon" style="visibility: hidden;">'.$htmlAfter.'</div>' : '';

        // draw input text
        $strFormElement .= form_input($arrInputAttr, $value, $jsFunction);

        if (!$useInDetail) {
          //draw option auto complete
          $strDataList = 'var '.$name.'_options_'.$this->formID.' = ';
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
          //END of LOGIC untuk data dari fungsi generateList dengan tipe array value-text

          foreach ($arrDataOption AS $key =>$valueOpt) {
            if (is_array($valueOpt)) {
              if ($indexDataGroup != '') {
                if (!isset($valueOpt[$indexDataGroup])) {
                  show_error('no data with index ' . $indexDataGroup. ' on auto complete '.$name);
                  exit();
                }
              }

              $arrDefaultOptions = array('value' => $key);
              $arrTempOptions[] = array_merge($valueOpt, $arrDefaultOptions);
            }
            else {
              $indexDataGroup = '';
              $arrTempOptions[] = array('value' => $key, 'label' => $valueOpt);
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
          $strDataList .= json_encode($arrTempOptions);        
          $this->arrAutoCompleteElement[$name] = array('source' => $name.'_options_'.$this->formID, 'sourceString' => $strDataList, 'groupByField' => $indexDataGroup);
        }

        $strHelpText = '';

        if ($value != '')
          $strHelpText = (isset($arrDataOption[$value])) ? $arrDataOption[$value] : '';

        $strFormElement .= $strLabelAfter;
        if ($strLabelAutoCompleteBefore == '' && $strLabelAutoCompleteAfter == '') {
          $strFormElement .= '<p class="help-block" id="label_'.$name.'">'.$strHelpText.'</p>';
          //$strFormElement .= '</div>';
        }
        else {
          $strFormElement .= '</div>';
          $strFormElement .= '<div class="input-group">';
          $strFormElement .= $strLabelAutoCompleteBefore;
          $strFormElement .= '<p class="help-block" id="label_'.$name.'">'.$strHelpText.'</p>';
          $strFormElement .= $strLabelAutoCompleteAfter;
        }

        if ($useInDetail)
          return $strFormElement;

        $strLabelAfter = '';
        break;

      case 'textArea' :
        //$arrInputAttr['style'] = 'resize:vertical;';
        $strFormElement .= form_textarea($arrInputAttr, $value, $jsFunction);
        break;

      case 'select' :
        /*if (!isset($arrInputAttr['class']) || $arrInputAttr['class'] == "")
        {
          $arrInputAttr['class'] = 'select2';
        }*/
        $arrInputAttr['style'] = 'width:100%';

        $this->arrSelectElement[$name] = array(
        'id' => $name.'_'.$this->formID,
        'htmlAfter' => $strLabelAfter,
        'hasEmptyOption' => false,
        'placeHolder' => '');

        if (isset($arrDataOption[0])) {
          $this->arrSelectElement[$name]['hasEmptyOption'] = true;
          $this->arrSelectElement[$name]['placeHolder'] = current($arrDataOption);
        }


        if (isset($arrInputAttr['multiple'])) {
          $elName = $arrInputAttr['name'].'[]';
          $arrInputAttr['name'] = $elName;
        }

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

        $strFormElement .= form_dropdown($arrInputAttr, $arrDataOption, $value, $jsFunction);
        break;

      case 'checkBox' :
        $arrInputAttr['class'] = 'checkbox';
        $useInputGroup = false;
        foreach ($arrDataOption AS $key => $val) {
          $arrInputAttr['value'] = $key;
          $arrInputAttr['id'] = $name.'_'.$this->formID.'_'.$key;
          $strFormElement .= '<div class="checkbox"><label class="m-checkbox">';
          $checked = ($key == $value) ? true : false;
          $strFormElement .= form_checkbox($arrInputAttr, $key, $checked, $jsFunction).'<span>'.$val.'</span>';
          $strFormElement .= '</label></div>';
        }
        break;

      case 'checkBoxInline' :
        $arrInputAttr['class'] = 'checkbox';
        $useInputGroup = false;
        if  (strtolower($this->formStyle) == 'basic') $strFormElement .= '<div class="input-group">';
        foreach ($arrDataOption AS $key => $val) {
          $arrInputAttr['value'] = $key;
          $arrInputAttr['id'] = $name.'_'.$this->formID.'_'.$key;

          $strFormElement .= '<label class="checkbox-inline">';
          $checked = ($key == $value) ? true : false;
          $strFormElement .= form_checkbox($arrInputAttr, $key, $checked, $jsFunction).'<span>'.$val.'</span>';
          $strFormElement .= '</label>';
        }
        if  (strtolower($this->formStyle) == 'basic') $strFormElement .= '</div>';
        break;

      case 'radio' :
        $arrInputAttr['class'] = 'radiobox style-0';
        $useInputGroup = false;
        foreach ($arrDataOption AS $key => $val) {
          $arrInputAttr['value'] = $key;
          $arrInputAttr['id'] = $name.'_'.$this->formID.'_'.$key;
          $strFormElement .= '<div class="radio"><label>';
          $checked = ($key == $value) ? true : false;
          $strFormElement .= form_radio($arrInputAttr, $key, $checked, $jsFunction).'<span>'.$val.'</span>';
          $strFormElement .= '</label></div>';
        }
        break;

      case 'radioInline' :
        $arrInputAttr['class'] = 'radiobox style-0';
        $useInputGroup = false;
        if  (strtolower($this->formStyle) == 'basic') $strFormElement .= '<div class="input-group">';
        foreach ($arrDataOption AS $key => $val) {
          $arrInputAttr['value'] = $key;
          $arrInputAttr['id'] = $name.'_'.$this->formID.'_'.$key;

          $strFormElement .= '<label class="radio radio-inline">';
          $checked = ($key == $value) ? true : false;
          $strFormElement .= form_radio($arrInputAttr, $key, $checked, $jsFunction).'<span>'.$val.'</span>';
          $strFormElement .= '</label>';

        }
        if  (strtolower($this->formStyle) == 'basic') $strFormElement .= '</div>';
        break;

      case 'file' :
        $this->formMultipart = true;
        $strFormElement .= form_upload($arrInputAttr, '', $jsFunction);
        break;

      case 'editor' :
        $editorName = "editor_".$name.'_'.$this->formID;
        $strFormElement .= '<div id="'.$editorName.'">'.$value.'</div>';
        $this->arrEditorElement[$editorName] = array('id' => $editorName, 'hiddenEl' => $name);
        if (!empty($value)) $this->arrEditorElement[$editorName]['code'] = $value;
        $this->addHidden($name, $value, "editor-id-dest='".$editorName."'");
        $name = $editorName;
        break;
    }

    if ($useInputGroup) $strResult .= $strInputGroup.$strLabelBefore.$strFormElement.$strLabelAfter.'</div>';
    else $strResult .= $strLabelBefore.$strFormElement.$strLabelAfter;

    if ($strHelpBlock != '') {
      if($strHelpBlock == strip_tags($strHelpBlock)) {
        $strHelpBlock = ($this->ci->lang->line($strHelpBlock) == '') ? $strHelpBlock : $this->ci->lang->line($strHelpBlock);
      }
      $strResult .= '<span id="helpBlock_'.$name.'_'.$this->formID.'" class="help-block"><small>'.$strHelpBlock.'</small></span>';
    }

    if (strtolower($this->formStyle) == 'basic') {
      $strResult .= '</div>';
    }
    else {
      $strResult .= '</div></div>';
    }

    if ($bolRequired) $this->addValidationRule($name.'_'.$this->formID, array('required' => 'true'), array('required' => 'please enter '.$title));
    $this->formObject[$name] = array('string' => $strResult, 'title' => $title);
    if (!empty($this->fieldset) && ($this->activeFieldset != '')) $this->fieldset[$this->activeFieldset]['element'][$name] = $this->formObject[$name]['string'];
    if (!empty($this->tabs)) $this->tabs[$this->activeTab]['element'][$name] = $this->formObject[$name]['string'];
  }

  private function renderFormObject() {

    //print_r($this->tabs);
    //die();
    $this->addHidden('submitButton_'.$this->formID, '');
    $this->addHidden('submitAction_'.$this->formID, '');

    if ($this->hasNavigationHeader) {
      if (isset($this->navigationTableSource['table']) && !empty($this->navigationTableSource['table']) ) {
        $tableKey = (isset($this->navigationTableSource['key'])) ? $this->navigationTableSource['key'] : 'id';
        $arrQueryMax = $this->ci->db->select_max($tableKey)->from($this->navigationTableSource['table'])->get()->row_array();
        $arrQueryMin = $this->ci->db->select_min($tableKey)->from($this->navigationTableSource['table'])->get()->row_array();
        $idMax = intval($arrQueryMax[$tableKey]);
        $idMin = intval($arrQueryMin[$tableKey]);
    
        $this->addHidden('maxIDTrans', $idMax);
        $this->addHidden('minIDTrans', $idMin);
      }
    }
    
    /*
    print_r($this->tabs);
    echo "<br>";
    print_r($this->fieldset);
    */
    if (!empty($this->tabs)) {
      $strResult = '<div class="tabbable"><ul class="nav nav-tabs bordered">';
      $counter = 0;

      //generate tab link
      foreach ($this->tabs AS $id => $tabs) {
        $strTitleTab = ($this->ci->lang->line($tabs['name']) == '') ? $tabs['name'] : $this->ci->lang->line($tabs['name']);

        $strClass = ($counter == 0) ? 'active' : '';
        $strResult .= '<li class="'.$strClass.'"><a href="#tabContent_'.$id.'" id="'.$id.'" data-toggle="tab" rel="tooltip" data-placement="top" data-original-title="" title="" aria-expanded="false">'.$strTitleTab.'</a>';
        $strResult .= '</li>';
        $counter++;
      }
      $strResult .= '</ul>';

      // generate tab content
      $strResult .= '<div class="tab-content padding-10">';
      $counter = 0;
      foreach ($this->tabs AS $id => $tabs) {
        $strClass = ($counter == 0) ? 'active' : '';
        $strResult .= '<div class="tab-pane '.$strClass.'" id="tabContent_'.$id.'">';
        if (isset($tabs['element'])) {
          //$isFieldset = false;
          $hasFieldSet = false;

          $activeFieldSet = '';
          $strElement = '';
          foreach ($tabs['element'] AS $name => $stringElement) {
            if (isset($this->fieldset[$name]) ) {
              $activeFieldSet =  $name;
              $stringElement .= $this->groupElementByFieldset($this->fieldset[$name]);

              // unset tab element within fieldset
              foreach($this->fieldset[$name]['element'] AS $fieldsetElement => $arrFieldsetElement) {
                unset($tabs['element'][$fieldsetElement]);
              }
            }
            else {
              if (isset($this->fieldset[$activeFieldSet]['element'][$name]))
                continue;
            }

            $strElement .= $stringElement;
            unset($this->formObject[$name]);

          }
          $strResult .= (!$hasFieldSet)? '<fieldset>'.$strElement.'</fieldset>' : $strElement;
        }
        $strResult .= '</div>';
        $counter++;
      }
      $strResult .= '</div></div>';
      $this->formObject[$id]['string'] .= $strResult;
    }
    else {
      foreach ($this->fieldset AS $id => $fieldset) {
        $numOfColumn = ($fieldset['column'] > 3) ? 3 : $fieldset['column'];
        $elementCount = (isset($fieldset['element'])) ? count($fieldset['element']) : 0;
        $elementPerRow = ceil($elementCount / $numOfColumn);
        $maxGridCount = 12;
        $rowGridLength = ($maxGridCount / $numOfColumn);

        $counter = 0;

        $strResult = '';
        if (isset($fieldset['element'])) {
          foreach ($fieldset['element'] AS $name => $stringElement) {
            $counter++;
            if ($counter == 1) $strResult .= '<div class="col-sm-'.$rowGridLength.'">';

            $strResult .= $stringElement;

            if ($counter == $elementPerRow) {
              $strResult .= '</div>';
              $counter = 0;
            }
            unset($this->formObject[$name]);
          }
        }
        $this->formObject[$id]['string'] .= $strResult.form_fieldset_close();
      }
    }

    $strObj = '';
    foreach ($this->formObject AS $idObject => $arrObj) {
      $strObj .= ' '.$arrObj['string'];
    }

    $strResult = $strObj;
    return $strResult;
  }

  private function groupElementByFieldset($fieldset) {
    //foreach ($arrFieldset AS $id => $fieldset) {
      $numOfColumn = ($fieldset['column'] > 3) ? 3 : $fieldset['column'];
      $elementCount = (isset($fieldset['element'])) ? count($fieldset['element']) : 0;
      $elementPerRow = ceil($elementCount / $numOfColumn);
      $maxGridCount = 12;
      $rowGridLength = ($maxGridCount / $numOfColumn);

      $counter = 0;

      $strResult = '';
      if (isset($fieldset['element'])) {
        foreach ($fieldset['element'] AS $name => $stringElement) {
          $counter++;
          if ($counter == 1) $strResult .= '<div class="col-sm-'.$rowGridLength.'">';

          $strResult .= $stringElement;

          if ($counter == $elementPerRow) {
            $strResult .= '</div>';
            $counter = 0;
          }
          unset($this->formObject[$name]);
        }
      }
      return $strResult.form_fieldset_close();
    //}
  }

  private function renderFormButton() {
    $strResult = '';

    if (!empty($this->formActionButton)) {
      $strResult .= '<div class="form-actions"><div class="row"><div class="col-sm-12">';

      $strResult .= implode(' ', $this->formActionButton);

      $strResult .= '</div></div></div>';
    }

    return $strResult;
  }

  private function renderHeadingButton() {
    $strResult = '';
    /*
    if ( (empty($this->headingActionButton)) && ($this->hasNavigationHeader) ) {
      $this->addHeadingButton('button', 'form-nav-action-save', '<i class="fa fa-save"></i> Simpan', array('class' => 'form-nav-action-header', 'action-request-type'=>'save', 'title' => 'F10'));
      $this->addHeadingButton('button', 'form-nav-action-delete', '<i class="fa  fa-trash-o"></i> Hapus', array('class' => 'form-nav-action-header', 'action-request-type'=>'delete', 'title' => 'F7'));
      $this->addHeadingButton('button', 'form-nav-action-search', '<i class="fa  fa-search"></i> Cari', array('class' => 'form-nav-action-header', 'action-request-type'=>'search', 'title' => 'F4', 'action-search-url' => site_url($this->navigationHeaderListUrl)));
      $this->addHeadingButton('button', 'form-nav-action-edit', '<i class="fa fa-edit"></i> Ubah', array('class' => 'form-nav-action-header', 'action-request-type'=>'edit', 'title' => 'F11'));
      $this->addHeadingButton('button', 'form-nav-action-new', '<i class="fa fa-file-o"></i> Buat Baru', array('class' => 'form-nav-action-header', 'action-request-type'=>'new', 'title' => 'F9'));
      $this->addHeadingButton('button', 'form-nav-last', '<i class="fa fa-forward"></i> Akhir', array('class' => 'form-nav-header', 'action-request-type'=>'last'));
      $this->addHeadingButton('button', 'form-nav-next', '<i class="fa fa-chevron-right"></i>', array('class' => 'form-nav-header', 'action-request-type'=>'next'));
      $this->addHeadingButton('button', 'form-nav-prev', '<i class="fa fa-chevron-left"></i>', array('class' => 'form-nav-header', 'action-request-type'=>'prev'));
      $this->addHeadingButton('button', 'form-nav-first', '<i class="fa fa-backward"></i> Awal', array('class' => 'form-nav-header','action-request-type'=>'first', 'title' => 'Go to Last Transaction'));        
    }
    */

    if (!empty($this->headingActionButton)) {
      foreach ($this->headingActionButton AS $btn) {
        $strResult .= '<div class="widget-toolbar" role="menu">';
        $strResult .= $btn;
        $strResult .= '</div>';        
      }
    }

    return $strResult;
  }

  // load component js
  private function generateJSString() {
    $strJSstring = '<script type="text/javascript">';
    //added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
    $strJSstring .= "
    jQuery.ajaxSetup({cache:true});";
    //end---added by Dedy, untuk cache javascript yang di created oleh jQuery DOM yang defaultnya selalu di download terus
    $strJSstring .= '
    jQuery(document).ready(function() {';
    $strJSstring .= $this->generateValidationJS();
        
    if (!empty($this->arrDateElement) || !empty($this->arrTimeElement) || !empty($this->arrAutoCompleteElement)  ) {
      $strJSstring .= "
        if (typeof jQuery.ui == 'undefined') {
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = '".base_url()."assets/js/jquery-ui-1.10.3.min.js';
          jQuery('head').append(script);
        }

        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '".base_url()."vendors/base/vendors.bundle.css';
        jQuery('head').append(style);
        ";
    }
    //style.href = '".base_url()."assets/css/smartadmin/smartadmin-production-plugins.min.css';
    
    if (!empty($this->arrSelectElement)) {
      $strJSstring .= $this->_generateLoadJSString(base_url().'assets/js/plugin/select2/select2.min.js');
    }

    if (!empty($this->arrEditorElement)) {
      $strJSstring .= "
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '".base_url()."assets/js/plugin/summernote/summernote.min.js';
        jQuery('head').append(script);

        var style = document.createElement('link');
        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = '".base_url()."assets/css/smartadmin/summernote.css';
        jQuery('head').append(style);";
    }

    foreach ($this->arrDateElement AS $elDate) {

      $arrAdditionalOption = array();
      if (isset($elDate['datePickerOption'])) {
        if (!isset($elDate['datePickerOption']['showOn'])) $elDate['datePickerOption']['showOn'] = "'key'";
        foreach ($elDate['datePickerOption'] AS $key => $val) {
          $arrAdditionalOption[] = $key .":". $val;
        }
      }

      $strJSstring .= "
        jQuery('#".$elDate['name']."_".$this->formID."').datepicker({
          dateFormat: '".$this->dateFormat."',
          yearRange: 'c-50:c+10',
          changeMonth: true,
          changeYear: true,
		      prevText: '<i class=\"fa fa-chevron-left\"></i>',
		      nextText: '<i class=\"fa fa-chevron-right\"></i>',
          ".implode(',', $arrAdditionalOption)."
        });
        $('.ui-datepicker-div').css('zIndex', 999999);

		      ";
    }

    if (!empty($this->arrTimeElement))
      $strJSstring .= "loadScript(\"".base_url()."assets/js/plugin/bootstrap-timepicker/bootstrap-timepicker.min.js\", runTimePicker);";

    $strJSstring .= " function runTimePicker() { ";
    foreach ($this->arrTimeElement AS $elTime) {
      $strJSstring .= "$('#".$elTime."_".$this->formID."').timepicker({ timeFormat: 'HH:mm:ss', showMeridian: false, showSeconds: true});";
    }
    $strJSstring .= " }; ";

    foreach ($this->arrAutoCompleteElement AS $elAutoComplete => $rowAutoComplete) {
      $strGroupingAuJS = '
          _renderItem: function( ul, item ) {
            return $( "<li>" )
              .append( "<a>" + item.value + " : " + item.label + "</a>" )
              .appendTo( ul );
          }
        ';
      if (!empty($rowAutoComplete['groupByField'])) {
        $strGroupingAuJS = '
          _renderMenu: function( ul, items ) {
            var that = this,
              currentCategory = "";
            $.each( items, function( index, item ) {
              var li;
              if ( item.'.$rowAutoComplete['groupByField'].' != currentCategory ) {
                ul.append( "<li class=\'ui-autocomplete-category\'>" + item.'.$rowAutoComplete['groupByField'].' + "</li>" );
                currentCategory = item.'.$rowAutoComplete['groupByField'].';
              }
              li = that._renderItemData( ul, item );
              if ( item.'.$rowAutoComplete['groupByField'].' ) {
                li.attr( "aria-label", item.'.$rowAutoComplete['groupByField'].' + " : " + item.label );
              }
            });
          }
        ';
      }

      $strJSstring .= '
        $.widget( "custom.autocomplete", $.ui.autocomplete, {
          _create: function() {
            this._super();
            this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
          },'.$strGroupingAuJS.'
        });';

      $strJSstring .= $rowAutoComplete['sourceString'];

      $strJSstring .= '
        $( "#'.$elAutoComplete."_".$this->formID.'" ).autocomplete({
          minLength: 0,
          source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
            response( $.grep( '.$rowAutoComplete['source'].', function( data ) {
              return (matcher.test( data.label ) || matcher.test( data.value ) || matcher.test( data ));
            }) );
          },
          focus: function( event, ui ) {
            $( "#'.$elAutoComplete.'" ).val( ui.item.value );
            return false;
          },
          select: function( event, ui ) {
            $( "#label_'.$elAutoComplete.'" ).html( ui.item.label );
            $( "#'.$elAutoComplete.'" ).val( ui.item.value );
          }
        });';
    }

    foreach ($this->arrSelectElement AS $name => $rowProp) {
      if ($rowProp['hasEmptyOption']){
        //$strJSstring .= ' $("#'.$name."_".$this->formID.' option:first-child").remove();';
        $strJSstring .= ' $("#'.$name."_".$this->formID.'").prepend("<option></option>");';
      }

      //$strJSstring .= '$("#'.$name."_".$this->formID.'").select2({allowClear:true, placeholder: "'.$rowProp['placeHolder'].'"});';

      $strJSstringAfter = '';
      if (!empty($rowProp['htmlAfter']))
        $strJSstringAfter = '$("#input-group-'.$name.'-'.$this->formID.'").append("'.str_replace('"',"'",$rowProp['htmlAfter']).'");';

      $strResult = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $strJSstringAfter);
      $strResult = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $strResult);

      $strJSstring .= $strResult;

    }

    foreach ($this->arrEditorElement AS $elDate => $arrEditor) {
      $strJSstring .= "jQuery('#".$arrEditor['id']."').summernote({height: 200});";

      if (isset($arrEditor['code']))
        $strJSstring .= "jQuery('#".$arrEditor['id']."').code('".$arrEditor['code']."');";
    }

    if ($this->ci->session->flashdata($this->formID.'_success_message') != '') {
      $strJSstring .= "jQuery('#".$this->formID."_success_alert').append('".$this->ci->session->flashdata($this->formID.'_success_message')."');";
      $strJSstring .= "jQuery('#".$this->formID."_success_alert').show();";
    }
    else if ($this->ci->session->flashdata($this->formID.'_error_message') != '') {
      $strJSstring .= "jQuery('#".$this->formID."_error_alert').append('".$this->ci->session->flashdata($this->formID.'_error_message')."');";
      $strJSstring .= "jQuery('#".$this->formID."_error_alert').show();";    }

      //javascript untuk navigation header
      if ($this->hasNavigationHeader) {
        $strJSstring .= "
          jQuery(document).keydown(function(e) {
            if (jQuery('#content').hasClass('in-use')) {
              var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
              //F9
              if (key==120) {
                jQuery('#form-nav-action-new_".$this->formID."').click();
              }          
              //F11
              if (key ==122) {
                e.preventDefault();
                jQuery('#form-nav-action-edit_".$this->formID."').click();
              }
              //F4
              if (key ==115) {
                e.preventDefault();
                jQuery('#form-nav-action-search_".$this->formID."').click();
              }
              //F7
              if (key ==118) {
                e.preventDefault();
                jQuery('#form-nav-action-delete_".$this->formID."').click();
              }
              //F10
              if (key==121) {
                e.preventDefault();
                jQuery('#form-nav-action-save_".$this->formID."').click();                
              }
              //F8
              if (key ==119) {
                e.preventDefault();
                jQuery('#form-nav-last_".$this->formID."').click();
              }
            }

          });        
        ";      
      }
    $strJSstring .= '});';

    // javascript untuk detail form input
    if (!empty($this->formDetail)) {
      $strInitJSAfterAdd = '';
      $strInitJSAfterDelete = '';

      foreach ($this->formDetail AS $detailName => $detailProp) {
        if (isset($this->formDetailNewRow[$detailName])) {
          $strJSstring .= 'var dhtml'.$detailName.$this->formID.' = "'.addcslashes(str_replace(array("\r", "\n"), '', trim($this->formDetailNewRow[$detailName])), '"\\/').'";';
        }

        if (!empty($detailProp['strJSAfterAdd'])) {
          $strInitJSAfterAdd = implode(';', $detailProp['strJSAfterAdd']);
        }
        if (!empty($detailProp['strJSAfterDelete'])) {
          $strInitJSAfterDelete = implode(';', $detailProp['strJSAfterDelete']);
        }
      }

      $strJSstring .= "
        function addRowDetail (formName, detailName) {
          var numShow = $('#numShow_'+detailName).val();
          numShow = parseInt(numShow) + 1;

          var tableId = 'table_detail_'+detailName+'_'+formName;
          var strNewRow = eval('dhtml'+detailName+formName).replace(/{{counter}}/gi, numShow);
          $('#'+tableId+' tbody').append(strNewRow);

          $('#counterSequence_'+detailName+'_'+numShow).html(numShow);
          $('#numShow_'+detailName).val(numShow);
          ".$strInitJSAfterAdd."        
        };

        function deleteRowDetail(formName, detailName, idx) {
          var numShow = $('#numShow_'+detailName).val();

          $('#isDeleted_'+detailName+'_'+idx+'_'+formName).val(1);
          $('#rowDetail_'+detailName+'_'+formName+'_'+idx).hide(\"slow\");
          ".$strInitJSAfterDelete." 
        }
      ";
    }
    $strJSstring .= '
    </script>';
    
    return $strJSstring;
  }

  // form validation js
  private function generateValidationJS() {
    $strResult = $this->_generateLoadJSString(base_url().'assets/js/jquery.validate.min.js');
    
    $strResult .= "

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
        ";
    $strResult .= '$("#'.$this->formID.'").validate({
      ignore:[],
      errorClass:"has-error",
      errorClassLabel:"label label-danger",
      errorPlacement: function(error, element) {
        if ( element.attr("type") == "checkbox") {
          if (element.parent("label").hasClass("checkbox-inline"))
            element.closest("div").append(error);
          else
            element.closest("div").parent().append(error);
        } else if (element.attr("type") == "radio") {
          if (element.parent("label").hasClass("radio-inline"))
            element.closest("div").append(error);
          else
            element.closest("div").parent().append(error);
        } else {
          if (element.closest("div.input-group-validation").nextAll("span:first").length == 0)
            error.insertAfter(element.closest("div.input-group-validation"));
          else
            error.insertAfter(element.closest("div.input-group-validation").nextAll("span:first"));
        }
      },
      highlight: function(element, errorClass, validClass) {
        if ( $(element).attr("type") == "checkbox") {
          if ($(element).parent("label").hasClass("checkbox-inline")) {
            $(element).closest("div").addClass(errorClass).removeClass(validClass);
          }
          else {
            $(element).closest("div").parent().addClass(errorClass).removeClass(validClass);
          }
        } else if ( $(element).attr("type") == "radio") {
          if ($(element).parent("label").hasClass("radio-inline")) {
            $(element).closest("div").addClass(errorClass).removeClass(validClass);
          }
          else {
            $(element).closest("div").parent().addClass(errorClass).removeClass(validClass);
          }
        }else {
          $(element).closest("div.input-group-validation").addClass(errorClass).removeClass(validClass);
        }
      },
      unhighlight: function(element, errorClass, validClass) {
        if ( $(element).attr("type") == "checkbox") {
          if ($(element).parent("label").hasClass("checkbox-inline")) {
            $(element).closest("div").addClass(validClass).removeClass(errorClass);
          }
          else {
            $(element).closest("div").parent().addClass(validClass).removeClass(errorClass);
          }
        } else if ( $(element).attr("type") == "radio") {
          if ($(element).parent("label").hasClass("radio-inline")) {
            $(element).closest("div").addClass(validClass).removeClass(errorClass);
          }
          else {
            $(element).closest("div").parent().addClass(validClass).removeClass(errorClass);
          }
        }else {
          $(element).closest("div.input-group-validation").addClass(validClass).removeClass(errorClass);
        }
      },';

    $arrRules = array();
    $arrMessages = array();

    foreach ($this->validationRules AS $idElement => $arrElementRules) {
      $tempRule = json_encode($arrElementRules);
      $strRule  = preg_replace('/"([a-zA-Z_]+[a-zA-Z0-9_]*)":/','$1:',$tempRule);
      $strRule  = preg_replace('/"(function[^"]*)"/','${1}',$strRule);
      $arrRules[] = $idElement.' : '.$strRule;

    }
    foreach ($this->validationMessages AS $idElement => $arrElementMessages) {
      $arrMessages[] = $idElement.' : '.json_encode($arrElementMessages);
    }

    if (!empty($arrRules))
      $strResult .= 'rules:{'.implode(",", $arrRules).'},';
    if (!empty($arrMessages))
      $strResult .= 'messages:{'.implode(",", $arrMessages).'},';

    $strResult .= '
      submitHandler: function(form) {
        var moduleName = "";
        var controllerName = "";
        var methodName = "";

        var submitButton = $("input[name=submitButton_'.$this->formID.']").val();
        var submitButtonText = $("#"+submitButton+"_'.$this->formID.'").val();';

    if (!empty($this->arrEditorElement)) {
      foreach ($this->arrEditorElement AS $elDate => $arrEditor) {
        $strResult .= "jQuery('#".$arrEditor['hiddenEl']."_".$this->formID."').val(jQuery('#".$arrEditor['id']."').code());";
      }
    }

/*
        alertify.confirm(submitButtonText + " this data ?", function (ok) {
          if (ok) {
            var submitAction = $("input[name=submitAction_'.$this->formID.']").val();
            var actionSegment = submitAction.split("/");
            actionSegment.reverse();

            if (actionSegment.hasOwnProperty(0)) methodName = actionSegment[0];
            if (actionSegment.hasOwnProperty(1)) controllerName = actionSegment[1];
            else controllerName = "'.$this->controller_name.'";
            if (actionSegment.hasOwnProperty(2)) moduleName = actionSegment[2];
            else moduleName = "'.$this->module.'";
            var arrSubmitUrl = [moduleName, controllerName, methodName];
            var submitUrl = arrSubmitUrl.join("/");

            $("#'.$this->formID.'").attr("action", "'.base_url().'"+submitUrl);
            form.submit();
          }
        });
*/
        $strResult .= '
//         if (confirm(submitButtonText + " this data ?")) {
            var submitAction = $("input[name=submitAction_'.$this->formID.']").val();
            var actionSegment = submitAction.split("/");
            actionSegment.reverse();

            if (actionSegment.hasOwnProperty(0)) methodName = actionSegment[0];
            if (actionSegment.hasOwnProperty(1)) controllerName = actionSegment[1];
            else controllerName = "'.$this->controller_name.'";
            if (actionSegment.hasOwnProperty(2)) moduleName = actionSegment[2];
            else moduleName = "'.$this->module.'";
            var arrSubmitUrl = [];//[moduleName, controllerName, methodName];
            if (moduleName != "") arrSubmitUrl.push(moduleName);
            if (controllerName != "") arrSubmitUrl.push(controllerName);
            if (methodName != "") arrSubmitUrl.push(methodName);
            var submitUrl = arrSubmitUrl.join("/");

            $("#'.$this->formID.'").attr("action", "'.base_url().'"+submitUrl);
            form.submit();
//           }
      }
    ';
    $strResult .= '});';

    return $strResult;
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
}
