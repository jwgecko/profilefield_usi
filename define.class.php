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
 * Form definition class.
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/user/profile/field/usi/classes/usiverification.class.php");

use user_profile_field_usi\USIVerification;

class profile_define_usi extends profile_define_base {

    /**
     * Define additional options for the field, e.g. to
     * specify the AVETMISS Done API Token.
     *
     * @param moodleform $form
     * @throws coding_exception
     */
    public function define_form_specific($form) {
        // AVETMISS done API key data entry field.
        $form->addElement(
            'text',
            'param1',
            get_string('apitoken', 'profilefield_usi'),
            'size="40"'
        );
        $form->setType('param1', PARAM_TEXT);

        // AVETMISS done API server data entry field.
        $form->addElement(
            'text',
            'param2',
            get_string('apiserver', 'profilefield_usi'),
            'size="40"'
        );
        $form->setDefault('param2', 'https://adts.avetmissfree.com');

    }

    /**
     * Validate the data from the add/edit profile field form that is common to all data types.
     *
     * Generally this method should not be overwritten by child classes.
     *
     * @param stdClass|array $data from the add/edit profile field form
     * @param $files
     * @return array
     * @throws coding_exception|moodle_exception
     */
    public function define_validate_common($data, $files) {
        $err = [];

        if (isset($data->param1) && strlen($data->param1) > 0) {
            $apirequest = new USIVerification($data->param1);
            if (!$apirequest->valid_token()) {
                $err['param1'] = get_string('apiinvalidtoken', 'profilefield_usi');
            }
        }

        return $err;
    }
}
