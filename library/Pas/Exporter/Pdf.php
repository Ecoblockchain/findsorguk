<?php
ini_set('memory_limit', '64M');

/**
 * This class is used for pdf export of results.
 *
 * @category Pas
 * @package Exporter
 * @version 1
 * @since 1/4/12
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @copyright Daniel Pett
 * @author Daniel Pett
 */
class Pas_Exporter_Pdf extends Pas_Exporter_Generate
{

    /** The format name
     * @access protected
     * @var string
     */
    protected $_format = 'pdf';

    /** The fields to return
     * @access protected
     * @var array
     */
    protected $_pdfFields = array(

        'id', 'old_findID', 'description',
        'fourFigure', 'gridref', 'county',
        'district', 'parish', 'knownas',
        'finder', 'smrRef', 'otherRef',
        'identifier', 'objecttype', 'broadperiod'
    );

    /** Constructor uses parent class
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /** Create the data for export
     * @access public
     * @return array
     */
    public function create()
    {
        $params = array_merge($this->_params, array('show' => 500, 'format' => 'pdf'));
        $this->_search->setFields($this->_pdfFields);
        $this->_search->setParams($params);
        $this->_search->execute();
        return $this->_clean($this->_search->processResults());
    }

    /** Clean the data
     * @access protected
     * @param array $data
     * @return array
     */
    protected function _clean(array $data)
    {
        $finalData = array();
        foreach ($data as $dat) {
            $record = array();
            foreach ($dat as $k => $v) {
                $clean = trim(strip_tags(utf8_decode($v)));
                $record[$k] = preg_replace( "/\r|\n/", "", $clean );
            }
            $finalData[] = $record;
        }
        return $finalData;
    }
}