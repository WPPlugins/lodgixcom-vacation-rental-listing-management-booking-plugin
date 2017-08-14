<?php

class LodgixAvailability {

    function __construct($config, $language='en') {
        $this->ownerId = $config->get('p_lodgix_owner_id');
        $this->displayHelp = ($config->get('p_lodgix_display_multi_instructions') == 1);
        $this->language = $language;

        $properties = (new LodgixServiceProperties($config))->getAll();
        if ($properties) {
            $this->nProperties = count($properties);
            $this->property = $properties[0];
        } else {
            $this->nProperties = 0;
            $this->property = null;
        }

        if (is_ssl()) {
            $this->website = 'https://www.lodgix.com';
        } else {
            $this->website = 'http://www.lodgix.com';
        }
    }

    function page() {
        $html = '';
        if ($this->nProperties > 0) {
            $help = $this->calendarHelpText();
            if ($this->nProperties > 1) {
                $calendar = $this->multiUnitCalendar();
            } else {
                $calendar = $this->singleUnitCalendar();
            }
            $html .= <<<EOT
<div class="ldgxCalendar ldgxCalendarAvailability">
    $calendar
    $help
</div>
EOT;
        }
        return $html;
    }

    protected function multiUnitCalendar() {
        $html = <<<EOT
<script>
    var __lodgix_origin='$this->website';
</script>
<script src="$this->website/static/muc/build/code.min.js"></script>
<script>
    new LodgixCalendar('$this->ownerId', 0, true, '$this->language', encodeURIComponent(location.protocol + "//" + location.host));
</script>
EOT;
        return $html;
    }

    protected function singleUnitCalendar() {
        $html = '';
        if ($this->property) {
            $ownerId = $this->property->owner_id;
            $propertyId = $this->property->id;
            $html .= <<<EOT
<script>
    var __lodgix_origin='$this->website';
</script>
<script src="$this->website/static/scc/build/code.min.js"></script>
<script>
    var lodgixUnitCalendarInstance = new LodgixUnitCalendar($ownerId, $propertyId, '$this->language', encodeURIComponent(location.protocol + "//" + location.host));
</script>
EOT;
        }
        return $html;
    }

    protected function calendarHelpText() {
        $html = '';
        if ($this->displayHelp && $this->property && $this->property->allow_booking == 1) {
            $lsp = new LodgixServiceProperty($this->property, $this->language);
            $policies = $lsp->policies();
            if ($policies) {
                $text = $policies[0]->multi_unit_helptext;
                if ($text) {
                    $label = LodgixTranslate::translate('Online Booking Instructions');
                    $html .= <<<EOT
<div class="ldgxCalendarHelp ldgxCalendarHelpAvailability">
    <div class="ldgxCalendarHelpTitle ldgxCalendarHelpTitleAvailability">$label</div>
    <div class="ldgxCalendarHelpText ldgxCalendarHelpTextAvailability">$text</div>
</div>
EOT;
                }
            }
        }
        return $html;
    }

}
