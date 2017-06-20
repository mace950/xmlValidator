<?php

/**
 * Class XmlValidator
 */
class XmlValidator
{
    /**
     * Returns DomDocument if valid, if not
     * exception with error message
     *
     * @param string $stream
     * @param string $xsd
     * @param string $encoding
     *
     * @return \DOMDocument
     * @throws \Exception
     */
    public function validate($stream, $xsd, $encoding = 'utf-8')
    {
        libxml_use_internal_errors(true);

        $xml = new \DOMDocument('1.0', $encoding);
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->recover = true;

        $xml->loadXML($stream);

        if (false === $xml->schemaValidate($xsd)) {
            $errors = libxml_get_errors();
            $output = '';
            foreach ($errors as $error) {
                $output .= $this->formatError($error);
            }

            throw new \Exception("XML not valid:\n" . $output);
        }

        return $xml;
    }

    /**
     * @param \LibXMLError $error
     *
     * @return string
     */
    private function formatError($error)
    {
        $return = "";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .=    " in $error->file";
        }
        $return .= " on line $error->line\n";

        return "$return\n--------------------------------------------\n";
    }
}