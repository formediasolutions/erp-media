<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// FPDF lib
include( APPPATH . 'libraries/fpdf-1.8.1/fpdf.php' );

// FPDI lib
include( APPPATH . 'libraries/fpdi-1.6.1/fpdi.php' );

/**
 * bgn_pdf class.
 *
 * @class bgn_pdf
 * @author Iqbal
 */
class Erp_Pdf
{
	var $CI;
	var $qualified;
	
	// simple export
	var $companyName;
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
	
	// ---------------------------------------------------------------------------
	
	function tax_export( $data, $outputType = 'D' ) {
		if ( ! is_array( $data ) )
			return false;
            
		$template = APPPATH . 'libraries/export_templates/1721_VI.pdf';
		$currentTime = time();
		$tempDir = $outputType == 'F' ? 'assets/export/' : '';

		if ( count( $data ) == 1 ) {
			$tax = $data[0];
			
			$this->title = 'Laporan Pajak Periode ' . $tax->period_name;
			$this->tempFile = $tempDir . 'Laporan_Pajak_1721_VI_' . $tax->period . '_' . $tax->username . '_' . date( 'YmdHis', $currentTime ) . '.pdf'; // relative to index.php of CI
		} else {
			$this->title = 'Laporan Pajak';
			$this->tempFile = $tempDir . 'Laporan_Pajak_1721_VI_' . '_' . date( 'YmdHis', $currentTime ) . '.pdf'; // relative to index.php of CI
		}

		// initiate FPDI 
		$pdf = new FPDI( 'P', 'pt' );

		foreach( $data as $idx => $tax ) {
		
			$tax_base = $tax->total_nominal;
			$tax_percentage = round( $tax->total_tax / $tax_base * 100, 2 );
			if ( $tax_percentage >= 6 ) 
				$tax_percentage = 3;
			elseif ( $tax_percentage >= 5)
				$tax_percentage = 2.5;
			elseif ( $tax_percentage >= 3 )
				$tax_percentage = 3;
			else
				$tax_percentage = 2.5;
                
            $tax_percentage = !empty($tax->npwp) ? 2.5 : 3;

			// address
			$address = $tax->address . ' ' . $tax->city;

			// ----------------------------------------------
			// load template
			// ----------------------------------------------
            
			// add a page 
			$pdf->AddPage(); 
			// set the sourcefile 
			$pdf->setSourceFile( $template );
			// import page 1 
			$tplIdx = $pdf->importPage( 1 ); 
			// use the imported page as the template 
			$pdf->useTemplate( $tplIdx );

			// ----------------------------------------------
			// edit template
			// ----------------------------------------------
			// now write some text above the imported page 
			$pdf->SetFont( 'Arial' );
			$pdf->SetFontSize( 9 );
			
			// set A. NOMOR
			$pdf->SetY( 103 );
			$pdf->SetX( 318 ); $pdf->Write( 0, str_pad( $tax->period_month, 2, '0', STR_PAD_LEFT ) );
			$pdf->SetX( 350 ); $pdf->Write( 0, substr( $tax->period_year, 1, 3 ) );
			$pdf->SetX( 385 ); $pdf->Write( 0, str_pad( $tax->sequence_num, 7, '0', STR_PAD_LEFT ) );
			
			// set A. NPWP, NIK
			$pdf->SetY( 143 );
			if ( $npwp = $this->_npwp_extract( $tax->npwp ) ) {
				$pdf->SetX( 160 ); $pdf->Write( 0, $npwp[0] );
				$pdf->SetX( 268 ); $pdf->Write( 0, $npwp[1] );
				$pdf->SetX( 304 ); $pdf->Write( 0, $npwp[2] );
			}
			$pdf->SetX( 439 ); $pdf->Write( 0, substr( $tax->idcard, 0, 16 ) );
			
			// set A. NAMA
			$pdf->SetY( 158 );
			$pdf->SetX( 160 ); $pdf->Write( 0, $tax->name );
			
			// set A. ALAMAT
			$length = 60; // allow max 55 chars for address
			$pdf->SetY( 173 );
			$address1 = substr( $address, 0, $length );
			$address2 = str_replace( $address1, '', $address );
			$pdf->SetX( 160 ); $pdf->Write( 0, $address1 . ( $address2 ? '-' : '' ) );
			
			if ( $address2 ) {
				$address2 = substr( $address2, 0, $length );
				$pdf->SetY( 187 );
				$pdf->SetX( 160 ); $pdf->Write( 0, $address2 );
			}
			
			// set B
			$pdf->SetY( 286 );
			$pdf->SetX( 202 ); $pdf->Write( 0, bgn_accounting( $tax_base ) );
			$pdf->SetX( 284 ); $pdf->Write( 0, bgn_accounting( $tax_base ) );
			$pdf->SetX( 376 ); $pdf->Write( 0, ( $npwp ? '' : 'X' ) );
			$pdf->SetX( 414 ); $pdf->Write( 0, $tax_percentage );
			$pdf->SetX( 458 ); $pdf->Write( 0, bgn_accounting( $tax->total_tax ) );
			
			// set C
            $sign_month = date( 'm', strtotime( $tax->period_year .'-'.$tax->period_month . ' + 1 months' ) );
            $sign_year  = date( 'Y', strtotime( $tax->period_year .'-'.$tax->period_month . ' + 1 months' ) );
            
			$pdf->SetY( 344 );
			$pdf->SetX( 333 ); $pdf->Write( 0, '10' );
			$pdf->SetX( 366 ); $pdf->Write( 0, str_pad( $sign_month, 2, '0', STR_PAD_LEFT ) );
			$pdf->SetX( 399 ); $pdf->Write( 0, $sign_year );
		}

		$pdf->Output( $this->tempFile, $outputType );
		
		return base_url( 'download?f=' . $this->tempFile );
	}

	// ---------------------------------------------------------------------------
	
	private function _npwp_extract( $npwp ) {
		if ( empty( $npwp ) )
			return false;
		
		$npwp = explode( '-', $npwp );
		if ( isset( $npwp[1] ) ) {
			$_last = explode( '.', $npwp[1] );
			unset( $npwp[1] );
			$npwp = array_merge( $npwp, $_last );
		}
		
		return $npwp;
	}
}