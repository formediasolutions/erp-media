<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "libraries/xlsxwriter.class.php";
require_once APPPATH . "/libraries/phpexcel/PHPExcel.php";

/**
 * BKEV Global Netowork PHPExcel class.
 *
 * @class bgn_excel
 * @author Iqbal
 */
class Erp_Excel extends PHPExcel
{
	var $CI;
	var $companyName;
	var $simpleExcel;
	var $objPHPExcel;
	var $objReader;
	var $objWriter;
	var $worksheet;
	var $tempFile;
	var $title;
	var $subTitle;
	var $heading;
	var $exportDate;
	var $data;

	/**
	 * Constructor - Sets up the object properties.
	 */
	function __construct()
	{
		$this->CI =& get_instance();
        $this->companyName = COMPANY_NAME;
	}
    
    /**
	 * All styles settings
	 * @author	Iqbal
	 */
	var $styleBorderThin = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
			),
		),
	);
	
	var $styleOutsideBorderThick = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
			),
		),
	);
    
    /**
	 * Simple exporter Set Header
	 * @author	Iqbal
	 */
	function setHeader($content_type, $filename) {		
		ob_end_clean();
		
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: ' . $content_type);
		header('Content-Disposition: attachment;filename="' . $filename . '"');
	}
    
    /**
	 * Set file properties
	 * @author	Iqbal
	 */
	function setProperties() {
		// Set document properties
        $this->objPHPExcel->getProperties()
            ->setCreator($this->companyName)
            ->setLastModifiedBy($this->companyName)
            ->setTitle($this->title)
            ->setSubject($this->title)
            ->setDescription($this->title)
            ->setKeywords($this->title)
            ->setCategory($this->title);
	}
    
    /**
	 * Init simple exporter
     * @author	Iqbal
	 */
	function simpleInit() {
		$this->simpleExcel    = new XLSXWriter();
		
		$currentTime          = time();
		$this->exportDate     = date('d F Y', $currentTime);
		$this->tempFile       = 'assets/export/' . str_replace(' ', '_', $this->title) . '_' . date('YmdHis', $currentTime) . '.xlsx';
		
		// Set table header
		array_unshift($this->data, $this->heading);
		// Set export date
		array_unshift($this->data, array('Tanggal Export : ' . $this->exportDate));
		// Set subtitle
		array_unshift($this->data, array($this->subTitle));
		// Set main title
        array_unshift($this->data, array($this->title . ' ' . $this->companyName));
		
		// Start writing data to worksheet
		$this->simpleExcel->writeSheet($this->data, $this->title);
		// Save as temporaryfile - raw excel - without any styling
        $this->simpleExcel->writeToFile($this->tempFile);
		
		// Load file using PHP excel then modif the style
		$this->objReader      = new PHPExcel_Reader_Excel2007();
		$this->objPHPExcel    = $this->objReader->load( $this->tempFile );
		
		// Setup properties
		$this->setProperties();
		
		$this->objPHPExcel->setActiveSheetIndex(0);
		$this->worksheet      = $this->objPHPExcel->getActiveSheet();
	}
    
    /**
	 * Output simple exporter to file
	 */
	function simpleOutput($save=true) {
		$this->objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);
        $this->objWriter->setPreCalculateFormulas(false);
		
		$filename = str_replace(' ', '_', $this->title) . date('YmdHis') . '.xlsx';
		
		if (!$save) {
			$this->setHeader('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $filename);
			$this->objWriter->save('php://output');
		} else {
			$this->objWriter->save($this->tempFile);
		}
		
		// Clean up objects
		$this->objPHPExcel->disconnectWorksheets();
		unset($this->objPHPExcel);
		
		return base_url($this->tempFile);
	}
	
	/**
	 * Export withdraw list - Admin 
	 */
	function withdraw_export_simple($data=array()) {
		// setup necessary information
		$this->title 	= 'Laporan Data Withdraw ';
		$this->heading 	= array(
            'No', 
            'Username', 
            'Nama Member', 
            'Bank', 
            'No. Rekening', 
            'Nama Pemilik Rekening', 
            'Jumlah Withdraw', 
			'Biaya Transfer',  
            'Jumlah Transfer', 
            'Status', 
            'Tanggal'
        );
		$this->data		= array();
        
        // complete subtitle
        $this->subTitle = 'Tanggal Withdraw : ' . date('d M, Y');

		// set data
		$no=1; $total=0;
        if( !empty($data) ){
            foreach($data as $row) {
    			if ($no==1) $this->subTitle = date('d M, Y', strtotime($row->datecreated)); // since the export data is datecreated DESC
    			$bank = bgn_banks($row->bank);
                
    			$this->data[] = array(
    				$no++ . '.',
    				$row->username,
    				$row->name,
    				strtoupper($bank->nama),
    				"'".$row->bill,
    				strtoupper($row->bill_name),
    				$row->nominal,
    				$row->transfer_fund,
    				$row->nominal_receipt,
    				( $row->status == 0 ? 'PENDING' : 'TRANSFERED' ),
    				date('d M, Y', strtotime($row->datecreated))
    			);
    		}
            
            // add 3 new rows
    		$this->data[] = array();
    		$this->data[] = array();
    		$this->data[] = array();
            
            // complete subtitle
            $this->subTitle = 'Tanggal Withdraw : ' . date('d M, Y', strtotime($row->datecreated)) . ' s/d ' . $this->subTitle;
        }else{
            $this->data[] = array('Belum Tersedia Data Withdraw');
        }
		
		// init simple export
		$this->simpleInit();
		
		$rowNumber = count($this->data);
        
        // styling excel file
        $styleArray = array(
            'alignment' => array(
                'wrap'          => true,
                'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'      => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '428BCA')
            ),
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'),
                'bold'  => true,
            ),
        );
        $this->worksheet->getStyle('A4:K4')->applyFromArray($styleArray);
        $this->worksheet->getRowDimension('1')->setRowHeight(15);
        $this->worksheet->getRowDimension('2')->setRowHeight(15);
        $this->worksheet->getRowDimension('3')->setRowHeight(15);
        $this->worksheet->getRowDimension('4')->setRowHeight(15);
        $this->worksheet->getStyle('A1')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A2')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A3')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->worksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->worksheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
        if( !empty($data) ){
    		// write formula
    		$this->worksheet->setCellValue('G' . $rowNumber, '=SUM(G5:G' . ($rowNumber-1) . ')');
    		$this->worksheet->setCellValue('H' . $rowNumber, '=SUM(H5:H' . ($rowNumber-1) . ')');
    		$this->worksheet->setCellValue('I' . $rowNumber, '=SUM(I5:I' . ($rowNumber-1) . ')');
    		$this->worksheet->setCellValue('J' . $rowNumber, '=SUM(J5:J' . ($rowNumber-1) . ')');
        }else{
            $this->worksheet->getRowDimension('5')->setRowHeight(25);
            $this->worksheet->getStyle('A5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->worksheet->mergeCells('A5:K5');
            $this->worksheet->getStyle('A4:K5')->applyFromArray($this->styleBorderThin);
        }
        
        // styling excel file
		$this->worksheet->getStyle('A4:K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('A5:B' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('D5:E' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('K5:K' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('G5:J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$this->worksheet->getStyle('G5:J' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
		$this->worksheet->getStyle('A4:K' . $rowNumber)->applyFromArray($this->styleBorderThin);
		
		$this->worksheet->getColumnDimension('A')->setWidth(5);
		$this->worksheet->getColumnDimension('B')->setWidth(15);
        $this->worksheet->getColumnDimension('C')->setWidth(35);
		$this->worksheet->getColumnDimension('D')->setWidth(25);
		$this->worksheet->getColumnDimension('E')->setWidth(35);
		$this->worksheet->getColumnDimension('F')->setWidth(35);
		$this->worksheet->getColumnDimension('G')->setWidth(25);
		$this->worksheet->getColumnDimension('H')->setWidth(25);
		$this->worksheet->getColumnDimension('I')->setWidth(25);
		$this->worksheet->getColumnDimension('J')->setWidth(25);
		$this->worksheet->getColumnDimension('K')->setWidth(15);

		// output to user browser
		return $this->simpleOutput();
	}
    
    // ---------------------------------------------------------------------------
	
	function tax_export( $data ) {
		// setup necessary information
		$this->title 	= 'Laporan Pajak';
		$this->heading	= array(
            'No', 
            'Periode', 
            'Nama', 
            'Username', 
            'Alamat', 
            'Kota', 
            'NPWP', 
            'Jumlah Bonus', 
            '% Pajak', 
            'Jumlah Pajak', 
            'Jumlah Diterima'
        );
		$this->data		= array();
        
        // complete subtitle
        $this->subTitle = 'Tanggal Export : ' . date('d M, Y');
		
		// set data
		$no=1;
        if( !empty($data) ){
            foreach($data as $row) {
    			$tax_base = $row->total_nominal;
    			$tax_percentage = round( $row->total_tax / $tax_base * 100, 2 );
    			if ( $tax_percentage >= 6 ) 
    				$tax_percentage = 3;
    			elseif ( $tax_percentage >= 5)
    				$tax_percentage = 2.5;
    			elseif ( $tax_percentage >= 3 )
    				$tax_percentage = 3;
    			else
    				$tax_percentage = 2.5;
    			
    			$this->data[] = array(
    				$no++ . '.',
    				$row->period_name,
    				$row->name,
    				$row->username,
    				$row->address,
    				$row->city,
    				$row->npwp,
    				$row->total_nominal,
    				$tax_percentage . '%',
    				$row->total_tax,
    				$row->total_received
    			);
    		}
            
            // add 3 new rows
    		$this->data[] = array();
    		$this->data[] = array();
    		$this->data[] = array();
        }else{
            $this->data[] = array('Belum Tersedia Data Withdraw');
        }

		// init simple export
		$this->simpleInit();
		
		$rowNumber = count($this->data);
        
        // styling excel file
        $styleArray = array(
            'alignment' => array(
                'wrap'          => true,
                'horizontal'    => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'      => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '428BCA')
            ),
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'),
                'bold'  => true,
            ),
        );
        $this->worksheet->getStyle('A4:K4')->applyFromArray($styleArray);
        $this->worksheet->getRowDimension('1')->setRowHeight(15);
        $this->worksheet->getRowDimension('2')->setRowHeight(15);
        $this->worksheet->getRowDimension('3')->setRowHeight(15);
        $this->worksheet->getRowDimension('4')->setRowHeight(15);
        $this->worksheet->getStyle('A1')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A2')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A3')->getFont()->setSize(10)->setBold(true);
        $this->worksheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->worksheet->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->worksheet->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
        if( !empty($data) ){
            // write formula
    		$this->worksheet->setCellValue('H' . $rowNumber, '=SUM(H5:H' . ($rowNumber-1) . ')');
    		$this->worksheet->setCellValue('J' . $rowNumber, '=SUM(J5:J' . ($rowNumber-1) . ')');
    		$this->worksheet->setCellValue('K' . $rowNumber, '=SUM(K5:K' . ($rowNumber-1) . ')');
        }else{
            $this->worksheet->getRowDimension('5')->setRowHeight(25);
            $this->worksheet->getStyle('A4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->worksheet->mergeCells('A4:K4');
            $this->worksheet->getStyle('A3:K4')->applyFromArray($this->styleBorderThin);
        }

		// styling excel file
		$this->worksheet->getStyle('A4:K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('A5:D' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('G5:G' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('I5:I' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->worksheet->getStyle('H5:H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
		$this->worksheet->getStyle('J5:K' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0');
		// set border
		$this->worksheet->getStyle('A4:K' . $rowNumber)->applyFromArray($this->styleBorderThin);
		
		$this->worksheet->getColumnDimension('A')->setWidth(5);
		$this->worksheet->getColumnDimension('B')->setWidth(10);
		$this->worksheet->getColumnDimension('C')->setWidth(25);
		$this->worksheet->getColumnDimension('D')->setWidth(25);
		$this->worksheet->getColumnDimension('E')->setWidth(50);
		$this->worksheet->getColumnDimension('F')->setWidth(30);
		$this->worksheet->getColumnDimension('G')->setWidth(25);
		$this->worksheet->getColumnDimension('H')->setWidth(25);
		$this->worksheet->getColumnDimension('I')->setWidth(10);
		$this->worksheet->getColumnDimension('J')->setWidth(25);
		$this->worksheet->getColumnDimension('K')->setWidth(25);
		
		// output to user browser
		return $this->simpleOutput();
	}
}

/*
CHANGELOG
---------
Insert new changelog at the top of the list.
-----------------------------------------------
Version	YYYY/MM/DD  Person Name		Description
-----------------------------------------------
1.0.0   2014/12/12  Iqbal           - Created this changelog
*/