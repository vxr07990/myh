<?php

class OrdersTask_ExportExcel_View extends Vtiger_View_Controller
{
    protected $fileName = 'DaybookReport';

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('GetXLS');
    }

    public function checkPermission(Vtiger_Request $request)
    {
    }

    public function preProcess(Vtiger_Request $request)
    {
        return false;
    }

    public function postProcess(Vtiger_Request $request)
    {
        return false;
    }

    public function process(Vtiger_request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }

    /**
     * Function exports the report in a Excel sheet
     *
     * @param Vtiger_Request $request
     */
    public function GetXLS(Vtiger_Request $request)
    {
        $rootDirectory = vglobal('root_directory');
        $tmpDir        = vglobal('tmp_dir');
        $tempFileName = tempnam($rootDirectory.$tmpDir, 'xls');
        $fileName     = decode_html($this->fileName).'.xls';
        $this->writeReportToExcelFile($tempFileName, $request->get('start_date'));
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        }
        header('Content-Type: application/x-msexcel');
        header('Content-Length: '.@filesize($tempFileName));
        header('Content-disposition: attachment; filename="'.$fileName.'"');
        $fp = fopen($tempFileName, 'rb');
        fpassthru($fp);
    }

    public function getReportData($startDate)
    {
        $db          = PearDatabase::getInstance();
        $valuesArray = [];
        $query = "SELECT vtiger_orderstask.*, vtiger_employees.name as Employee,  vtiger_vehicles.name as Vehicle FROM vtiger_orderstask
                        INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid
                        LEFT JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.crmid
                        LEFT JOIN vtiger_employees ON vtiger_crmentityrel.relcrmid = vtiger_employees.employeesid
                        LEFT JOIN vtiger_vehicles ON vtiger_crmentityrel.relcrmid = vtiger_vehicles.vehiclesid
                        WHERE deleted = 0
                        AND startdate = ?";
        $result = $db->pquery($query, [$startDate]);
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $valuesArray[] = $row;
            }
        }

        return $valuesArray;
    }

    public function writeReportToExcelFile($fileName, $startDate)
    {
        require_once("libraries/PHPExcel/PHPExcel.php");
        $workbook  = new PHPExcel();
        $worksheet = $workbook->setActiveSheetIndex(0);
        $arr_val = $this->getReportData($startDate);
        $header_styles = [
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'E1E0F7']],
            //'font' => array( 'bold' => true )
        ];
        if (isset($arr_val)) {
            $count    = 0;
            $rowcount = 1;
            //copy the first value details
            $arrayFirstRowValues = $arr_val[0];
            foreach ($arrayFirstRowValues as $key => $value) {
                $worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $key, true);
                $worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);
                // NOTE Performance overhead: http://stackoverflow.com/questions/9965476/phpexcel-column-size-issues
                //$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);
                $count = $count + 1;
            }
            $rowcount++;
            foreach ($arr_val as $key => $array_value) {
                $count = 0;
                foreach ($array_value as $hdr => $value) {
                    if ($hdr == 'ACTION') {
                        continue;
                    }
                    $value = decode_html($value);
                    // TODO Determine data-type based on field-type.
                    // String type helps having numbers prefixed with 0 intact.
                    $worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                    $count = $count + 1;
                }
                $rowcount++;
            }
        }
        $workbookWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $workbookWriter->save($fileName);
    }
}
