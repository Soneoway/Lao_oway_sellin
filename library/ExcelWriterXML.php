<?php
/**
 * ExcelWriterXML Package
 * Used the schema documentation from Microsoft
 * @link http://msdn.microsoft.com/en-us/library/aa140066(office.10).aspx
 * @package ExcelWriterXML
 */

/**
 * Class for generating the initial Excel XML document
 * <code>
 * <?php
 * $xml = new ExcelWriterXML;
 * $format = $xml->addStyle('StyleHeader');
 * $format->fontBold();
 * $sheet = $xml->addSheet('Test Sheet');
 * $sheet->writeString (1,1,'Header1','StyleHeader');
 * $sheet->writeString(2,1,'My String');
 * $xml->sendHeaders();
 * $xml->writeData();
 * ?>
 * </code>
 * @link http://msdn.microsoft.com/en-us/library/aa140066(office.10).aspx
 * @author Robert F Greer
 * @editor Thong Duong
 * @version 1.0
 * @package ExcelWriterXML
 */
class ExcelWriterXML{
	// Private Variables //
	private $styles = array();
	public  $formatErrors = array();
	private $sheets = array();
	private $showErrorSheet = false;
	private $overwriteFile = false;
	private $docFileName;
	private $docTitle;
	private $docSubject;
	private $docAuthor;
	private $docCreated;
	private $docManager;
	private $docCompany;
	private $docVersion = 11.9999;
	///////////////////////

	/**
     * Constructor for the ExcelWriterXML class.
     * A default style is created, a filename is generated (if not supplied) and
     * the create time of the document is stored.
     * @param string $fileName This is the filename that will be passed to the
     * browser.  If not present it will default to "file.xml"
     * @return ExcelWriterXML Instance of the class
     */
	function ExcelWriterXML($fileName = 'file.xml'){
		// Add default style
		$style = $this->addStyle('Default');
		$style->name('Normal');
		$style->alignVertical('Bottom');
		
		if ($fileName == ''){
			$fileName = 'file.xml';
			$this->addError(__FUNCTION__,'File name was blank, default to "file.xml"');
		}
		
		$this->docFileName = $fileName;
		$this->docCreated = date('Y-m-d').'T'.date('H:i:s').'Z';
		EWXcreateStylesDB($this);
	}

	/**
	 * Whether or not to overwrite a file (when writing to disk)
	 * @param boolean $overwrite True or False
	 */
	public function overwriteFile($overwrite = true){
		if (!is_bool($overwrite)){
			$this->overwriteFile = false;
			return;
		}
		else{
			$this->overwriteFile = $overwrite;
		}
	}

	/**
	 * Whether or not to show the sheet containing the Formatting Errors
	 * @param boolean $show
	 */
	public function showErrorSheet($show = true){
		if (!is_bool($show)){
			$this->showErrorSheet = true;
			return;
		}
		else{
			$this->showErrorSheet = $show;
		}
	}

	/**
	 * Adds a format error.  When the document is generated if there are any
	 * errors they will be listed on a seperate sheet.
@param string $function The name of the function that was called
	 * @param string $message Details of the error
	 */
	public function addError($function, $message){
		$tmp = array(
			'function'	=> $function,
			'message'	=> $message,
		);
		$this->formatErrors[] = $tmp;
	}
	
	/**
     * Sends the HTML headers to the client.
     * This is only necessary if the XML doc is to be delivered from the server
     * to the browser.
     */
	public function sendHeaders(){
		header('content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$this->docFileName.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
		header('Pragma: public');
	}

	/**
     * Gets the default style that was created by the contructor.
     * This is used when modifications to the default style are required.
     * @return ExcelWriterXML_Style Reference to a style class
     */
	public function getDefaultStyle(){
		return($this->styles[0]);
	}

	/**
     * Creates a new style within the spreadsheet.
     * Styles cannot have the same name as any other style. If a style has the
     * same name as another style then it will follow the default naming
     * convention as if $id was null
     * @param string $id The name of the style.  If left blank then the style
     * will default to "CustomStyle" + n (e.g. "CustomStyle1")
     * @return ExcelWriterXML_Style Reference to a new style class
     */
	public function addStyle($id = null){
		static $styleNum = 1;
		if (trim($id) == '') $id = null;

		if ($id == null){
			$id = 'CustomStyle'.$styleNum;
			$styleNum++;
			//$this->addError(__FUNCTION__,'Style name was blank, renamed to "'.$id.'"');
		}

		while (!$this->checkStyleID($id)){
			$old_id = $id;
			$id = 'CustomStyle'.$styleNum;
			$this->addError(__FUNCTION__,'Style name was duplicate ("'.$old_id.'"), renamed to "'.$id.'"');
			$styleNum++;
		}
		
		$style =& new ExcelWriterXML_Style($id);
		$this->styles[] = $style;
		return ($style);
	}
	
	/**
     * Creates a new sheet within the spreadsheet
     * At least one sheet is required.
     * Additional sheets cannot have the same name as any other sheet.
     * If a sheet has the same name as another sheet then it will follow the
     * default naming convention as if $id was null
     * @param string $id The name of the sheet.  If left blank then the sheet
     * will default to "Sheet" + n (e.g. "Sheet1")
     * @return ExcelWriterXML_Sheet Reference to a new sheet class
     */
	public function addSheet($id = null){
		static $sheetNum = 1;
		if (trim($id) == '') $id = null;

		if ($id == null){
			$id = 'Sheet'.$sheetNum;
			$sheetNum++;
			$this->addError(__FUNCTION__,'Sheet name was blank, renamed to "'.$id.'"');
		}

		while (!$this->checkSheetID($id)){
			$old_id = $id;
			$id = 'Sheet'.$sheetNum;
			$this->addError(__FUNCTION__,'Sheet name was duplicate ("'.$old_id.'"), renamed to "'.$id.'"');
			$sheetNum++;
		}
		
		$sheet =& new ExcelWriterXML_Sheet($id);
		$this->sheets[] = $sheet;
		return ($sheet);
	}
	
	/**
	 * Checks whether a proposed Sheet ID has already been used
	 * @param string $id The sheet id to be checked
	 * @return boolean True if the id is unique, false otherwise
	 */
	private function checkSheetID($id){
		foreach($this->sheets as $sheet){
			$sheetID = $sheet->getID();
			if ($id == $sheetID){
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks whether a proposed Style ID has already been used
	 * @param string $id The style id to be checked
	 * @return boolean True if the id is unique, false otherwise
	 */
	public function checkStyleID($id){
		foreach($this->styles as $style){
			$styleID = $style->getID();
			if ($id == $styleID){
				return false;
			}
		}
		return true;
	}

	/**
     * Writes the XML data
     * @param string $target If left null the function will output to STD OUT
     * (e. g. browser or console)
     */
	public function writeData($target = null){
		$docTitle = '';
		$docSubject = '';
		$docAuthor = '';
		$docCreated = '';
		$docManager = '';
		$docCompany = '';
		$docVersion = 12;
		
		$errors = false;
		
		if ($this->showErrorSheet == true){
			$format = $this->addStyle('formatErrorsHeader');
			$format->fontBold();
			$format->bgColor('red');
		}
		
		if (!empty($this->docTitle)) $docTitle = '<Title>'.htmlspecialchars($this->docTitle).'</Title>'."\r";
		if (!empty($this->docSubject)) $docSubject = '<Subject>'.htmlspecialchars($this->docSubject).'</Subject>'."\r";
		if (!empty($this->docAuthor)) $docAuthor = '<Author>'.htmlspecialchars($this->docAuthor).'</Author>'."\r";
		if (!empty($this->docCreated)) $docCreated = '<Created>'.htmlspecialchars($this->docCreated).'</Created>'."\r";
		if (!empty($this->docManager)) $docManager = '<Manager>'.htmlspecialchars($this->docManager).'</Manager>'."\r";
		if (!empty($this->docCompany)) $docCompany = '<Company>'.htmlspecialchars($this->docCompany).'</Company>'."\r";
		
		$xml = '<?xml version="1.0"?>'."\r";
		$xml .= '<?mso-application progid="Excel.Sheet"?>'."\r";
		$xml .= '<Workbook
			xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			xmlns:o="urn:schemas-microsoft-com:office:office"
			xmlns:x="urn:schemas-microsoft-com:office:excel"
			xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
			xmlns:html="http://www.w3.org/TR/REC-html40">'."\r";
		$xml .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">'."\r";
			if (!empty($this->docTitle))	$xml .= '	'.$docTitle;
			if (!empty($this->docSubject))	$xml .= '	'.$docSubject;
			if (!empty($this->docAuthor))	$xml .= '	'.$docAuthor;
			if (!empty($this->docCreated))	$xml .= '	'.$docCreated;
			if (!empty($this->docManager))	$xml .= '	'.$docManager;
			if (!empty($this->docCompany))	$xml .= '	'.$docCompany;
			$xml .= '	<Version>'.$this->docVersion.'</Version>'."\r";
		$xml .= '</DocumentProperties>'."\r";
		$xml .= '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel" />'."\r";
		$xml .= '<Styles>'."\r";
		foreach($this->styles as $style){
			$xml .= $style->getStyleXML();
			if (count($style->getErrors()) > 0){
				$errors = true;
			}
		}
		$xml .= '</Styles>'."\r";
		if (count($this->sheets) == 0){
			$this->addSheet();
		}
		foreach($this->sheets as $sheet){
			$xml .= $sheet->getSheetXML();
			if (count($sheet->getErrors()) > 0){
				$errors = true;
			}
		}
		if (count($this->formatErrors) > 0){
			$errors = true;
		}
		
		if ($errors == true && $this->showErrorSheet == true){
			$sheet = $this->addSheet('formatErrors');
			$sheet->cellMerge(1,1,3,0);	// Merge the first three cells across in row 1
			$sheet->writeString(1,1,'Formatting Errors');
			$sheet->writeString(2,1,'Type','formatErrorsHeader');
			$sheet->writeString(2,2,'Function','formatErrorsHeader');
			$sheet->cellWidth(2,1,200);
			$sheet->cellWidth(2,2,200);
			$sheet->cellWidth(2,3,400);
			$sheet->writeString(2,3,'Error Message','formatErrorsHeader');
			$row = 3;
			foreach($this->formatErrors as $error){
				$function = $error['function'];
				$message = $error['message'];
				$sheet->writeString($row,1,'Document');
				$sheet->writeString($row,2,$function);
				$sheet->writeString($row,3,$message);
				$row++;
			}
			foreach($this->styles as $styleObject){
				$formatErrors = $styleObject->getErrors();
				$styleID = 'Style='.$styleObject->getID();
				foreach($formatErrors as $error){
					$function = $error['function'];
					$message = $error['message'];
					$sheet->writeString($row,1,$styleID);
					$sheet->writeString($row,2,$function);
					$sheet->writeString($row,3,$message);
					$row++;
				}
			}
			foreach($this->sheets as $sheetObject){
				$formatErrors = $sheetObject->getErrors();
				$sheetID = 'Sheet='.$sheetObject->getID();
				foreach($formatErrors as $error){
					$function = $error['function'];
					$message = $error['message'];
					$sheet->writeString($row,1,$sheetID);
					$sheet->writeString($row,2,$function);
					$sheet->writeString($row,3,$message);
					$row++;
				}
			}
			$xml .= $sheet->getSheetXML();
		}
		
		
		$xml .= '</Workbook>';
		
		if ($target == null){
			// We aren't writing this file to disk, so echo back to the client.
			echo $xml;
			return true;
		}
		else{
			$fileExists = file_exists($target);
			if ($fileExists == true && $this->overwriteFile == false){
				die('"'.$target.'" exists and "overwriteFile" is set to "false"');
			}
			$handle = fopen($target, 'w');
			if ($handle){
				fwrite($handle,$xml);
				fclose($handle);
				return true;
			}
			else{
				echo('<br/>Not able to open "'.$target.'" for writing');
				return false;
			}
		}
	}
	
	/**
     * Sets the Title of the document
     * @param string $title Part of the properties of the document.
     */
	public function docTitle($title = ''){$this->docTitle = $title;}

	/**
     * Sets the Subject of the document
     * @param string $subject Part of the properties of the document.
     */
	public function docSubject($subject = ''){$this->docSubject = $subject;}

	/**
     * Sets the Author of the document
     * @param string $author Part of the properties of the document.
     */
	public function docAuthor($author = ''){$this->docAuthor = $author;}

	/**
     * Sets the Manager of the document
     * @param string $manager Part of the properties of the document.
     */
	public function docManager($manager = ''){$this->docManager = $manager;}

	/**
     * Sets the Company of the document
     * @param string $company Part of the properties of the document.
     */
	public function docCompany($company = ''){$this->docCompany = $company;}
	
	/**
     * Outputs a MYSQL table or list of tables to an Excel doc
     * @param string $host MySQL host to connect to
     * @param string $username Username to connect with
     * @param string $password Password to connect with
     * @param string $db Database to use
     * @param mixed $table If string, out specific table.  If array, each table will have it's own sheet
     * @param mixed $alternateName For multiple tables this does nothing.  For table, overrides auto naming of the sheet (table name)
     */
	public function mysqlTableDump($host,$username,$password,$db,$table,$alternateName = null){
		EWXcheckDriverDB('mysql');
		if (empty($host))		$this->addError('Database','HOSTNAME is empty');
		if (empty($username))	$this->addError('Database','USERNAME is empty');
		if (empty($db))			$this->addError('Database','DB is empty');
		if (empty($table))		$this->addError('Database','TABLE(S) is empty');
		if (count($this->formatErrors) > 0){$this->showErrorSheet();return false;}
		
		$link = mysql_connect($host,$username,$password);
		if (!$link) $this->addError('Database','UNABLE to connect to '.$host.'('.mysql_error().')');
		if (count($this->formatErrors) > 0){ $this->showErrorSheet(); return false; }
		
		$db_selected = mysql_select_db($db);
		if (!$db_selected) $this->addError('Database','DB "'.$db.'" does not exist');
		if (count($this->formatErrors) > 0){ $this->showErrorSheet(); return false; }
		
		if (gettype($table) == 'array'){
			foreach($table as $table2){
				$sheet = $this->addSheet($table2);
				$query = 'SELECT * FROM `'.$db.'`.`'.$table2.'` ';
				EWXmysqlGenerateByQuery($sheet,$link,$query);
			}
		}
		else{
			if ($alternateName == null || empty($alternateName)) $sheet = $this->addSheet($table);
			else $sheet = $this->addSheet($alternateName);
			$query = 'SELECT * FROM `'.$db.'`.`'.$table.'` ';
			EWXmysqlGenerateByQuery($sheet,$link,$query);
		}
		if (count($this->formatErrors) > 0){ $this->showErrorSheet(); return false; }
	}

    /** Push to client first data */
    function stdOutStart(){
        $docTitle = '';
        $docSubject = '';
        $docAuthor = '';
        $docCreated = '';
        $docManager = '';
        $docCompany = '';
        $docVersion = 12;

        $errors = false;

        if ($this->showErrorSheet == true){
            $format = $this->addStyle('formatErrorsHeader');
            $format->fontBold();
            $format->bgColor('red');
        }

        if (!empty($this->docTitle)) $docTitle = '<Title>'.htmlspecialchars($this->docTitle).'</Title>'."\r";
        if (!empty($this->docSubject)) $docSubject = '<Subject>'.htmlspecialchars($this->docSubject).'</Subject>'."\r";
        if (!empty($this->docAuthor)) $docAuthor = '<Author>'.htmlspecialchars($this->docAuthor).'</Author>'."\r";
        if (!empty($this->docCreated)) $docCreated = '<Created>'.htmlspecialchars($this->docCreated).'</Created>'."\r";
        if (!empty($this->docManager)) $docManager = '<Manager>'.htmlspecialchars($this->docManager).'</Manager>'."\r";
        if (!empty($this->docCompany)) $docCompany = '<Company>'.htmlspecialchars($this->docCompany).'</Company>'."\r";

        $xml = '<?xml version="1.0"?>'."\r";
        $xml .= '<?mso-application progid="Excel.Sheet"?>'."\r";
        $xml .= '<Workbook
			xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			xmlns:o="urn:schemas-microsoft-com:office:office"
			xmlns:x="urn:schemas-microsoft-com:office:excel"
			xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
			xmlns:html="http://www.w3.org/TR/REC-html40">'."\r";
        $xml .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">'."\r";
        if (!empty($this->docTitle))	$xml .= '	'.$docTitle;
        if (!empty($this->docSubject))	$xml .= '	'.$docSubject;
        if (!empty($this->docAuthor))	$xml .= '	'.$docAuthor;
        if (!empty($this->docCreated))	$xml .= '	'.$docCreated;
        if (!empty($this->docManager))	$xml .= '	'.$docManager;
        if (!empty($this->docCompany))	$xml .= '	'.$docCompany;
        $xml .= '	<Version>'.$this->docVersion.'</Version>'."\r";
        $xml .= '</DocumentProperties>'."\r";
        $xml .= '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel" />'."\r";
        $xml .= '<Styles>'."\r";
        foreach($this->styles as $style){
            $xml .= $style->getStyleXML();
            if (count($style->getErrors()) > 0){
                $errors = true;
            }
        }
        $xml .= '</Styles>'."\r";

        /*if (count($this->sheets) == 0){
            $this->addSheet();
        }*/

        echo $xml;

        return true;
    }

    /** Push to client end */
    function stdOutEnd(){

        $xml = '</Workbook>';
        echo $xml;

        return true;
    }
}

/**
 * File contains the class files for ExcelWriterXML_Sheet
 * @package ExcelWriterXML
 */

/**
 * Class for generating sheets within the Excel document
 * @link http://msdn.microsoft.com/en-us/library/aa140066(office.10).aspx
 * @author Robert F Greer
 * @version 1.0
 * @package ExcelWriterXML
 * @uses ExcelWriterXML_Style::alignHorizontal()
 * @uses ExcelWriterXML_Style::alignRotate()
 * @uses ExcelWriterXML_Style::alignShrinktofit()
 * @uses ExcelWriterXML_Style::alignVertical()
 * @uses ExcelWriterXML_Style::alignVerticaltext()
 * @uses ExcelWriterXML_Style::alignWraptext()
 * @uses ExcelWriterXML_Style::bgColor()
 * @uses ExcelWriterXML_Style::bgPattern()
 * @uses ExcelWriterXML_Style::bgPatternColor()
 * @uses ExcelWriterXML_Style::border()
 * @uses ExcelWriterXML_Style::checkColor()
 * @uses ExcelWriterXML_Style::fontBold()
 * @uses ExcelWriterXML_Style::fontColor()
 * @uses ExcelWriterXML_Style::fontFamily()
 * @uses ExcelWriterXML_Style::fontItalic()
 * @uses ExcelWriterXML_Style::fontName()
 * @uses ExcelWriterXML_Style::fontOutline()
 * @uses ExcelWriterXML_Style::fontShadow()
 * @uses ExcelWriterXML_Style::fontStrikethrough()
 * @uses ExcelWriterXML_Style::fontUnderline()
 * @uses ExcelWriterXML_Style::getErrors()
 * @uses ExcelWriterXML_Style::getID()
 * @uses ExcelWriterXML_Style::getStyleXML()
 * @uses ExcelWriterXML_Style::name()
 * @uses ExcelWriterXML_Style::numberFormat()
 * @uses ExcelWriterXML_Style::numberFormatDate()
 * @uses ExcelWriterXML_Style::numberFormatDatetime()
 * @uses ExcelWriterXML_Style::numberFormatTime()
 */
class ExcelWriterXML_Sheet {
    // Private Variables
    private $id;
    private $cells = array();
    private $colWidth = array();
    private $rowHeight = array();
    private $URLs = array();
    private $mergeCells = array();
    private $comments = array();
    public  $formatErrors = array();
    private $displayRightToLeft = false;
    /////////////////////

    // Public Variables
    /////////////////////

    // Constructor
    /**
     * Constructor for a new Sheet
     * @param string $id The name of the sheet to be referenced within the
     * spreadsheet
     */
    function ExcelWriterXML_Sheet($id){
        $this->id = $id;
    }

    /**
     * Function to get the named value of the Sheet
     * @return string Name of the Sheet
     */
    function getID(){
        return $this->id;
    }

    /**
     * Adds a format error.  When the document is generated if there are any
     * errors they will be listed on a seperate sheet.
    @param string $function The name of the function that was called
     * @param string $message Details of the error
     */
    public function addError($function, $message){
        $tmp = array(
            'sheet'		=> $this->id,
            'function'	=> $function,
            'message'	=> $message,
        );
        $this->formatErrors[] = $tmp;
    }

    /**
     * Returns any errors found in the sheet
     * @return mixed Array of errors if they exist, otherwise false
     */
    public function getErrors(){
        return($this->formatErrors);
    }

    /**
     * Converts a MySQL type datetime field to a value that can be used within
     * Excel.
     * If the passed value is not valid then the passed string is sent back.
     * @param string $datetime Value must in in the format "yyyy-mm-dd hh:ii:ss"
     * @return string Value in the Excel format "yyyy-mm-ddThh:ii:ss.000"
     */
    public function convertMysqlDatetime($datetime){
        $datetime = trim($datetime);
        $pattern = "/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/";
        if (preg_match($pattern, $datetime, $matches)) {
            $datetime = $matches[0];
            list($date,$time) = explode(' ',$datetime);
            return($date.'T'.$time.'.000');
        }
        else{
            return($datetime);
        }
    }

    /**
     * Converts a MySQL type date field to a value that can be used within Excel
     * If the passed value is not valid then the passed string is sent back.
     * @param string $datetime Value must in in the format "yyyy-mm-dd hh:ii:ss"
     * or "yyyy-mm-dd"
     * @return string Value in the Excel format "yyyy-mm-ddT00:00:00.000"
     */
    public function convertMysqlDate($datetime){
        $datetime = trim($datetime);
        $pattern1 = "/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/";
        $pattern2 = "/[0-9]{4}-[0-9]{2}-[0-9]{2}/";
        if (preg_match($pattern1, $datetime, $matches)) {
            $datetime = $matches[0];
            list($date,$time) = explode(' ',$datetime);
            return($date.'T'.$time.'.000');
        }
        else if (preg_match($pattern2, $datetime, $matches)) {
            $date = $matches[0];
            return($date.'T00:00:00.000');
        }
        else{
            return($datetime);
        }
    }

    /**
     * Converts a MySQL type time field to a value that can be used within Excel
     * If the passed value is not valid then the passed string is sent back.
     * @param string $datetime Value must in in the format "yyyy-mm-dd hh:ii:ss"
     * or "hh:ii:ss"
     * @return string Value in the Excel format "1899-12-31Thh:ii:ss.000"
     */
    public function convertMysqlTime($datetime){
        $datetime = trim($datetime);
        $pattern1 = "/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/";
        $pattern2 = "/[0-9]{2}:[0-9]{2}:[0-9]{2}/";
        if (preg_match($pattern1, $datetime, $matches)) {
            $datetime = $matches[0];
            list($date,$time) = explode(' ',$datetime);
            return($date.'T'.$time.'.000');
        }
        else if (preg_match($pattern2, $datetime, $matches)) {
            $time = $matches[0];
            return('1899-12-31T'.$time.'.000');
        }
        else{
            return($datetime);
        }
    }


    /**
     * Writes a formula to a cell
     * From MS
     * Specifies the formula stored in this cell. All formulas are persisted in
     * R1C1 notation because they are significantly easier to parse and generate
     * than A1-style formulas. The formula is calculated upon reload unless
     * calculation is set to manual. Recalculation of the formula overrides the
     * value in this cell's Value attribute.
     * @see writeFormula()
     * @param string $dataType Type of data that the formula should generate,
     * "String" "Number" "DateTime"
     * @param integer $row Row, based upon a "1" based array
     * @param integer $column Column, based upon a "1" based array
     * @param string $data Formula data to be written to a cell
     * @param mixed $style Named style, or style reference to be applied to the
     * cell
     */
    public function writeFormula($dataType,$row,$column,$data,$style = null){
        if ($dataType != 'String'
            && $dataType != 'Number'
            && $dataType != 'DateTime'){
            $this->addError(__FUNCTION__,'('.$row.','.$column.') DataType for formula was not valid "'.$dataType.'"');
            $halign = 'Automatic';
        }

        $this->writeData('String',$row,$column,'',$style,$data);
    }

    /**
     * Writes a string to a cell
     * @see writeData()
     * @param integer $row Row, based upon a "1" based array
     * @param integer $column Column, based upon a "1" based array
     * @param string $data String data to be written to a cell
     * @param mixed $style Named style, or style reference to be applied to the
     * cell
     */
    public function writeString($row,$column,$data,$style = null){
        $this->writeData('String',$row,$column,$data,$style);
    }

    /**
     * Writes a number to a cell.
     * If the data is not numeric then the function will write the data as a
     * string.
     * @see writeData()
     * @param integer $row Row, based upon a "1" based array
     * @param integer $column Column, based upon a "1" based array
     * @param mixed $data Number data to be written to a cell
     * @param mixed $style Named style, or style reference to be applied to the
     * cell
     */
    public function writeNumber($row,$column,$data,$style = null){
        if (!is_numeric($data)){
            $this->writeData('String',$row,$column,$data,$style);
            $this->addError(__FUNCTION__,'('.$row.','.$column.') Tried to write non-numeric data to type Number "'.$data.'"');
        }
        else{
            $this->writeData('Number',$row,$column,$data,$style);
        }
    }

    /**
     * Writes a Date/Time to a cell.
     * If data is not valid the function will write the passed value as a
     * string.
     * @see writeData()
     * @param integer $row Row, based upon a "1" based array
     * @param integer $column Column, based upon a "1" based array
     * @param string $data Date or Time data to be written to a cell.  This must
     * be in the format "yyyy-mm-ddThh:ii:ss.000" for Excel to recognize it.
     * @param mixed $style Named style, or style reference to be applied to the
     * cell
     */
    public function writeDateTime($row,$column,$data,$style = null){
        $pattern = "/[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\.000/";
        if (preg_match($pattern, $data, $matches)) {
            $data = $matches[0];
            $this->writeData('DateTime',$row,$column,$data,$style);
        }
        else{
            $this->writeData('String',$row,$column,$data,$style);
            $this->addError(__FUNCTION__,'('.$row.','.$column.') Tried to write invalid datetime data to type DateTime "'.$data.'"');
        }
    }
    private function writeData($type,$row,$column,$data,$style = null,$formula = null){
        if ($style != null){
            if (gettype($style) == 'object'){
                if (get_class($style) == 'ExcelWriterXML_Style'){
                    $styleID = $style->getID();
                }
                else{
                    $this->addError(__FUNCTION__,'('.$row.','.$column.') StyleID supplied was an object, but not a style object "'.get_class($style).'"');
                    $styleID = null;
                }
            }
            else{
                $styleID = $style;
            }
        }
        else{
            $styleID = null;
        }

        $cell = array(
            'type'		=> $type,
            'style'		=> $styleID,
            'data'		=> $data,
            'formula'	=> $formula,
        );
        $this->cells[$row][$column] = $cell;
    }

    /**
     * Displays the sheet in Right to Left format
     */
    public function displayRightToLeft(){
        $this->displayRightToLeft = true;
    }

    /**
     * Called by the ExcelWriterXML class to get the XML data for this object
     * @return string Contains only the XML data for the sheet
     */
    public function getSheetXML(){
        ksort($this->cells);

        $displayRightToLeft = ($this->displayRightToLeft) ? 'ss:RightToLeft="1"' : '';

        $xml = '<Worksheet ss:Name="'.$this->id.'" '.$displayRightToLeft.'>'."\r";
        $xml .= '	<Table>'."\r";
        foreach($this->colWidth as $colIndex => $colWidth){
            $xml .= '		<Column ss:Index="'.$colIndex.'" ss:AutoFitWidth="0" ss:Width="'.$colWidth.'"/>'."\r";
        }
        foreach($this->cells as $row => $rowData){
            ksort($rowData);
            if (isset($this->rowHeight[$row])){
                $rowHeight = 'ss:AutoFitHeight="0" ss:Height="'.$this->rowHeight[$row].'"';
            }
            else{$rowHeight = '';}
            $xml .= '		<Row ss:Index="'.$row.'" '.$rowHeight.' >'."\r";
            foreach($rowData as $column => $cell){
                if (!empty($cell['formula'])) $formula = 'ss:Formula="'.$cell['formula'].'"';
                else $formula = '';
                if (!empty($cell['style'])) $style = 'ss:StyleID="'.$cell['style'].'"';
                else $style = '';
                if (empty($this->URLs[$row][$column])) $URL = '';
                else $URL = 'ss:HRef="'.htmlspecialchars($this->URLs[$row][$column]).'"';
                if (empty($this->mergeCells[$row][$column])) $mergeCell = '';
                else $mergeCell = 'ss:MergeAcross="'.$this->mergeCells[$row][$column]['width'].'" ss:MergeDown="'.$this->mergeCells[$row][$column]['height'].'"';
                if (empty($this->comments[$row][$column])) $comment = '';
                else{
                    $comment = '					<Comment ss:Author="'.$this->comments[$row][$column]['author'].'">'."\r";
                    $comment .= '					<ss:Data xmlns="http://www.w3.org/TR/REC-html40">'."\r";
                    $comment .= '					<B><Font html:Face="Tahoma" x:CharSet="1" html:Size="8" html:Color="#000000">'.htmlspecialchars($this->comments[$row][$column]['author']).':</Font></B>'."\r";
                    $comment .= '					<Font html:Face="Tahoma" x:CharSet="1" html:Size="8" html:Color="#000000">'.htmlspecialchars($this->comments[$row][$column]['comment']).'</Font>'."\r";
                    $comment .= '					</ss:Data>'."\r";
                    $comment .= '					</Comment>'."\r";
                }
                $type = $cell['type'];
                $data = $cell['data'];
                $data = htmlspecialchars($data);
                $data = str_replace("\r\n",'&#10;',$data);
                $data = str_replace("\n",'&#10;',$data);

                $xml .= '			<Cell '.$style.' ss:Index="'.$column.'" '.$URL.' '.$mergeCell.' '.$formula.'>'."\r";
                $xml .= '				<Data ss:Type="'.$type.'">';
                $xml .= $data;
                $xml .= '</Data>'."\r";
                $xml .= str_replace('&amp;#10;','&#10;',$comment);
                $xml .= '			</Cell>'."\r";
            }
            $xml .= '		</Row>'."\r";
        }
        $xml .= '	</Table>'."\r";
        $xml .= '</Worksheet>'."\r";
        return($xml);
    }

    /**
     * Alias for function columnWidth()
     */
    function cellWidth( $row, $col,$width = 48){$this->columnWidth($col,$width);}

    /**
     * Sets the width of a cell.
     * Sets  the width of the column that the cell resides in.
     * Cell width of zero effectively hides the column
     * @param integer $col Column, based upon a "1" based array
     * @param mixed $width Width of the cell/column, default is 48
     */
    function columnWidth( $col,$width = 48){$this->colWidth[$col] = $width;}

    /**
     * Alias for function rowHeight()
     */
    function cellHeight( $row, $col,$height = 12.5){$this->rowHeight($row,$height);}

    /**
     * Sets the height of a cell.
     * Sets  the height of the column that the cell resides in.
     * Cell height of zero effectively hides the row
     * @param integer $row Row, based upon a "1" based array
     * @param integer $col Column, based upon a "1" based array
     * @param mixed $height Height of the cell/column, default is 12.5
     */
    function rowHeight( $row,$height = 12.5){$this->rowHeight[$row] = $height;}

    /**
     * Makes the target cell a link to a URL
     * @param integer $row Row, based upon a "1" based array
     * @param integer $col Column, based upon a "1" based array
     * @param string $URL The URL that the link should point to
     */
    function addURL( $row, $col,$URL){$this->URLs[$row][$col] = $URL;}

    /**
     * Merges 2 or more cells.
     * The function acts like a bounding box, with the row and column defining
     * the upper left corner, and the width and height extending the box.
     * If width or height are zero (or ommitted) then the function does nothing.
     * @param integer $row Row, based upon a "1" based array
     * @param integer $col Column, based upon a "1" based array
     * @param integer $width Number of cells to the right to merge with
     * @param integer $height Number of cells down to merge with
     */
    function cellMerge($row,$col, $width = 0, $height = 0){
        if ($width < 0 || $height < 0){
            $this->addError(__FUNCTION__,'('.$row.','.$col.') Tried to merge cells with width/height < 0 "(w='.$width.',h='.$height.')"');
            return;
        }

        $this->mergeCells[$row][$col] = array(
            'width'		=> $width,
            'height'	=> $height,
        );
        /* I don't think this code is necessary
        if (!isset($cells[$row][$col])){
            $this->writeString($row,$col,'');
        }
        */
    }

    /**
     * Adds a comment to a cell
     * @param integer $row Row, based upon a "1" based array
     * @param integer $col Column, based upon a "1" based array
     * @param string $comment The comment to be displayed on the cell
     * @param string $author The comment will show a bold header displaying the
     * author
     */
    function addComment( $row, $col,$comment,$author = 'SYSTEM'){
        $this->comments[$row][$col] = array(
            'comment'	=> $comment,
            'author'	=> $author,
        );
    }

    /**
     * Outputs a MYSQL table or list of tables to an Excel doc
     * @param string $host MySQL host to connect to
     * @param string $username Username to connect with
     * @param string $password Password to connect with
     * @param string $db Database to use
     * @param mixed $table If string, out specific table.  If array, each table will have it's own sheet
     */
    public function mysqlQueryToTable($host,$username,$password,$query){
        EWXcheckDriverDB('mysql');
        if (empty($host))		$this->addError('Database','HOSTNAME is empty');
        if (empty($username))	$this->addError('Database','USERNAME is empty');
        if (count($this->formatErrors) > 0){return false;}

        $link = mysql_connect($host,$username,$password);
        if (!$link) $this->addError('Database','UNABLE to connect to '.$host.'('.mysql_error().')');
        if (count($this->formatErrors) > 0){return false;}

        EWXmysqlGenerateByQuery($this,$link,$query);
    }

    /** Push to client Start of sheet */
    function stdOutSheetStart(){
        $displayRightToLeft = ($this->displayRightToLeft) ? 'ss:RightToLeft="1"' : '';

        $xml = '<Worksheet ss:Name="'.$this->id.'" '.$displayRightToLeft.'>'."\r";
        $xml .= '	<Table>'."\r";
        foreach($this->colWidth as $colIndex => $colWidth){
            $xml .= '		<Column ss:Index="'.$colIndex.'" ss:AutoFitWidth="0" ss:Width="'.$colWidth.'"/>'."\r";
        }

        echo $xml;

        return true;
    }

    /** Push to client End of sheet */
    function stdOutSheetEnd(){
        $xml = '';
        $xml .= '	</Table>'."\r";
        $xml .= '</Worksheet>'."\r";

        echo $xml;

        return true;
    }

    /** Push to client Row */
    function stdOutSheetRowStart($row){
        $xml = '';
        if (isset($this->rowHeight[$row])){
            $rowHeight = 'ss:AutoFitHeight="0" ss:Height="'.$this->rowHeight[$row].'"';
        }
        else{$rowHeight = '';}
        $xml .= '		<Row ss:Index="'.$row.'" '.$rowHeight.' >'."\r";

        echo $xml;

        return true;
    }

    /** Push to client Row */
    function stdOutSheetRowEnd(){
        $xml = '';
        $xml .= '		</Row>'."\r";

        echo $xml;

        return true;
    }

    /** Push to client Row */
    function stdOutSheetColumn($type,$row,$column,$data,$style = null,$formula = null){
        $xml = '';

        if ($formula) $formula = 'ss:Formula="'.$formula.'"';
        else $formula = '';
        if ($style) $style = 'ss:StyleID="'.$style.'"';
        else $style = '';
        if (empty($this->URLs[$row][$column])) $URL = '';
        else $URL = 'ss:HRef="'.htmlspecialchars($this->URLs[$row][$column]).'"';
        if (empty($this->mergeCells[$row][$column])) $mergeCell = '';
        else $mergeCell = 'ss:MergeAcross="'.$this->mergeCells[$row][$column]['width'].'" ss:MergeDown="'.$this->mergeCells[$row][$column]['height'].'"';
        if (empty($this->comments[$row][$column])) $comment = '';
        else{
            $comment = '					<Comment ss:Author="'.$this->comments[$row][$column]['author'].'">'."\r";
            $comment .= '					<ss:Data xmlns="http://www.w3.org/TR/REC-html40">'."\r";
            $comment .= '					<B><Font html:Face="Tahoma" x:CharSet="1" html:Size="8" html:Color="#000000">'.htmlspecialchars($this->comments[$row][$column]['author']).':</Font></B>'."\r";
            $comment .= '					<Font html:Face="Tahoma" x:CharSet="1" html:Size="8" html:Color="#000000">'.htmlspecialchars($this->comments[$row][$column]['comment']).'</Font>'."\r";
            $comment .= '					</ss:Data>'."\r";
            $comment .= '					</Comment>'."\r";
        }
        $type = $type;
        $data = $data;
        $data = htmlspecialchars($data);
        $data = str_replace("\r\n",'&#10;',$data);
        $data = str_replace("\n",'&#10;',$data);

        $xml .= '			<Cell '.$style.' ss:Index="'.$column.'" '.$URL.' '.$mergeCell.' '.$formula.'>'."\r";
        $xml .= '				<Data ss:Type="'.$type.'">';
        $xml .= $data;
        $xml .= '</Data>'."\r";
        $xml .= str_replace('&amp;#10;','&#10;',$comment);
        $xml .= '			</Cell>'."\r";

        echo $xml;

        return true;
    }
}

/**
 * File contains the class files for ExcelWriterXML_Style
 * @package ExcelWriterXML
 */

/**
 * Style class for generating Excel styles
 * @link http://msdn.microsoft.com/en-us/library/aa140066(office.10).aspx
 * @author Robert F Greer
 * @version 1.0
 * @package ExcelWriterXML
 */
class ExcelWriterXML_Style {
    // Private Variables
    /////////////////////
    // Options
    private $id;
    private $name;
    private $useAlignment = false;
    private $useFont = false;
    private $useBorder = false;
    private $useInterior = false;

    // Alignment
    private $valign;
    private $halign;
    private $rotate;
    private $shrinktofit = 0;
    private $verticaltext = 0;
    private $wraptext = 0;

    // Font
    private $fontColor = 'Automatic';
    private $fontName;
    private $fontFamily;
    private $fontSize;
    private $bold;
    private $italic;
    private $underline;
    private $strikethrough;
    private $shadow;
    private $outline;
    /////////////////////

    // Borders
    private $borderTop = array();
    private $borderBottom = array();
    private $borderLeft = array();
    private $borderRight = array();
    private $borderDL = array();
    private $borderDR = array();
    /////////////////////

    // Interior
    private $interiorColor;
    private $interiorPattern;
    private $interiorPatternColor;
    /////////////////////

    // NumberFormat
    private $numberFormat;
    /////////////////////

    // Other Vars
    private $formatErrors = array();
    private $namedColorsIE = array (
        'aliceblue' => '#F0F8FF',
        'antiquewhite' => '#FAEBD7',
        'aqua' => '#00FFFF',
        'aquamarine' => '#7FFFD4',
        'azure' => '#F0FFFF',
        'beige' => '#F5F5DC',
        'bisque' => '#FFE4C4',
        'black' => '#000000',
        'blanchedalmond' => '#FFEBCD',
        'blue' => '#0000FF',
        'blueviolet' => '#8A2BE2',
        'brown' => '#A52A2A',
        'burlywood' => '#DEB887',
        'cadetblue' => '#5F9EA0',
        'chartreuse' => '#7FFF00',
        'chocolate' => '#D2691E',
        'coral' => '#FF7F50',
        'cornflowerblue' => '#6495ED',
        'cornsilk' => '#FFF8DC',
        'crimson' => '#DC143C',
        'cyan' => '#00FFFF',
        'darkblue' => '#00008B',
        'darkcyan' => '#008B8B',
        'darkgoldenrod' => '#B8860B',
        'darkgray' => '#A9A9A9',
        'darkgreen' => '#006400',
        'darkkhaki' => '#BDB76B',
        'darkmagenta' => '#8B008B',
        'darkolivegreen' => '#556B2F',
        'darkorange' => '#FF8C00',
        'darkorchid' => '#9932CC',
        'darkred' => '#8B0000',
        'darksalmon' => '#E9967A',
        'darkseagreen' => '#8FBC8F',
        'darkslateblue' => '#483D8B',
        'darkslategray' => '#2F4F4F',
        'darkturquoise' => '#00CED1',
        'darkviolet' => '#9400D3',
        'deeppink' => '#FF1493',
        'deepskyblue' => '#00BFFF',
        'dimgray' => '#696969',
        'dodgerblue' => '#1E90FF',
        'firebrick' => '#B22222',
        'floralwhite' => '#FFFAF0',
        'forestgreen' => '#228B22',
        'fuchsia' => '#FF00FF',
        'gainsboro' => '#DCDCDC',
        'ghostwhite' => '#F8F8FF',
        'gold' => '#FFD700',
        'goldenrod' => '#DAA520',
        'gray' => '#808080',
        'green' => '#008000',
        'greenyellow' => '#ADFF2F',
        'honeydew' => '#F0FFF0',
        'hotpink' => '#FF69B4',
        'indianred' => '#CD5C5C',
        'indigo' => '#4B0082',
        'ivory' => '#FFFFF0',
        'khaki' => '#F0E68C',
        'lavender' => '#E6E6FA',
        'lavenderblush' => '#FFF0F5',
        'lawngreen' => '#7CFC00',
        'lemonchiffon' => '#FFFACD',
        'lightblue' => '#ADD8E6',
        'lightcoral' => '#F08080',
        'lightcyan' => '#E0FFFF',
        'lightgoldenrodyellow' => '#FAFAD2',
        'lightgreen' => '#90EE90',
        'lightgrey' => '#D3D3D3',
        'lightpink' => '#FFB6C1',
        'lightsalmon' => '#FFA07A',
        'lightseagreen' => '#20B2AA',
        'lightskyblue' => '#87CEFA',
        'lightslategray' => '#778899',
        'lightsteelblue' => '#B0C4DE',
        'lightyellow' => '#FFFFE0',
        'lime' => '#00FF00',
        'limegreen' => '#32CD32',
        'linen' => '#FAF0E6',
        'magenta' => '#FF00FF',
        'maroon' => '#800000',
        'mediumaquamarine' => '#66CDAA',
        'mediumblue' => '#0000CD',
        'mediumorchid' => '#BA55D3',
        'mediumpurple' => '#9370DB',
        'mediumseagreen' => '#3CB371',
        'mediumslateblue' => '#7B68EE',
        'mediumspringgreen' => '#00FA9A',
        'mediumturquoise' => '#48D1CC',
        'mediumvioletred' => '#C71585',
        'midnightblue' => '#191970',
        'mintcream' => '#F5FFFA',
        'mistyrose' => '#FFE4E1',
        'moccasin' => '#FFE4B5',
        'navajowhite' => '#FFDEAD',
        'navy' => '#000080',
        'oldlace' => '#FDF5E6',
        'olive' => '#808000',
        'olivedrab' => '#6B8E23',
        'orange' => '#FFA500',
        'orangered' => '#FF4500',
        'orchid' => '#DA70D6',
        'palegoldenrod' => '#EEE8AA',
        'palegreen' => '#98FB98',
        'paleturquoise' => '#AFEEEE',
        'palevioletred' => '#DB7093',
        'papayawhip' => '#FFEFD5',
        'peachpuff' => '#FFDAB9',
        'peru' => '#CD853F',
        'pink' => '#FFC0CB',
        'plum' => '#DDA0DD',
        'powderblue' => '#B0E0E6',
        'purple' => '#800080',
        'red' => '#FF0000',
        'rosybrown' => '#BC8F8F',
        'royalblue' => '#4169E1',
        'saddlebrown' => '#8B4513',
        'salmon' => '#FA8072',
        'sandybrown' => '#F4A460',
        'seagreen' => '#2E8B57',
        'seashell' => '#FFF5EE',
        'sienna' => '#A0522D',
        'silver' => '#C0C0C0',
        'skyblue' => '#87CEEB',
        'slateblue' => '#6A5ACD',
        'slategray' => '#708090',
        'snow' => '#FFFAFA',
        'springgreen' => '#00FF7F',
        'steelblue' => '#4682B4',
        'tan' => '#D2B48C',
        'teal' => '#008080',
        'thistle' => '#D8BFD8',
        'tomato' => '#FF6347',
        'turquoise' => '#40E0D0',
        'violet' => '#EE82EE',
        'wheat' => '#F5DEB3',
        'white' => '#FFFFFF',
        'whitesmoke' => '#F5F5F5',
        'yellow' => '#FFFF00',
        'yellowgreen' => '#9ACD32',
    );
    /////////////////////

    // Public Variables
    /////////////////////

    // Constructor

    /**
     * Constructor for a style
     * @param string $id The named style referenced by Excel.  This is called by
     * ExcelWriterXML object when adding a style
     */
    function ExcelWriterXML_Style($id){
        $this->id = $id;
    }
    /////////////////////

    /**
     * Returns the named style for this style
     * @return string $id The id for this style
     */
    public function getID(){
        return $this->id;
    }

    /**
     * Retrieves the XML string data for a style.
     * Called by ExcelWriterXML object
     * @return string Returns the formatted XML data <style>...</style>
     */
    public function getStyleXML(){
        $name = '';
        $valign = '';
        $halign = '';
        $rotate = '';
        $shrinktofit = '';
        $verticaltext = '';
        $wraptext = '';

        $bold = '';
        $italic = '';
        $strikethrough = '';
        $underline = '';
        $outline = '';
        $shadow = '';
        $fontName = '';
        $fontFamily = '';
        $fontSize = '';

        $borders = '';

        $interior = '';
        $interiorColor = '';
        $interiorPattern = '';
        $interiorPatternColor = '';

        $numberFormat = '';

        if (empty($this->id)) throw new exception;
        if (!empty($this->name)){$name = 'ss:Name="'.$this->name.'"';}

        // Alignment
        if ($this->useAlignment){
            if (!empty($this->valign)){$valign = 'ss:Vertical="'.$this->valign.'"';}
            if (!empty($this->halign)){$halign = 'ss:Horizontal="'.$this->halign.'"';}
            if (!empty($this->rotate)){$rotate = 'ss:Rotate="'.$this->rotate.'"';}
            if (!empty($this->shinktofit)){$shrinktofit = 'ss:ShrinkToFit="1"';}
            if (!empty($this->verticaltext)){$verticaltext = 'ss:VerticalText="1"';}
            if (!empty($this->wraptext)){$wraptext = 'ss:WrapText="1"';}
        }

        // Font
        if ($this->useFont){
            if (!empty($this->fontColor)){$fontColor = 'ss:Color="'.$this->fontColor.'"';}
            if (!empty($this->bold)){$bold = 'ss:Bold="1"';}
            if (!empty($this->italic)){$italic = 'ss:Italic="1"';}
            if (!empty($this->strikethrough)){$strikethrough = 'ss:StrikeThrough="'.$this->strikethrough.'"';}
            if (!empty($this->underline)){$underline = 'ss:Underline="'.$this->underline.'"';}
            if (!empty($this->outline)){$outline = 'ss:Outline="1"';}
            if (!empty($this->shadow)){$shadow = 'ss:Shadow="1"';}
            if (!empty($this->fontName)){$fontName = 'ss:FontName="'.$this->fontName.'"';}
            if (!empty($this->fontFamily)){$fontFamily = 'x:Family="'.$this->fontFamily.'"';}
            if (!empty($this->fontSize)){$fontSize = 'ss:Size="'.$this->fontSize.'"';}
        }
        // Border
        if ($this->useBorder){
            $borders = '		<Borders>'."\r";
            $positions = array(
                'Top'			=> $this->borderTop,
                'Bottom'		=> $this->borderBottom,
                'Left'			=> $this->borderLeft,
                'Right'			=> $this->borderRight,
                'DiagonalLeft'	=> $this->borderDL,
                'DiagonalRight'	=> $this->borderDR,

            );
            foreach($positions as $position => $pData){
                if (empty($pData)) continue;
                $bLinestyle = isset($pData['LineStyle'])
                    ? 'ss:LineStyle="'.$pData['LineStyle'].'"'
                    : '';
                $bColor = isset($pData['Color'])
                    ? 'ss:Color="'.$pData['Color'].'"'
                    : '';
                $bWeight = isset($pData['Weight'])
                    ? 'ss:Weight="'.$pData['Weight'].'"'
                    : '';
                $borders .= '<Border ss:Position="'.$position.'" '.$bLinestyle.' '.$bColor.' '.$bWeight.'/>'."\r";
            }
            $borders .= '</Borders>'."\r";
        }

        if ($this->useInterior){
            if (!empty($this->interiorColor)){$interiorColor = 'ss:Color="'.$this->interiorColor.'"';}
            if (!empty($this->interiorPattern)){$interiorPattern = 'ss:Pattern="'.$this->interiorPattern.'"';}
            if (!empty($this->interiorPatternColor)){$interiorPatternColor = 'ss:PatternColor="'.$this->interiorPatternColor.'"';}
            $interior = '		<Interior '.$interiorColor.' '.$interiorPattern.' '.$interiorPatternColor.'/>'."\r";
        }

        if (!empty($this->numberFormat)) {$numberFormat = '		<NumberFormat ss:Format="'.$this->numberFormat.'"/>'."\r";}
        else $numberFormat = '		<NumberFormat/>'."\r";

        $xml = '	<Style ss:ID="'.$this->id.'" '.$name.'>'."\r";
        if ($this->useAlignment) $xml .= '		<Alignment '.$valign.' '.$halign.' '.$rotate.' '.$shrinktofit.' '.$wraptext.' '.$verticaltext.'/>'."\r";
        if ($this->useBorder) $xml .= $borders;
        if ($this->useFont) $xml .= '		<Font '.$fontSize.' '.$fontColor.' '.$bold.' '.$italic.' '.$strikethrough.' '.$underline.' '.$shadow.' '.$outline.' '.$fontName.' '.$fontFamily.'/>'."\r";
        if ($this->useInterior) $xml .= $interior;
        $xml .= $numberFormat;
        $xml .= '		<Protection/>'."\r";
        $xml .= '	</Style>'."\r";
        return($xml);
    }

    /**
     * Checks whether a color is valid for the spreadsheet
     * @param string $color Named color from MS or web color in HEX format (e.g.
     * #ff00ff
     * @return mixed Either the valid color in HEX format or false if the color
     * is not valid
     */
    public function checkColor($color){
        $pattern = "/[0-9a-f]{6}/";
        if (preg_match($pattern, strtolower($color), $matches)) {
            $color = '#'.$matches[0];
            return($color);
        }
        else if (isset($this->namedColorsIE[strtolower($color)])){
            $color = $this->namedColorsIE[strtolower($color)];
            return($color);
        }
        else{
            $this->addError(__FUNCTION__,'Supplied color was not valid "'.$color.'"');
            return(false);
        }
    }

    /**
     * Adds a format error.  When the document is generated if there are any
     * errors they will be listed on a seperate sheet.
     * @param string $namedStyle The style in which the error occurred
     * @param string $function The name of the function that was called
     * @param string $message Details of the error
     */
    private function addError($function, $message){
        $tmp = array(
            'style'		=> $this->id,
            'function'	=> $function,
            'message'	=> $message,
        );
        $this->formatErrors[] = $tmp;
    }

    /**
     * Returns any errors found in the sheet
     * @return mixed Array of errors if they exist, otherwise false
     */
    public function getErrors(){
        return($this->formatErrors);
    }


    // Change Options

    /**
     * Changes the name of the named style
     * @param string $name The named style referenced by Excel.
     */
    public function name($name){$this->name = $name; }
    ////////////////////////

    // Change Alignment
    /**
     * Changes the vertical alignment setting for the style
     * @param string $valign The value for the vertical alignment.
     * Acceptable values are "Automatic" "Top" "Bottom" "Center"
     */
    public function alignVertical($valign){
        // Automatic, Top, Bottom, Center
        if ($valign != 'Automatic'
            && $valign != 'Top'
            && $valign != 'Bottom'
            && $valign != 'Center'){
            $this->addError(__FUNCTION__,'vertical alignment was not valid "'.$valign.'"');
            return;
        }
        $this->valign = $valign;
        $this->useAlignment = true;
    }

    /**
     * Changes the horizontal alignment setting for the style
     * @param string $halign The value for the horizontal alignment. Acceptable
     * values are "Automatic" "Left" "Center" "Right"
     */
    public function alignHorizontal($halign){
        // Automatic, Left, Center, Right
        if ($halign != 'Automatic'
            && $halign != 'Left'
            && $halign != 'Center'
            && $halign != 'Right'){
            $this->addError(__FUNCTION__,'horizontal alignment was not valid "'.$halign.'"');
            $halign = 'Automatic';
        }
        $this->halign = $halign;
        $this->useAlignment = true;
    }

    /**
     * Changes the rotation setting for the style
     * @param mixed $rotate The value for the Rotation.  Value must be a
     * number between -90 and 90
     */
    public function alignRotate($rotate){
        // Degrees to rotate the text
        if (!is_numeric($rotate)){
            $this->addError(__FUNCTION__,'rotation was not numeric "'.$rotate.'"');
            return;
        }
        if (abs($rotate) > 90){
            $rotate = $rotate % 90;
            $this->addError(__FUNCTION__,'rotation was greater than 90, defaulted to "'.$rotate.'"');
        }
        $this->rotate = $rotate;
        $this->useAlignment = true;
    }

    /**
     * Changes the Shrink To Fit setting for the style
     * ShrinkToFit shrinks the text so that it fits within the cell.
     * This doesn't actually work.
     */
    public function alignShrinktofit(){
        $this->shrinktofit = 1;
        $this->useAlignment = true;
    }

    /**
     * Changes the Vertical Text setting for the style.
     * Text will be displayed vertically.
     */
    public function alignVerticaltext(){
        $this->verticaltext = 1;
        $this->useAlignment = true;
    }

    /**
     * Changes the Wrap Text setting for the style.
     */
    public function alignWraptext(){
        $this->wraptext = 1;
        $this->useAlignment = true;
    }
    /////////////////////////

    // Change Font
    /**
     * Changes the size of the font
     * @param string $fontSize The value for the Size. Value must be greater
     * than zero
     */
    public function fontSize($fontSize = 10){
        if (!is_numeric($fontSize)){
            $fontSize = 10;
            $this->addError(__FUNCTION__,'font size was not a number, defaulted to 10 "'.$fontSize.'"');
        }
        if ($fontSize <= 0){
            $fontSize = 10;
            $this->addError(__FUNCTION__,'font size was less than zero, defaulted to 10 "'.$fontSize.'"');
        }
        $this->fontSize = $fontSize;
        $this->useFont = true;
    }

    /**
     * Changes the color for the font
     * @param string $fontColor The value for the Color.
     * This can be a MS named color or a Hex web color.
     */
    public function fontColor($fontColor = 'Automatic'){
        $pattern = "/[0-9a-f]{6}/";
        $fontColor = $this->checkColor($fontColor);
        if ($fontColor === false){
            $this->addError(__FUNCTION__,'font color was not valid "'.$fontColor.'"');
            $fontColor = 'Automatic';
        }
        $this->fontColor = $fontColor;
        $this->useFont = true;
    }

    /**
     * Changes the font for the cell
     * @param string $fontName The value for the font name. This should be a
     * standard windows font available on most systems.
     */
    public function fontName($fontName = 'Arial'){
        $this->fontName = $fontName;
        $this->useFont = true;
    }

    /**
     * Changes the family for the font
     * @param string $fontFamily The value for the font family. Not really sure
     * what this does.
     * Win32-dependant font family.
     * Values can be "Automatic" "Decorative"
     * "Modern" "Roman" "Script" "Swiss"
     */
    public function fontFamily($fontFamily = 'Swiss'){
        // Win32-dependent font family.
        // Automatic, Decorative, Modern, Roman, Script, and Swiss
        if ($fontFamily != 'Automatic'
            && $fontFamily != 'Decorative'
            && $fontFamily != 'Modern'
            && $fontFamily != 'Roman'
            && $fontFamily != 'Script'
            && $fontFamily != 'Swiss'){
            $this->addError(__FUNCTION__,'font family was not valid "'.$fontFamily.'"');
            return;
        }
        $this->fontFamily = $fontFamily;
        $this->useFont = true;
    }

    /**
     * Makes the font bold for the named style
     */
    public function fontBold(){
        $this->bold = 1;
        $this->useFont = true;
    }

    /**
     * Makes the font italic for the named style
     */
    public function fontItalic(){
        $this->italic = 1;
        $this->useFont = true;
    }

    /**
     * Makes the font strikethrough for the named style
     */
    public function fontStrikethrough(){
        $this->strikethrough = 1;
        $this->useFont = true;
    }

    /**
     * Makes the font underlined for the named style
     * @param string $uStyle The type of underlining for the style.
     * Acceptable values are "None" "Single" "Double" "SingleAccounting"
     * "DoubleAccounting"
     */
    public function fontUnderline($uStyle = 'Single'){
        // None, Single, Double, SingleAccounting, and DoubleAccounting
        if ($uStyle != 'None'
            && $uStyle != 'Single'
            && $uStyle != 'Double'
            && $uStyle != 'SingleAccounting'
            && $uStyle != 'DoubleAccounting'){
            $this->addError(__FUNCTION__,'underline type was not valid "'.$uStyle.'"');
            return;
        }
        $this->underline = $uStyle;
        $this->useFont = true;
    }

    /**
     * Makes the font shadowed for the named style
     */
    public function fontShadow(){
        $this->shadow = 1;
        $this->useFont = true;
    }

    /**
     * Makes the font outlines for the named style
     */
    public function fontOutline(){
        $this->outline = 1;
        $this->useFont = true;
    }
    //////////////////////////

    // Change Border

    /**
     * Sets the border for the named style.
     * This function can be called multiple times to set different sides of the
     * cell or set all sides the same at once.
     * @param string $position Sets which side of the cell should be modified.
     * Acceptable values are "All" "Left" "Top" "Right" "Bottom" "DiagonalLeft"
     * "DiagonalRight"
     * @param integer $weight Thickness of the border.  Default is 1 "Thin"
     * @param string $color Color of the border. Default is "Automatic" but any
     * 6-hexadecimal digit number in "#rrggbb" format or it can be any of the
     * Microsoft� Internet Explorer named colors
     * @param string $linestyle Type of line to use on the border.
     * Default is "Continuous".  Acceptable balues are "None" "Continuous"
     * "Dash" "Dot" "DashDot" "DashDotDot" "SlantDashDot" "Double"
     */
    public function border(
        $position = 'All'			// All, Left, Top, Right, Bottom, DiagonalLeft, DiagonalRight
        ,$weight = '1'				// 0�Hairline, 1�Thin, 2�Medium, 3�Thick
        ,$color = 'Automatic'		// Automatic, 6-hexadecimal digit number in "#rrggbb" format or it can be any of the Microsoft� Internet Explorer named colors
        ,$linestyle = 'Continuous'	// None, Continuous, Dash, Dot, DashDot, DashDotDot, SlantDashDot, Double
    ){

        if ($position != 'All'
            && $position != 'Left'
            && $position != 'Top'
            && $position != 'Right'
            && $position != 'Bottom'
            && $position != 'DiagonalLeft'
            && $position != 'DiagonalRight'){
            $this->addError(__FUNCTION__,'border position was not valid, defaulted to All "'.$position.'"');
            $position = 'All';
        }

        if (is_numeric($weight)){
            if (abs($weight) > 3){
                $this->addError(__FUNCTION__,'line weight greater than 3, defaulted to 3 "'.$weight.'"');
                $weight = 3;
            }
        }
        else{
            $this->addError(__FUNCTION__,'line weight not numeric, defaulted to 3 "'.$weight.'"');
            $weight = 1;
        }

        $color = $this->checkColor($color);
        if ($color === false){
            $this->addError(__FUNCTION__,'border color was not valid, defaulted to Automatic "'.$weight.'"');
            $color = 'Automatic';
        }

        if ($linestyle != 'None'
            && $linestyle != 'Continuous'
            && $linestyle != 'Dash'
            && $linestyle != 'Dot'
            && $linestyle != 'DashDot'
            && $linestyle != 'DashDotDot'
            && $linestyle != 'SlantDashDot'
            && $linestyle != 'Double'){
            $linestyle = 'Continuous';
            $this->addError(__FUNCTION__,'line style was not valid, defaulted to Continuous "'.$linestyle.'"');
        }


        $tmp = array(
            'LineStyle'	=> $linestyle,
            'Color'		=> $color,
            'Weight'	=> $weight,
        );
        if ($position == 'Top'		|| $position == 'All') $this->borderTop = $tmp;
        if ($position == 'Bottom'	|| $position == 'All') $this->borderBottom = $tmp;
        if ($position == 'Left'		|| $position == 'All') $this->borderLeft = $tmp;
        if ($position == 'Right'	|| $position == 'All') $this->borderRight = $tmp;
        if ($position == 'DiagonalLeft'	)					$this->borderDL = $tmp;
        if ($position == 'DiagonalRight'	)				$this->borderDR = $tmp;

        $this->useBorder = true;
    }
    //////////////////////////

    // Change Interior
    /**
     * Sets the background style of a style
     * @param string $color Named color from MS or web color in HEX format (e.g.
     * #ff00ff
     * @param string $pattern Defaults to a None if not supplied.
     * @param string $patternColor Defaults to a Automatic if not supplied.
     */
    public function bgColor($color = 'Yellow',$pattern = 'Solid', $patternColor = null){
        // 6-hexadecimal digit number in "#rrggbb" format
        // Or it can be any of the Internet Explorer named colors
        $color = $this->checkColor($color);
        if ($color === false){
            $color = 'Yellow';
            $this->addError(__FUNCTION__,'cell color not valid, defaulted to Yellow "'.$color.'"');
        }
        $this->interiorColor = $color;
        if ($pattern != 'None'){
            $this->bgPattern($pattern, $patternColor);
        }
        $this->useInterior = true;
    }

    /**
     * Sets the background pattern of a style.
     * @see bgColor()
     * @param string $color Named color from MS or web color in HEX format (e.g.
     * #ff00ff
     * @param string $pattern Defaults to a solid if not supplied.
     */
    public function bgPattern($pattern = 'None', $color = null){
        // None, Solid, Gray75, Gray50, Gray25, Gray125, Gray0625,
        // HorzStripe, VertStripe, ReverseDiagStripe, DiagStripe,
        // DiagCross, ThickDiagCross, ThinHorzStripe, ThinVertStripe,
        // ThinReverseDiagStripe, ThinDiagStripe, ThinHorzCross, and ThinDiagCross
        if ($pattern != 'None'
            && $pattern != 'Solid'
            && $pattern != 'Gray75'
            && $pattern != 'Gray50'
            && $pattern != 'Gray25'
            && $pattern != 'Gray125'
            && $pattern != 'Gray0625'
            && $pattern != 'HorzStripe'
            && $pattern != 'VertStripe'
            && $pattern != 'ReverseDiagStripe'
            && $pattern != 'DiagStripe'
            && $pattern != 'DiagCross'
            && $pattern != 'ThickDiagCross'
            && $pattern != 'ThinHorzStripe'
            && $pattern != 'ThinVertStripe'
            && $pattern != 'ThinReverseDiagStripe'
            && $pattern != 'ThinDiagStripe'
            && $pattern != 'ThinHorzCross'
            && $pattern != 'ThinDiagCross'){
            $pattern = 'None';
            $this->addError(__FUNCTION__,'cell pattern was not valid, defaulted to Solid "'.$pattern.'"');
        }

        $this->interiorPattern = $pattern;
        if ($color != null) $this->bgPatternColor($color);
        $this->useInterior = true;
    }

    /**
     * Specifies the secondary fill color of the cell when Pattern does not equal Solid.
     * @see function bgPattern()
     * @param string $color Named color from MS or web color in HEX format (e.g.
     * #ff00ff
     * @param string $pattern Defaults to a solid if not supplied.
     */
    public function bgPatternColor($color = 'Yellow'){
        // 6-hexadecimal digit number in "#rrggbb" format
        // Or it can be any of the Internet Explorer named colors
        if ($color != 'Automatic'){
            $color = $this->checkColor($color);
            if ($color === false){
                $color = 'Automatic';
                $this->addError(__FUNCTION__,'cell pattern color was not valid, defaulted to Automatic "'.$color.'"');
            }
        }
        $this->interiorPatternColor = $color;
        $this->useInterior = true;
    }

    //////////////////////////

    // Number Formats

    /**
     * Sets the number format of a style
     * @param string $formatString Format string to be used by Excel for
     * displaying the number.
     */
    public function numberFormat($formatString){$this->numberFormat = $formatString;}

    /**
     * Sets a default date format for a style
     */
    public function numberFormatDate(){$this->numberFormat('mm/dd/yy');}

    /**
     * Sets a default time format for a style
     */
    public function numberFormatTime(){$this->numberFormat('hh:mm:ss');}

    /**
     * Sets a default date and time format for a style
     */
    public function numberFormatDatetime(){$this->numberFormat('mm/dd/yy\ hh:mm:ss');}
    //////////////////////////
}

function EWXmysqlGenerateByQuery(&$sheet,$link,$query){
	$res = mysql_query($query,$link);
	if (!$res) $sheet->addError('Database','Unable to execute query ('.mysql_error().')');
	if (count($sheet->formatErrors) > 0){return false;}

	if (mysql_num_rows($res) == 0){
		$sheet->writeString(1,1,'No data');
		return true;
	}
	$row = 0;
	$headersWritten = false;
	while($data = mysql_fetch_row($res)){
		$row++;$col=1;
		if ($headersWritten == false){
			$numFields = mysql_num_fields($res);
			for($x=0; $x<$numFields;$x++){
				$name = mysql_field_name($res,$x);
				$sheet->writeString($row,$col++,$name,'db_header');
			}
			$row++;$col=1;
			$headersWritten = true;
		}
		foreach($data as $offset => $value){
			$field = mysql_field_name($res,$offset);
			$type = mysql_field_type($res,$offset);
			$value = htmlentities(trim($value));
			if (strstr($type,'int')){
				$sheet->writeNumber($row,$col++,$value);
			}
			else if ($type == 'datetime'){
				$value = $sheet->convertMysqlDateTime($value);
				$sheet->writeDateTime($row,$col++,$value,'db_datetime');
			}
			else if ($type == 'date'){
				$value = $sheet->convertMysqlDate($value);
				$sheet->writeDateTime($row,$col++,$value,'db_date');
			}
			else if ($type == 'time'){
				$value = $sheet->convertMysqlTime($value);
				$sheet->writeDateTime($row,$col++,$value,'db_time');
			}
			else if (is_numeric($value)){
				$sheet->writeNumber($row,$col++,$value);
			}
			else{
				$sheet->writeString($row,$col++,$value);
			}
		}
	}
	return true;
}
function EWXcheckDriverDB($driver){
	if (!extension_loaded($driver)){
		$this->addError('Database','DB driver "'.$driver.'" could not be loaded');
		return false;
	}
	return true;
}

function EWXcreateStylesDB(&$xml){
	if ($xml->checkStyleID('db_header')){
		$sHeader = $xml->addStyle('db_header');
		$sHeader->fontBold();
		$sHeader->fontFamily('Swiss');
		$sHeader->fontColor('0000FF');
	}
	if ($xml->checkStyleID('db_datetime')){
		$hDateTime = $xml->addStyle('db_datetime');
		$hDateTime->numberFormatDateTime();
	}
	if ($xml->checkStyleID('db_date')){
		$hDate = $xml->addStyle('db_date');
		$hDate->numberFormatDate();
	}
	if ($xml->checkStyleID('db_time')){
		$hTime = $xml->addStyle('db_time');
		$hTime->numberFormatTime();
	}
}

?>