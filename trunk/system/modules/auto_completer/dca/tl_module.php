<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  leo@leo-unglaub.net, CyberSpectrum 2009 
 * @author     Leo Unglaub <leo@leo-unglaub.net>, Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    auto_completer 
 * @license    GNU/GPL 
 * @filesource
 */

// load helper file
require_once(TL_ROOT . '/system/modules/auto_completer/ac_helper.php');

// extends the table tl_modules for the auto completer
$GLOBALS['TL_DCA']['tl_module']['palettes']['search'] = str_replace(';{template_legend:hide}', ';{ajax_legend:hide},use_auto_completer;{template_legend:hide}', $GLOBALS['TL_DCA']['tl_module']['palettes']['search']);
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'use_auto_completer';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_auto_completer'] = 'auto_completer_override_global';

$GLOBALS['TL_DCA']['tl_module']['palettes']['auto_completer'] = '{title_legend},name,headline,type;{config_legend},jumpTo,auto_completer_override_global;{template_legend:hide},auto_complete_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][]=array('tl_module_auto_completer', 'modifyPalette');
						
// manipulate dca dynamically.
class tl_module_auto_completer extends Backend
{
	public function modifyPalette(DataContainer $dc) {
		if (!$dc->id)
			return;
		$obj = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
									->limit(1)
									->execute($dc->id);
		if ($obj->numRows)
		{
			if($obj->type=='auto_completer')
			{
				if($obj->auto_completer_override_global)
					$GLOBALS['TL_DCA']['tl_module']['palettes']['auto_completer'] = str_replace('auto_completer_override_global', 'auto_completer_override_global,auto_completer_min_length, auto_completer_max_choises,auto_complete_ignore_words,auto_completer_expert_settings', $GLOBALS['TL_DCA']['tl_module']['palettes']['auto_completer']);
				if($obj->auto_completer_expert_settings)
					$GLOBALS['TL_DCA']['tl_module']['palettes']['auto_completer'] = str_replace('auto_completer_expert_settings', 'auto_completer_expert_settings,auto_completer_mark_query,auto_completer_width,auto_completer_inject_choice,auto_completer_custom_choices,auto_completer_empty_choises, auto_completer_visible_choises,auto_completer_class_name,auto_completer_z_index,auto_completer_delay,auto_completer_observer_options,auto_completer_fx_options, auto_completer_auto_submit,auto_completer_overflow,auto_completer_overflow_margin,auto_completer_select_first,auto_completer_filter,auto_completer_filter_case,auto_completer_filter_subset,auto_completer_force_select,auto_completer_select_mode,auto_completer_choices_match,auto_completer_multiple,auto_completer_auto_trim,auto_completer_allow_dupes,auto_completer_cache,auto_completer_relative', $GLOBALS['TL_DCA']['tl_module']['palettes']['auto_completer']);
			} else {
				if($obj->auto_completer_override_global)
					$GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_auto_completer']=str_replace('auto_completer_override_global', 'auto_completer_override_global,auto_completer_min_length, auto_completer_max_choises, auto_complete_ignore_words, auto_completer_expert_settings', $GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_auto_completer']);
				else
					return;
				if($obj->auto_completer_expert_settings)
					$GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_auto_completer']=str_replace('auto_completer_expert_settings', 'auto_completer_expert_settings,auto_completer_mark_query,auto_completer_width,auto_completer_inject_choice,auto_completer_custom_choices,auto_completer_empty_choises, auto_completer_visible_choises,auto_completer_class_name,auto_completer_z_index,auto_completer_delay,auto_completer_observer_options,auto_completer_fx_options, auto_completer_auto_submit,auto_completer_overflow,auto_completer_overflow_margin,auto_completer_select_first,auto_completer_filter,auto_completer_filter_case,auto_completer_filter_subset,auto_completer_force_select,auto_completer_select_mode,auto_completer_choices_match,auto_completer_multiple,auto_completer_auto_trim,auto_completer_allow_dupes,auto_completer_cache,auto_completer_relative', $GLOBALS['TL_DCA']['tl_module']['subpalettes']['use_auto_completer']);
			}
		}
	}
}

// Definition der 2 Hauptcheckboxen "use_auto_completer" und "auto_completer_expert_settings"
$GLOBALS['TL_DCA']['tl_module']['fields']['use_auto_completer'] = 
array(
	'label' => &$GLOBALS['TL_LANG']['tl_module']['use_auto_completer'], 
	'exclude' => true, 
	'inputType' => 'checkbox', 
	'eval' => array('submitOnChange'=> true)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_override_global'] = 
array(
	'label' => &$GLOBALS['TL_LANG']['tl_module']['auto_completer_override_global'], 
	'exclude' => true, 
	'inputType' => 'checkbox', 
	'eval' => array('submitOnChange'=> true)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_expert_settings'] = 
array(
	'label' => &$GLOBALS['TL_LANG']['tl_module']['auto_completer_expert_settings'], 
	'exclude' => true, 
	'inputType' => 'checkbox', 
	'eval' => array('submitOnChange'=> true)
);


/**
 * Definition aller Elemente unter "use_auto_completer"
 */

// Mindestanzahl der Buchstaben damit mit der Vervollständigung begeonnen wird
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_min_length'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_min_length'], 
	'default'		=> '1',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'digit'
			)
	
);

// Maximale Anzahl der Suchvorschläge
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_max_choises'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_max_choises'], 
	'default'		=> '10',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'digit'
			)
	
);

// Wörter die aus der Suche ausgeschlossen werden sollen
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_complete_ignore_words'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_complete_ignore_words'], 
	'exclude'		=> true, 
	'inputType' 	=> 'textarea', 
	'save_callback'	=> array(array('ac_helper', 'save_ignore_words')),
	'load_callback'	=> array(array('ac_helper', 'load_ignore_words')),
	'eval' 			=> array(
			'tl_class'	=> 'clr'
			)
);

// need to mask it away, as we can not fetch the template group in Frontend.
if ((TL_MODE == 'BE') && isset($this))
{
// Template selection
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_complete_template'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_complete_template'], 
			'default'                 => 'mod_ac_search',
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => $this->getTemplateGroup('mod_ac_search'),
			'eval'                    => array('tl_class'=>'w50')
);
}

/**
 * Definition aller Elemente unter "auto_completer_expert_settings"
 */

// mark_query
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_mark_query'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_mark_query'], 
	'default'		=> 'true',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// width
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_width'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_width'], 
	'default'		=> 'inherit',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// inject_choice
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_inject_choice'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_inject_choice'], 
	'default'		=> 'null',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// auto_completer_custom_choices
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_custom_choices'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_custom_choices'], 
	'default'		=> 'null',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// auto_completer_empty_choises
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_empty_choises'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_empty_choises'], 
	'default'		=> 'null',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// auto_completer_visible_choises
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_visible_choises'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_visible_choises'], 
	'default'		=> 'true',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_class_name
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_class_name'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_class_name'], 
	'default'		=> 'autocompleter-choices',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// auto_completer_z_index
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_z_index'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_z_index'], 
	'default'		=> '42',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'digit'
			)
);

// auto_completer_delay
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_delay'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_delay'], 
	'default'		=> '400',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'digit'
			)
);

// auto_completer_observer_options
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_observer_options'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_observer_options'], 
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'textarea', 
	'eval' 			=> array(
			'tl_class'	=> 'clr',
			)
);

// auto_completer_observer_options
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_fx_options'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_fx_options'], 
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'textarea', 
	'eval' 			=> array(
			'tl_class'	=> 'clr',
			)
);

// auto_completer_auto_submit
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_auto_submit'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_auto_submit'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_overflow
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_overflow'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_overflow'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_overflow_margin
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_overflow_margin'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_overflow_margin'], 
	'default'		=> '25',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'text', 
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'digit'
			)
);

// auto_completer_select_first
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_select_first'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_select_first'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_filter
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_filter'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_filter'], 
	'default'		=> 'null',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'textarea',
	'eval' 			=> array(
			'tl_class'	=> 'clr',
			)
);

// auto_completer_filter_case
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_filter_case'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_filter_case'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_filter_subset
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_filter_subset'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_filter_subset'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_force_select
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_force_select'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_force_select'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_select_mode
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_select_mode'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_select_mode'], 
	'default'		=> 'true',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_choices_match
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_choices_match'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_choices_match'], 
	'default'		=> 'null',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'text',
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			)
);

// auto_completer_multiple
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_multiple'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_multiple'], 
	'default'		=> 'true',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_auto_trim
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_auto_trim'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_auto_trim'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_allow_dupes
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_allow_dupes'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_allow_dupes'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_cache
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_cache'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_cache'], 
	'default'		=> 'true',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')),
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

// auto_completer_relative
$GLOBALS['TL_DCA']['tl_module']['fields']['auto_completer_relative'] = 
array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['auto_completer_relative'], 
	'default'		=> 'false',
	'exclude'		=> true,
    'save_callback'	=> array(array('ac_helper', 'set_missing_default_values')), 
	'inputType' 	=> 'select',
	'options'  		=> array('true' => $GLOBALS['TL_LANG']['MSC']['yes'], 'false' => $GLOBALS['TL_LANG']['MSC']['no']),
	'eval' 			=> array(
			'tl_class'	=> 'w50',
			'rgxp'		=> 'alpha'
			)
);

?>