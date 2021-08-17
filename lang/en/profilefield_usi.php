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
 * Language pack.
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Unique Student Identifier (USI)';

$string['apiinvalidtoken'] = 'Invalid API token.';
$string['apiquotareached'] = 'AVETMISS Done API USI verifications requests quota reached.';
$string['apirequestfailed'] = 'Request to AVETMISS Done API failed.';
$string['apirequestfailedcode'] = 'Request to AVETMISS Done API failed, HTTP response code {$a}';
$string['apirequiresdob'] = 'AVETMISS Done USI verification API requires date of birth field.';
$string['apiserver'] = 'AVETMISS Done API server.';
$string['apitoken'] = 'AVETMISS Done USI verification API token.';

$string['usi'] = 'USI';

$string['usideactivated'] = 'USI has been de-activated.';
$string['usiexempt'] = 'I am exempt';
$string['usiexemptionreason'] = 'Please specify your reason for being USI exempt (mandatory).';
$string['usiexemptioncompleted'] =
    'Course completed prior to introduction of USI system on 1 Jan 2015.';
$string['usiexemptiondefence'] =
    'RTO granted exemption for defence and security personnel.';
$string['usiexemptionindividual'] =
    'Individual student exemption granted and verified.';
$string['usiexemptioninternational'] =
    'International student where the course is studied entirely outside Australia.';
$string['usiexemptionreasonmissing'] = 'USI exemption reason is missing.';

$string['usiinvalidalreadyexists'] = 'USI already exists in Moodle for another user.';
$string['usiinvalidblank'] = 'Invalid USI, blank or empty.';
$string['usiinvalidcharacters'] = 'Invalid characters in USI.';
$string['usiinvalidchecksum'] = 'Invalid USI checksum please re-check USI.';
$string['usiinvalidlength'] = 'Invalid USI length, must be 10 characters.';

$string['usiinvalid'] = 'USI is invalid.';
$string['usimismatchdob'] = 'USI does not match date of birth in your Moodle user profile.';
$string['usimismatchname'] = 'USI does not match {$a} in your Moodle user profile.';
