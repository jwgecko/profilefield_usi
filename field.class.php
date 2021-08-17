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
 * User profile field class.
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/user/profile/field/usi/classes/usiverification.class.php");
require_once("$CFG->dirroot/user/profile/field/usi/classes/luhn.class.php");
require_once("$CFG->dirroot/user/profile/field/usi/locallib.php");

use user_profile_field_usi\Luhn;
use user_profile_field_usi\USIVerification;

class profile_field_usi extends profile_field_base {

    /**
     * Translate the USI exemption code to the relevant USI value for display.
     * @return string
     */
    public function display_data() {
        $this->data = translate_exemption_to_usi($this->data);
        $options = new stdClass();
        $options->para = false;
        return format_text($this->data, FORMAT_MOODLE, $options);
    }

    /**
     * Translate the USI exemption code to the relevant USI value for editing.
     *
     * @param stdClass $user
     */
    public function edit_load_user_data($user) {
        if ($this->data !== null) {
            $this->data = translate_exemption_to_usi($this->data);
            $user->{$this->inputname} = $this->data;
        }
    }

    public function edit_field_set_default($mform) {
        if (strstr($this->data, USI_EXEMPT_PREFIX) !== false) {
            $mform->setDefault(
                'usiexempt',
                'checked'
            );
        }
    }

    /**
     * Add 3 elements to the form:
     * - USI value itself
     * - 'I am exempt' checkbox
     * - Exemption reason checkbox (only shown if 'I am Exempt' is chosen)
     *
     * @param moodleform $mform
     * @throws coding_exception
     */
    public function edit_field_add($mform) {
        $mform->addElement(
            'text',
            $this->inputname,
            format_string($this->field->name),
            'maxlength="10" size="15" id="profilefield_usi"'
        );
        $mform->setType($this->inputname, PARAM_TEXT);

        // Exempt checkbox.
        $mform->addElement(
            'checkbox',
            'usiexempt',
            get_string('usiexempt', 'profilefield_usi')
        );

        // Exemption reason drop down (only shown if exempt checkbox is checked).
        $usiexemptionreasons = [
            USI_EXEMPT_INTERNATIONAL => get_string(
                'usiexemptioninternational',
                'profilefield_usi'
            ),
            USI_EXEMPT_INDIVIDUAL => get_string(
                'usiexemptionindividual',
                'profilefield_usi'
            ),
            USI_EXEMPT_COURSE => get_string(
                'usiexemptioncompleted',
                'profilefield_usi'
            ),
            USI_EXEMPT_DEFENCE => get_string(
                'usiexemptiondefence',
                'profilefield_usi'
            )
        ];

        $mform->addElement(
            "select",
            "usiexemptionreason",
            get_string("usiexemptionreason", "profilefield_usi"),
            $usiexemptionreasons
        );

        // If the 'I am exempt' checkbox is enabled show exemption reason.
        $mform->hideIf(
            'usiexemptionreason',
            'usiexempt',
            'notchecked'
        );

        // If the 'I am exempt' checkbox is enabled hide USI and disable it.
        $mform->hideIf(
            $this->inputname,
            'usiexempt',
            'checked'
        );

        $mform->disabledIf(
            $this->inputname,
            'usiexempt',
            'checked'
        );
    }

    /**
     * Validate the field for a user.
     *
     * @param stdClass $usernew
     * @return array|string
     */
    public function edit_validate_field($usernew) {
        if (isset($usernew->usiexempt) && $usernew->usiexempt) {
            if (!$usernew->usiexemptionreason) {
                $return[$this->inputname] = get_string(
                    'usiexemptionreasonmissing',
                    'profilefield_usi'
                );
                return $return;
            }
        } else if (isset($usernew->{$this->inputname})) {
            $response = $this->validate_usi($usernew->{$this->inputname});
            if ($response['error']) {
                $return[$this->inputname] = $response['message'];
                return $return;
            }

            if ($this->already_exists($usernew->{$this->inputname}, $usernew->id)) {
                $return[$this->inputname] = get_string(
                    'usiinvalidalreadyexists',
                    'profilefield_usi'
                );
            }
        }

        return [];
    }

    /**
     * Save exemption value if USI exempt in the USI field.
     *
     * @param stdClass $usernew
     * @return mixed|void
     */
    public function edit_save_data($usernew) {
        $usernew->{$this->inputname} = strtoupper($usernew->{$this->inputname});
        if ($usernew->usiexempt) {
            $usernew->{$this->inputname} = USI_EXEMPT_PREFIX . $usernew->usiexemptionreason;
        }
        parent::edit_save_data($usernew);
    }

    /**
     * Check if the USI already exists in the database for another user.
     *
     * @param $usi
     * @param $currentuserid
     * @return bool
     * @throws dml_exception
     */
    private function already_exists($usi, $currentuserid) {
        global $DB;

        $q = "select count(uid.data) as usi
              from {user_info_data} uid inner join {user_info_field} uif
              on uid.fieldid = uif.id
              where uif.datatype = :datatype
              and uid.data = :usi
              and uid.userid != :currentuserid";

        $p = array(
            'datatype' => 'usi',
            'usi' => $usi,
            'currentuserid' => $currentuserid
        );

        $usicount = $DB->count_records_sql($q, $p);
        if ($usicount > 0) {
            return true;
        }

        return false;
    }

    /**
     * USI validation.
     *
     * @param $usi
     * @return array
     * @throws moodle_exception
     */
    private function validate_usi($usi) {
        if (is_null($usi) || empty($usi)) {
            return []; // A USI can be blank.
        }

        if (strlen($usi) != 10) {
            return $this->validation_response(true, 'usiinvalidlength');
        }

        // Alphanumeric only, A-Z excluding letters I and O, 2-9 excluding numbers 0, 1.
        if (preg_match('/[^a-h\-j-n\-p-z_\-2-9]/i', $usi)) {
            return $this->validation_response(true, 'usiinvalidcharacters');
        }

        $luhn = new Luhn();
        if (!$luhn->validate_checksum($usi)) {
            return $this->validation_response(true, 'usiinvalidchecksum');
        }

        // Check for AVETMISS Done API token to determine if USI verification can occur.
        $apitoken = $this->field->param1;
        $server = $this->field->param2;

        if (!empty($apitoken)) {

            $apirequest = new USIVerification($apitoken, $server);
            if (!$apirequest->valid_token()) {
                return $this->validation_response(true, 'apiinvalidtoken');
            }

            if (!isset($_POST['profile_field_dob'])) {
                return $this->validation_response(true, 'apirequiresdob');
            }

            $month = str_pad($_POST['profile_field_dob']['month'], 2, '0', STR_PAD_LEFT);
            $day = str_pad($_POST['profile_field_dob']['day'], 2, '0', STR_PAD_LEFT);
            $dob = $_POST['profile_field_dob']['year'] . '-' . $month . '-' . $day;

            $userdata = [
                'USI' => $usi,
                'FirstName' => $_POST['firstname'],
                'FamilyName' => $_POST['lastname'],
                'DateOfBirth' => $dob
            ];

            // Verify USI using AVETMISS Done API.
            return $apirequest->verify_usi($userdata);
        }

    }

    /**
     * Return the validation response in a standard format
     * indicating if there is an error, and the relevant
     * message from the language pack.
     *
     * @param $error
     * @param $messagekey
     * @return array
     * @throws coding_exception
     */
    private function validation_response($error, $messagekey) {
        return [
            'error' => $error,
            'message' => get_string($messagekey, 'profilefield_usi')
        ];
    }
}
