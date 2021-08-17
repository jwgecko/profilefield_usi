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
 * Local plugin library functions.
 *
 * @package     profilefield
 * @subpackage  usi
 * @copyright   JWGecko https://jwgecko.com/
 */

defined('MOODLE_INTERNAL') || die();

const USI_EXEMPT_PREFIX = 'exempt_';
const USI_EXEMPT_COURSE = 'course';
const USI_EXEMPT_DEFENCE = 'defence';
const USI_EXEMPT_INTERNATIONAL = 'international';
const USI_EXEMPT_INDIVIDUAL = 'individual';

const USI_EXEMPT_BLANK = '';
const USI_EXEMPT_INDIV = 'INDIV';
const USI_EXEMPT_INTOFF = 'INTOFF';

function translate_exemption_to_usi($exemption) {
    switch ($exemption) {
        case USI_EXEMPT_PREFIX . USI_EXEMPT_DEFENCE:
        case USI_EXEMPT_PREFIX . USI_EXEMPT_INDIVIDUAL:
            return USI_EXEMPT_INDIV;
            break;
        case USI_EXEMPT_PREFIX . USI_EXEMPT_INTERNATIONAL:
            return USI_EXEMPT_INTOFF;
            break;
        case USI_EXEMPT_PREFIX . USI_EXEMPT_COURSE:
            return USI_EXEMPT_BLANK;
            break;
        default:
            return USI_EXEMPT_BLANK;
    }
}
