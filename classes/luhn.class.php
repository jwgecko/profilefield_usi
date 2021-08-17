<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Luhn Mod N checksum validation for USI.
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

namespace user_profile_field_usi;

class Luhn {

    protected $alphabetlength;
    protected $factor = 2;

    // Alphabet used for code generation.
    // Does not include numbers 0, 1 and Letters O, I.
    protected $alphabet = [
        '2', '3', '4', '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
        'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    protected $codelength = 10;
    protected $checksumlength = 9;

    public function __construct() {
        $this->alphabetlength = count($this->alphabet);
    }

    /**
     * Validate checksum.
     *
     * @param $code
     * @return bool
     */
    public function validate_checksum($code) {
        // Check the code is the right length.
        if (!isset($code[$this->codelength - 1])) {
            return false;
        }

        $checksum = $this->generate_checksum($this->remove_checksum($code));
        $checkcode = $this->attach_checksum($this->remove_checksum($code), $checksum);

        if ($checkcode === $code) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate checksum.
     *
     * @param $code
     * @return string
     */
    protected function generate_checksum($code) {
        $sum = 0;
        $codelength = strlen($code);
        $currentfactor = $this->factor;

        for ($i = $codelength - 1; $i >= ($codelength - $this->checksumlength); $i--) {
            $num = $i;
            $codepoint = array_search($code[$num], $this->alphabet);
            $addend = $currentfactor * $codepoint;

            // Alternate the factor that each point is multiplied by.
            $currentfactor = ($currentfactor == $this->factor) ? 1 : $this->factor;

            $addend = ($addend / $this->alphabetlength) + ($addend % $this->alphabetlength);
            $addend = intval($addend); // Take the integer value only.
            $sum += $addend;
        }

        $remainder = $sum % $this->alphabetlength;
        $checkcodepoint = ($this->alphabetlength - $remainder) % $this->alphabetlength;

        return $this->alphabet[$checkcodepoint];
    }

    /**
     * Attach checksum.
     *
     * @param $code
     * @param $checksum
     * @return string
     */
    protected function attach_checksum($code, $checksum) {
        return $code . $checksum;
    }

    /**
     * Remove checksum.
     *
     * @param $code
     * @return string
     */
    protected function remove_checksum($code) {
        return substr($code, 0, -1);
    }
}
