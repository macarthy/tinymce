<?php

/**
 * Filename:       assets/snippets/ditto/nederlands.inc.php
 * Function:       Dutch language file for Ditto.
 * Author:         The MODx Project
 * Date:           2006/07/2
 * Version:        1.0.2
 * MODx version:   0.9.5
*/

// NOTE: New language keys should added at the bottom of this page

$_lang['file_does_not_exist'] = " bestaat niet. Controleer a.u.b. het bestand.";

$_lang['default_template'] = '
    <div class="ditto_summaryPost">
        <h3><a href="[~[+id+]~]">[+title+]</a></h3>
        <div>[+summary+]</div>
        <p>[+link+]</p>
        <div style="text-align:right;">door <strong>[+author+]</strong> op [+date+]</div>
    </div>
';

$_lang['blank_tpl'] = "is leeg of u heeft een typefout in de chunk naam. Controleer dit a.u.b.";

$_lang['missing_placeholders_tpl'] = 'E&#233;n van uw Ditto templates mist placeholders, controleer a.u.b. de onderstaande template: <br /><br /><hr /><br /><br />';

$_lang['missing_placeholders_tpl_2'] = '<br /><br /><hr /><br />';

$_lang['default_splitter'] = "<!-- splitter -->";

$_lang['more_text'] = "Lees meer...";

$_lang['no_entries'] = '<p>Geen gegevens gevonden.</p>';

$_lang['date_format'] = "%d-%b-%y %H:%M";

$_lang['archives'] = "Archieven";

$_lang['prev'] = "&lt; Vorige";

$_lang['next'] = "Volgende &gt;";

$_lang['button_splitter'] = "|";

$_lang['default_copyright'] = "[(site_name)] 2006";	

$_lang['rss_lang'] = "nl";

$_lang['debug_summarized'] = "Aantal verwacht in dit overzicht (summarize):";

$_lang['debug_returned'] = "<br />Totaal aantal terug verwacht:";

$_lang['debug_retrieved_from_db'] = "Telling van totaal in db:";

$_lang['debug_sort_by'] = "Gesorteerd door (sortBy):";

$_lang['debug_sort_dir'] = "Sorteer richting (sortDir):";

$_lang['debug_start_at'] = "Start bij";

$_lang['debug_stop_at'] = "en stop bij";

$_lang['debug_out_of'] = "van";

$_lang['debug_document_data'] = "Documentgegevens voor ";

$_lang['default_archive_template'] = "<a href=\"[~[+id+]~]\">[+title+]</a> (<span class=\"ditto_date\">[+date+]</span>)";

$_lang['invalid_class'] = "De Ditto class is ongeldig. Controleer dit a.u.b.";

// New language key added 2-July-2006 to 5-July-2006

// Keys deprecated : $_lang['api_method'] and $_lang['GetAllSubDocs_method'] 

$_lang['tvs'] = "TV\'s:";

$_lang['api'] = "Gebruikt de nieuwe MODx 0.9.5 API";

?>