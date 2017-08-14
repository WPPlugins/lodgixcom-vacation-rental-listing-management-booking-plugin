(function($) {

    $(function() {
        $(document).on("shown.bs.collapse shown.bs.tab", ".panel-collapse, a[data-toggle='tab']", onTabClick);
        $('#p_lodgix_vacation_rentals_page_design').on('change', applyPageDesign);
        $('#p_lodgix_icon_set').on('change', applyIconSet);
        $('#p_lodgix_full_size_thumbnails').on('click', showFullSizeThumbnails);
        $('#p_lodgix_display_search_learn_more_book_now_button').on('click', showLearnMoreBookNow);
        $('#p_lodgix_display_search_areas').on('click', showAreas);
        $('#p_lodgix_display_search_text_expander').on('click', showTextExpander);
        $('#p_lodgix_display_search_bedrooms').on('click', showTable);
        $('#p_lodgix_display_search_bathrooms').on('click', showTable);
        $('#p_lodgix_display_search_guests').on('click', showTable);
        $('#p_lodgix_display_search_type').on('click', showTable);
        $('#p_lodgix_display_search_pets').on('click', showTable);
        $('#p_lodgix_display_search_daily_rates').on('click', showTable);
        $('#p_lodgix_display_search_weekly_rates').on('click', showTable);
        $('#p_lodgix_display_search_monthly_rates').on('click', showTable);
        $('#p_lodgix_display_search_table_high_rate').on('click', showTable);
        $('#p_lodgix_display_search_table_icons').on('click', applyIconSet);
        $('#p_lodgix_display_availability_icon').on('click', showButtons);
        $('#p_lodgix_display_icons').on('click', showButtons);
    });

    var isPreviewInitialized = false;

    function onTabClick (event) {
        var tabId = event.target.id;
        if (tabId == 'ldgxSettingsTabPropList' || tabId == 'ldgxSettingsPanelPropList-collapse') {
            if (!isPreviewInitialized) {
                $('#ldgxAdminPreviewPropList').show();
                setTimeout(applyPageDesign, 0);
                isPreviewInitialized = true;
            }
        }
    }

    function applyPageDesign () {
        var val = $('#p_lodgix_vacation_rentals_page_design').val();
        if (val == 1) {
            // Grid
            $('.ldgxInventoryList').hide();
            $('.ldgxInventoryGrid').show();
        } else {
            // Rows
            $('.ldgxInventoryGrid').hide();
            var el = $('.ldgxInventoryList');
            el.show();
            if (val == 0) {
                el.removeClass('ldgxInventoryTheme2').addClass('ldgxInventoryTheme1');
            } else if (val == 2) {
                el.removeClass('ldgxInventoryTheme1').addClass('ldgxInventoryTheme2');
            }
            showFullSizeThumbnails();
            showLearnMoreBookNow();
            showTextExpander();
            showButtons();
        }
        showAreas();
        showTable();
        showDisplayOptions();
    }

    function applyIconSet () {
        var val = $('#p_lodgix_icon_set').val();
        var cls = val.replace(/\s+/g, '');
        if ($('#p_lodgix_display_search_table_icons').attr('checked') && val != 'Old') {
            $('.ldgxListingFeats').attr('class', 'ldgxListingFeats ldgxListingFeatsIcons ldgxListingFeatsIcons' + cls);
        } else {
            $('.ldgxListingFeats').attr('class', 'ldgxListingFeats');
        }
        $('.ldgxPetsYes').attr('class', 'ldgxPetsYes ldgxPetsYes' + cls);
        $('.ldgxPetsNo').attr('class', 'ldgxPetsNo ldgxPetsNo' + cls);
        $('.ldgxButtonMap').attr('class', 'ldgxButton ldgxButtonMap ldgxButtonMap' + cls);
        $('.ldgxButtonMail').attr('class', 'ldgxButton ldgxButtonMail ldgxButtonMail' + cls);
    }

    function showFullSizeThumbnails () {
        if ($('#p_lodgix_full_size_thumbnails').attr('checked')) {
            $('.ldgxListingImg').hide();
            $('.ldgxListingName').hide();
            $('.ldgxListingBody').hide();
            $('.ldgxListingFullSizeImg').show();
            $('.ldgxListingFullSizeName').show();
            $('.ldgxListingFullSizeBody').show();
            $('#p_lodgix_display_search_text_expander').attr('disabled', true);
        } else {
            $('.ldgxListingFullSizeImg').hide();
            $('.ldgxListingFullSizeName').hide();
            $('.ldgxListingFullSizeBody').hide();
            $('.ldgxListingImg').show();
            $('.ldgxListingName').show();
            $('.ldgxListingBody').show();
            $('#p_lodgix_display_search_text_expander').attr('disabled', false);
        }
    }

    function showLearnMoreBookNow () {
        if ($('#p_lodgix_display_search_learn_more_book_now_button').attr('checked')) {
            $('.ldgxSlashBtnWrap').show();
        } else {
            $('.ldgxSlashBtnWrap').hide();
        }
    }

    function showAreas () {
        if ($('#p_lodgix_display_search_areas').attr('checked')) {
            $('.ldgxListingArea').show();
            $('.ldgxGridArea').show();
        } else {
            $('.ldgxListingArea').hide();
            $('.ldgxGridArea').hide();
        }
    }

    function showTextExpander () {
        if (!$('#p_lodgix_full_size_thumbnails').attr('checked')) {
            if ($('#p_lodgix_display_search_text_expander').attr('checked')) {
                jQueryLodgix('.ldgxListingDesc:visible').LodgixTextExpander();
            } else {
                jQueryLodgix('.ldgxListingDesc:visible').LodgixTextExpander('destroy');
            }
        }
    }

    function showTable () {
        if ($('#p_lodgix_vacation_rentals_page_design').val() == 1) {
            // Grid
            if ($('#p_lodgix_display_search_bedrooms').attr('checked')) {
                $('.ldgxGridBeds').show();
            } else {
                $('.ldgxGridBeds').hide();
            }
            var displayHighRate = $('#p_lodgix_display_search_table_high_rate').attr('checked') ? true : false;
            if ($('#p_lodgix_display_search_daily_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxGridRateDailyLow').hide();
                    $('.ldgxGridRateDailyHigh').show();
                } else {
                    $('.ldgxGridRateDailyHigh').hide();
                    $('.ldgxGridRateDailyLow').show();
                }
            } else {
                $('.ldgxGridRateDaily').hide();
            }
            if ($('#p_lodgix_display_search_weekly_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxGridRateWeeklyLow').hide();
                    $('.ldgxGridRateWeeklyHigh').show();
                } else {
                    $('.ldgxGridRateWeeklyHigh').hide();
                    $('.ldgxGridRateWeeklyLow').show();
                }
            } else {
                $('.ldgxGridRateWeekly').hide();
            }
            if ($('#p_lodgix_display_search_monthly_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxGridRateMonthlyLow').hide();
                    $('.ldgxGridRateMonthlyHigh').show();
                } else {
                    $('.ldgxGridRateMonthlyHigh').hide();
                    $('.ldgxGridRateMonthlyLow').show();
                }
            } else {
                $('.ldgxGridRateMonthly').hide();
            }
        } else {
            // Rows
            jQueryLodgix('.ldgxListingFeats').LodgixResponsiveTable('destroy');
            if ($('#p_lodgix_display_search_bedrooms').attr('checked')) {
                $('.ldgxListingFeatCellBeds').show();
            } else {
                $('.ldgxListingFeatCellBeds').hide();
            }
            if ($('#p_lodgix_display_search_bathrooms').attr('checked')) {
                $('.ldgxListingFeatCellBaths').show();
            } else {
                $('.ldgxListingFeatCellBaths').hide();
            }
            if ($('#p_lodgix_display_search_guests').attr('checked')) {
                $('.ldgxListingFeatCellGuests').show();
            } else {
                $('.ldgxListingFeatCellGuests').hide();
            }
            if ($('#p_lodgix_display_search_type').attr('checked')) {
                $('.ldgxListingFeatCellType').show();
            } else {
                $('.ldgxListingFeatCellType').hide();
            }
            if ($('#p_lodgix_display_search_pets').attr('checked')) {
                $('.ldgxListingFeatCellPets').show();
            } else {
                $('.ldgxListingFeatCellPets').hide();
            }
            var displayHighRate = $('#p_lodgix_display_search_table_high_rate').attr('checked') ? true : false;
            if ($('#p_lodgix_display_search_daily_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxListingFeatCellDailyLow').hide();
                    $('.ldgxListingFeatCellDailyHigh').show();
                } else {
                    $('.ldgxListingFeatCellDailyHigh').hide();
                    $('.ldgxListingFeatCellDailyLow').show();
                }
            } else {
                $('.ldgxListingFeatCellDaily').hide();
            }
            if ($('#p_lodgix_display_search_weekly_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxListingFeatCellWeeklyLow').hide();
                    $('.ldgxListingFeatCellWeeklyHigh').show();
                } else {
                    $('.ldgxListingFeatCellWeeklyHigh').hide();
                    $('.ldgxListingFeatCellWeeklyLow').show();
                }
            } else {
                $('.ldgxListingFeatCellWeekly').hide();
            }
            if ($('#p_lodgix_display_search_monthly_rates').attr('checked')) {
                if (displayHighRate) {
                    $('.ldgxListingFeatCellMonthlyLow').hide();
                    $('.ldgxListingFeatCellMonthlyHigh').show();
                } else {
                    $('.ldgxListingFeatCellMonthlyHigh').hide();
                    $('.ldgxListingFeatCellMonthlyLow').show();
                }
            } else {
                $('.ldgxListingFeatCellMonthly').hide();
            }
            jQueryLodgix('.ldgxListingFeats').LodgixResponsiveTable();
            applyIconSet();
        }
    }

    function showButtons () {
        var displayAvailability = $('#p_lodgix_display_availability_icon').attr('checked') ? true : false;
        var displayButtons = $('#p_lodgix_display_icons').attr('checked') ? true : false;
        if (displayAvailability || displayButtons) {
            if (displayAvailability) {
                $('.ldgxListingButsBlock1').show();
            } else {
                $('.ldgxListingButsBlock1').hide();
            }
            if (displayButtons) {
                $('.ldgxListingButsBlock2').show();
            } else {
                $('.ldgxListingButsBlock2').hide();
            }
            $('.ldgxListingButs').show();
        } else {
            $('.ldgxListingButs').hide();
        }
    }

    function showDisplayOptions () {
        if ($('#p_lodgix_vacation_rentals_page_design').val() == 1) {
            // Grid
            $('.ldgxSettingsRowsOnly').hide();
        } else {
            // Rows
            $('.ldgxSettingsRowsOnly').show();
        }
    }

})(jQLodgix);
