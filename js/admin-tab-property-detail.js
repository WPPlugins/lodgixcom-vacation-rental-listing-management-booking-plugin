(function($) {

    $(function() {
        $(document).on("shown.bs.collapse shown.bs.tab", ".panel-collapse, a[data-toggle='tab']", onTabClick);
        $('#ldgxSettingsTabs')
            .on('shown-accordion.bs.tabcollapse', onTabAccordionSwitch)
            .on('shown-tabs.bs.tabcollapse', onTabAccordionSwitch);
        $('#p_lodgix_single_page_design').on('change', applyPageDesign);
        $('#p_lodgix_display_title').on('change', applyTitle);
        $('#p_lodgix_display_daily_rates').on('click', showDailyRates);
        $('#p_lodgix_display_weekly_rates').on('click', showWeeklyRates);
        $('#p_lodgix_display_monthly_rates').on('click', showMonthlyRates);
        $('#p_lodgix_display_property_book_now_always').on('click', showBookNowOffline);
        $('.ldgxSettingsContactUrl input').on('input', updateContactUrl);
        $('#p_lodgix_single_page_tab_details_is_visible').on('click', updateTabTitleDetails);
        $('#p_lodgix_single_page_tab_details').on('input', updateTabTitleDetails);
        $('#p_lodgix_single_page_tab_calendar_is_visible').on('click', updateTabTitleCalendar);
        $('#p_lodgix_single_page_tab_calendar').on('input', updateTabTitleCalendar);
        $('#p_lodgix_single_page_tab_location_is_visible').on('click', updateTabTitleLocation);
        $('#p_lodgix_single_page_tab_location').on('input', updateTabTitleLocation);
        $('#p_lodgix_single_page_tab_amenities_is_visible').on('click', updateTabTitleAmenities);
        $('#p_lodgix_single_page_tab_amenities').on('input', updateTabTitleAmenities);
        $('#p_lodgix_single_page_tab_policies_is_visible').on('click', updateTabTitlePolicies);
        $('#p_lodgix_single_page_tab_policies').on('input', updateTabTitlePolicies);
        $('#p_lodgix_single_page_tab_reviews_is_visible').on('click', updateTabTitleReviews);
        $('#p_lodgix_single_page_tab_reviews').on('input', updateTabTitleReviews);
        $('#p_lodgix_image_size').on('change', setImageSize);
        $('#p_lodgix_display_beds').on('click', showBedrooms);
        $('#p_lodgix_display_single_instructions').on('click', showInstructionsSingleUnit);
        $('#p_lodgix_gmap_zoom_level').on('change', setMapZoom);
        $('#p_lodgix_rates_display').on('change', showMergedRates);
    });

    var isPreviewInitialized = false;

    function onTabClick (event) {
        var tabId = event.target.id;
        if (tabId == 'ldgxSettingsTabPropDetail' || tabId == 'ldgxSettingsPanelPropDetail-collapse') {
            if (!isPreviewInitialized) {
                setTimeout(applyPageDesign, 0);
                isPreviewInitialized = true;
            }
            applyIconSet();
        }
    }

    function onTabAccordionSwitch () {
        if (isPreviewInitialized) {
            initWidgets();
        }
    }

    function applyPageDesign () {
        var val = $('#p_lodgix_single_page_design').val();
        if (val == 1) {
            // Tabs
            $('.ldgxPropertySingle').hide();
            $('.ldgxPropertyTabs').show();
            $('.ldgxSettingsPreviewTabsDetails').append($('.ldgxSettingsPreviewContentDetails'));
            $('.ldgxSettingsPreviewTabsCalendar').append($('.ldgxSettingsPreviewContentCalendar'));
            $('.ldgxSettingsPreviewTabsLocation').append($('.ldgxSettingsPreviewContentLocation'));
            $('.ldgxSettingsPreviewTabsAmenities').append($('.ldgxSettingsPreviewContentAmenities'));
            $('.ldgxSettingsPreviewTabsPolicies').append($('.ldgxSettingsPreviewContentPolicies'));
            $('.ldgxSettingsPreviewTabsReviews').append($('.ldgxSettingsPreviewContentReviews'));
            initTabs();
        } else {
            // Single
            $('.ldgxPropertyTabs').hide();
            $('.ldgxPropertySingle').show();
            $('.ldgxSettingsPreviewSingleDetails').append($('.ldgxSettingsPreviewContentDetails'));
            $('.ldgxSettingsPreviewSinglePolicies').append($('.ldgxSettingsPreviewContentPolicies'));
            $('.ldgxSettingsPreviewSingleCalendar').append($('.ldgxSettingsPreviewContentCalendar'));
            $('.ldgxSettingsPreviewSingleLocation').append($('.ldgxSettingsPreviewContentLocation'));
            $('.ldgxSettingsPreviewSingleAmenities').append($('.ldgxSettingsPreviewContentAmenities'));
            $('.ldgxSettingsPreviewSingleReviews').append($('.ldgxSettingsPreviewContentReviews'));
            initWidgets();
        }
        applyTitle();
        showDailyRates();
        showWeeklyRates();
        showMonthlyRates();
        showBookNowOffline();
        updateContactUrl();
        updateTabTitleDetails();
        updateTabTitleCalendar();
        updateTabTitleLocation();
        updateTabTitleAmenities();
        updateTabTitlePolicies();
        updateTabTitleReviews();
        setImageSize();
        showBedrooms();
        showInstructionsSingleUnit();
        setMapZoom();
        showMergedRates();
    }

    var isTabsInitialized = false;

    function initTabs () {
        if (!isTabsInitialized) {
            jQueryLodgix('.ldgxTabbedContent').tabs({
                create: function () {
                    initWidgets();
                },
                show: function(event, ui) {
                    if (ui.index == 1 && typeof lodgixUnitCalendarInstance != "undefined") {
                        lodgixUnitCalendarInstance.resize();
                    }
                }
            });
            isTabsInitialized = true;
        } else {
            initWidgets();
        }
    }

    var isSliderInitialized = false;

    function initSlider () {
        if (!isSliderInitialized) {
            LodgixSlider.init('.ldgxSlider');
            isSliderInitialized = true;
        } else {
            LodgixSlider.resize();
        }
    }

    var isMapInitialized = false;

    function initMap () {
        if (typeof lodgixMap != 'undefined') {
            if (!isMapInitialized) {
                lodgixMap.init();
                isMapInitialized = true;
            } else {
                lodgixMap.resize();
            }
        }
    }

    function initWidgets () {
        initSlider();
        initMap();
    }

    function applyIconSet () {
        var val = $('#p_lodgix_icon_set').val();
        var cls = val.replace(/\s+/g, '');
        $('.ldgxButtonMap').attr('class', 'ldgxButton ldgxButtonMap ldgxButtonMap' + cls);
        $('.ldgxButtonVideo').attr('class', 'ldgxButton ldgxButtonVideo ldgxButtonVideo' + cls);
        $('.ldgxButtonTour').attr('class', 'ldgxButton ldgxButtonTour ldgxButtonTour' + cls);
        $('.ldgxButtonMail').attr('class', 'ldgxButton ldgxButtonMail ldgxButtonMail' + cls);
        $('.ldgxButtonPetsNo').attr('class', 'ldgxButton ldgxButtonPetsNo ldgxButtonPetsNo' + cls);
        $('.ldgxButtonSmokeNo').attr('class', 'ldgxButton ldgxButtonSmokeNo ldgxButtonSmokeNo' + cls);
    }

    function applyTitle () {
        if ($('#p_lodgix_display_title').val() == 'name') {
            $('.ldgxPropBadgeName').html($('#ldgxSettingsPropertyName').html());
        } else {
            $('.ldgxPropBadgeName').html($('#ldgxSettingsPropertyTitle').html());
        }
    }

    function showDailyRates () {
        if ($('#p_lodgix_display_daily_rates').attr('checked')) {
            $('.ldgxPropBadgeRatesDaily').show();
        } else {
            $('.ldgxPropBadgeRatesDaily').hide();
        }
    }

    function showWeeklyRates () {
        if ($('#p_lodgix_display_weekly_rates').attr('checked')) {
            $('.ldgxPropBadgeRatesWeekly').show();
        } else {
            $('.ldgxPropBadgeRatesWeekly').hide();
        }
    }

    function showMonthlyRates () {
        if ($('#p_lodgix_display_monthly_rates').attr('checked')) {
            $('.ldgxPropBadgeRatesMonthly').show();
        } else {
            $('.ldgxPropBadgeRatesMonthly').hide();
        }
    }

    function showBookNowOffline () {
        if ($('#p_lodgix_display_property_book_now_always').attr('checked')) {
            $('.ldgxBookNowOffline').show();
        } else {
            $('.ldgxBookNowOffline').hide();
        }
    }

    function updateContactUrl () {
        var inputs = $('.ldgxSettingsContactUrl input');
        var len = inputs.length;
        var isEmpty = true;
        var val;
        for (var i = 0; i < len; i++) {
            val = inputs[i].value;
            if (val.length) {
                $('.ldgxPropBadge a.ldgxButtonMail').attr('href', val);
                isEmpty = false;
                break;
            }
        }
        if (isEmpty) {
            $('.ldgxPropBadge a.ldgxButtonMail').hide();
        } else {
            $('.ldgxPropBadge a.ldgxButtonMail').show();
        }
    }

    function updateTabTitleDetails () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_details',
            '#p_lodgix_single_page_tab_details_is_visible',
            '.ldgxTabDetails a',
            '.ldgxMobileTabDetails'
        );
    }

    function updateTabTitleCalendar () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_calendar',
            '#p_lodgix_single_page_tab_calendar_is_visible',
            '.ldgxTabCalendar a',
            '.ldgxMobileTabCalendar'
        );
    }

    function updateTabTitleLocation () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_location',
            '#p_lodgix_single_page_tab_location_is_visible',
            '.ldgxTabLocation a',
            '.ldgxMobileTabLocation'
        );
    }

    function updateTabTitleAmenities () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_amenities',
            '#p_lodgix_single_page_tab_amenities_is_visible',
            '.ldgxTabAmenities a',
            '.ldgxMobileTabAmenities'
        );
    }

    function updateTabTitlePolicies () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_policies',
            '#p_lodgix_single_page_tab_policies_is_visible',
            '.ldgxTabPolicies a',
            '.ldgxMobileTabPolicies'
        );
    }

    function updateTabTitleReviews () {
        updateTabTitle(
            '#p_lodgix_single_page_tab_reviews',
            '#p_lodgix_single_page_tab_reviews_is_visible',
            '.ldgxTabReviews a',
            '.ldgxMobileTabReviews'
        );
    }

    function updateTabTitle (selInput, selVisible, selTitle, selTitleMobile) {
        var val = $(selInput).val();
        if (val && val.length && $(selVisible).attr('checked')) {
            $(selTitle).text(val).removeClass('ldgxTabHidden');
            $(selTitleMobile).text(val).removeClass('ldgxTabMobileHidden');
        } else {
            val = 'Not Visible';
            $(selTitle).text(val).addClass('ldgxTabHidden');
            $(selTitleMobile).text(val).addClass('ldgxTabMobileHidden');
        }
    }

    function setImageSize () {
        var val = $('#p_lodgix_image_size').val();
        if (val == '640x480') {
            $('.ldgxSliderWrapper').css('max-width', '640px');
        } else if (val == '800x600') {
            $('.ldgxSliderWrapper').css('max-width', '800px');
        } else {
            $('.ldgxSliderWrapper').css('max-width', 'none');
        }
        initSlider();
    }

    function showBedrooms () {
        if ($('#p_lodgix_display_beds').attr('checked')) {
            $('.ldgxPropertyInfoBeds').show();
        } else {
            $('.ldgxPropertyInfoBeds').hide();
        }
    }

    function showInstructionsSingleUnit () {
        if ($('#p_lodgix_display_single_instructions').attr('checked')) {
            $('.ldgxCalendarHelpProperty').show();
        } else {
            $('.ldgxCalendarHelpProperty').hide();
        }
    }

    function setMapZoom () {
        if (typeof lodgixMap != 'undefined') {
            var val = parseInt($('#p_lodgix_gmap_zoom_level').val());
            if (!isNaN(val)) {
                if (val < 1) {
                    val = 13;
                }
                lodgixMap.setZoom(val);
                initMap();
            }
        }
    }

    function showMergedRates () {
        var val = $('#p_lodgix_rates_display').val();
        if (val == '0') {
            $('.ldgxPropertyRatesMerged').hide();
            $('.ldgxPropertyRatesSimple').show();
            $('.ldgxPropertyBlockRates').show();
        } else if (val == '1') {
            $('.ldgxPropertyRatesSimple').hide();
            $('.ldgxPropertyRatesMerged').show();
            $('.ldgxPropertyBlockRates').show();
        } else {
            $('.ldgxPropertyBlockRates').hide();
        }
    }

})(jQLodgix);
