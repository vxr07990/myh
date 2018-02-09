<?php

namespace MoveCrm;

class xmlBuilder
{

    /**
     * Built XML
     *
     * @var string
     */
    protected $xml;

    /**
     * Collection of XML tags that may be self-closing instead of omitted
     *
     * @var string[]
     */
    protected $selfClosingTags =    ['record_changes',
                                     'booking_agent',
                                     'origin_agent',
                                     'dest_agent',
                                     'carrier_agent',
                                     'rooms',
                                     'third_party',
                                     'operational_list',
                                     'pvo_driver_inventory',
                                    ];

    /**
     * Construct new instance. If hasRootTag is set to false, add IGCSync root tag.
     *
     * @param string[]	$array
     * @param bool		$hasRootTag
     */
    public function __construct(array $array, $hasRootTag=false, $hasXMLHeader = true)
    {
        if ($hasXMLHeader) {
            $this->xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        }
        if (!$hasRootTag) {
            $this->xml .= "<IGCSync>";
        }
        file_put_contents('logs/builtArray.log', date('Y-m-d H:i:s - ').print_r($array, true)."\n");

        $this->convertArrayToXML($array);

        if (!$hasRootTag) {
            $this->xml .= "</IGCSync>";
        }
    }

    private function checkKey($key)
    {
        if ($key == '') {
            LogUtils::LogToFile('LOG_XML_FAILS', 'Blank XML tag', true, FILE_APPEND, DEBUG_BACKTRACE_PROVIDE_OBJECT);
            throw new \InvalidArgumentException('XML tag cannot be blank');
        }
    }

    /**
     * Process each element of array and generate XML tags for each key.
     * Recursively handles array elements that are collections.
     *
     * @param string[]	$array
     */
    private function convertArrayToXML(array $array)
    {
        //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Entering convertArrayToXML\n", FILE_APPEND);
        foreach ($array as $key=>$value) {
            if (preg_match('/^[0-9]+$/',$key)) {
                $key = 'Key_Item #'. $key;
            }
            //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ').$key."\n", FILE_APPEND);
            $tags = explode(' ', $key);
            $isNumTag = false;
            if (strpos($key, '#') !== false) {
                $isNumTag = true;
                $numberedTags = explode('#', $key);
            }
            if (is_array($value) && count($value) > 0 && $isNumTag) {
                //Collection of numbered tags
                //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Collection of numbered tags found\n", FILE_APPEND);
                $this->checkKey($numberedTags[0]);
                $this->xml .= "<".$numberedTags[0].">";
                $this->convertArrayToXML($value);
                $this->xml .= "</".$numberedTags[0].">";
            } elseif (is_array($value) && count($value) > 0) {
                //Collection of tags
                //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Collection of tags found\n", FILE_APPEND);
                $this->checkKey($key);
                $this->checkKey($tags[0]);
                $this->xml .= "<$key>";
                $this->convertArrayToXML($value);
                $this->xml .= "</".$tags[0].">";
            } elseif (($value == '' || is_array($value)) && in_array($key, $this->selfClosingTags)) {
                //Self-closing tag
                //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Self-closing tag found\n", FILE_APPEND);
                $this->checkKey($key);
                $this->xml .= "<$key />";
            } elseif ($value == '' || is_array($value)) {
                //Do nothing
                //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Empty tag found\n", FILE_APPEND);
            } elseif ($isNumTag) {
                //Bottom level hashed tag
                $this->checkKey($numberedTags[0]);
                $this->xml .= "<".$numberedTags[0].">".htmlspecialchars($value, ENT_XML1, 'UTF-8', false)."</".$numberedTags[0].">";
            } else {
                //Single tag value
                //file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Single tag found\n", FILE_APPEND);
                $this->checkKey($key);
                $this->checkKey($tags[0]);
                $this->xml .= "<$key>".htmlspecialchars($value, ENT_XML1, 'UTF-8', false)."</".$tags[0].">";
            }
        }
    }

    /**
     * Returns as XML.
     *
     * @return string
     */
    public function getXML()
    {
        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Preparing to output XML\n", FILE_APPEND);
        return $this->xml;
    }

    /**
     * Convert the given array into XML
     *
     * @param string[]	$array
     * @param bool		$hasRootTag
     *
     * @return string
     */
    public static function build(array $array, $hasRootTag=false)
    {
        file_put_contents('logs/xml.log', date('Y-m-d H:i:s - ')."Entering build function\n", FILE_APPEND);
        $builder = new static($array, $hasRootTag);

        return $builder->getXML();
    }
}
