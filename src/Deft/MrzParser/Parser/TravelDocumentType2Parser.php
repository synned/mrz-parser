<?php

namespace Deft\MrzParser\Parser;

use Deft\MrzParser\Document\TravelDocument;
use Deft\MrzParser\Document\TravelDocumentInterface;
use Deft\MrzParser\Document\TravelDocumentType;
use Deft\MrzParser\Exception\ParseException;

/**
 * Parser of "travel document type 2" (td2) documents. Below a reference to the format:
 *
 *   01 - 02: Document code
 *   03 - 05: Issuing state or organization
 *   06 - 36: Name
 *   37 - 45: Document number
 *   46 - 46: Check digit
 *   47 - 49: Nationality
 *   50 - 55: Date of birth
 *   56 - 56: Check digit
 *   57 - 57: Sex
 *   58 - 63: Date of expiry
 *   64 - 64: Check digit
 *   65 - 71: Optional data
 *   72 - 72: Check digit
 *
 *
 * @package Deft\MrzParser\Parser
 */
class TravelDocumentType2Parser extends AbstractParser
{
    /**
     * Extracts all the information from a MRZ string and returns a populated instance of TravelDocumentInterface
     *
     * @param $string
     * @return TravelDocumentInterface
     * @throws ParseException
     */
    public function parse($string)
    {
        if (!in_array($this->getToken($string, 1), ['I', 'A', 'C'])) {
            throw new ParseException("First character is not 'I', 'A', or 'C'");
        }

        $fields = [
            'type' => TravelDocumentType::ID_CARD,
            'issuingCountry' => $this->getToken($string, 3, 5),
            'documentNumber' => $this->getToken($string, 37, 45),
            'personalNumber' => $this->getToken($string, 65, 71),
            'dateOfBirth' => $this->getDateToken($string, 50),
            'sex' => $this->getToken($string, 57),
            'dateOfExpiry' => $this->getDateToken($string, 58),
            'nationality' => $this->getToken($string, 47, 49)
        ];

        $names = $this->getNames($string, 6, 36);
        $fields['primaryIdentifier'] = $names[0];
        $fields['secondaryIdentifier'] = $names[1];

        return new TravelDocument($fields);
    }
}
