<?php
/*
 * Custom Fields Global Shortcodes 
 *
 * Released under the terms and conditions of the
 * Apache License 2.0 (see LICENSE file or http://www.apache.org/licenses/LICENSE-2.0)
 *
 * Shortcodes used sitewide 
*/


if (!defined('e107_INIT')) {
    exit;
}

// using word customfield(s) could have a conflict with core customfields

class customfields_shortcodes extends e_shortcode
{
    public $override = false; // when set to true, existing core/plugin shortcodes matching methods below will be overridden.
  
}
