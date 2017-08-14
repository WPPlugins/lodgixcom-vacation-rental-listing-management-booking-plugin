<?php

class LodgixParser {

    function __construct($config) {
        global $wpdb;
        $this->db = $wpdb;
        $prefix = $this->db->prefix;
        $this->tableProperties = $prefix . LodgixConst::TABLE_PROPERTIES;
        $this->tableTags = $prefix . LodgixConst::TABLE_TAGS;
        $this->tablePropertyTags = $prefix . LodgixConst::TABLE_PROPERTY_TAGS;
        $this->tableCategories = $prefix . LodgixConst::TABLE_CATEGORIES;
        $this->tablePropertyCategories = $prefix . LodgixConst::TABLE_PROPERTY_CATEGORIES;
        $this->tableAmenities = $prefix . LodgixConst::TABLE_AMENITIES;
        $this->tableMergedRates = $prefix . LodgixConst::TABLE_MERGED_RATES;
        $this->tablePictures = $prefix . LodgixConst::TABLE_PICTURES;
        $this->tablePages = $prefix . LodgixConst::TABLE_PAGES;
        $this->tableLangPages = $prefix . LodgixConst::TABLE_LANG_PAGES;
        $this->tableLangProperties = $prefix . LodgixConst::TABLE_LANG_PROPERTIES;
        $this->tableLangAmenities = $prefix . LodgixConst::TABLE_LANG_AMENITIES;
        $this->tableTaxes = $prefix . LodgixConst::TABLE_TAXES;
        $this->tableFees = $prefix . LodgixConst::TABLE_FEES;
        $this->tableDeposits = $prefix . LodgixConst::TABLE_DEPOSITS;
        $this->tableReviews = $prefix . LodgixConst::TABLE_REVIEWS;
        $this->tableLanguages = $prefix . LodgixConst::TABLE_LANGUAGES;
        $this->tableLinkRotators = $prefix . LodgixConst::TABLE_LINK_ROTATORS;
        $this->tablePolicies = $prefix . LodgixConst::TABLE_POLICIES;
        $this->tableSearchableAmenities = $prefix . LodgixConst::TABLE_SEARCHABLE_AMENITIES;

        $this->config = $config;
    }

    function processFetchedData($ownerData, $propertyData) {
        $this->config->set('p_lodgix_date_format', strval($ownerData->DateTimeFormat->DateFormat));
        $this->config->set('p_lodgix_time_format', strval($ownerData->DateTimeFormat->TimeFormat));
        $this->config->save();

        $this->db->query("DELETE FROM $this->tablePolicies");
        $this->db->query("DELETE FROM $this->tableLinkRotators");
        $this->db->query("DELETE FROM $this->tableSearchableAmenities");
        $this->db->query("DELETE FROM $this->tableLangAmenities");

        $this->parseOwnerWebsite($ownerData->Website);
        $this->parseOwnerLanguages($ownerData->Languages->Language);
        $this->parseOwnerRotators($ownerData->Rotators->Rotator);
        $this->parseOwnerAmenities($ownerData->Amenities->Amenity);

        // Delete all records and reset autoincrement
        $this->db->query("TRUNCATE TABLE $this->tablePropertyTags");
        $this->db->query("TRUNCATE TABLE $this->tableTags");
        $this->db->query("TRUNCATE TABLE $this->tablePropertyCategories");
        $this->db->query("TRUNCATE TABLE $this->tableCategories");

        $activeProperties = Array(-1, -2, -3);
        if ($propertyData->Properties->Property) {
            $counter = 0;
            foreach ($propertyData->Properties->Property as $property) {
                if ($property->ServingStatus == 'ACTIVE' && $property->WordpressStatus == 'ACTIVE') {
                    $propertyId = strval($property->ID);

                    $this->db->query("DELETE FROM $this->tableAmenities WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableMergedRates WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tablePictures WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableLangProperties WHERE id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableTaxes WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableFees WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableDeposits WHERE property_id=$propertyId");
                    $this->db->query("DELETE FROM $this->tableReviews WHERE property_id=$propertyId");

                    $this->parseProperty($property, $counter);
                    $activeProperties[] = $propertyId;
                    $counter++;
                }
            }
        }
        $activeProperties = join(',', $activeProperties);
        $this->cleanProperties($activeProperties);

        if ($propertyData->Categories->Category) {
            foreach ($propertyData->Categories->Category as $category) {
                if ($category->WordpressStatus != 'PAUSED') {
                    $this->parseCategory($category);
                }
            }
        }
        (new LodgixServicePost($this->config))->deleteRemovedCategoryPosts();
    }

    protected function parseOwnerWebsite($website) {
        $a = Array(
            'language_code' => 'en',
            'cancellation_policy' => strval($website->CancellationPolicy),
            'deposit_policy' => strval($website->DepositPolicy),
            'single_unit_helptext' => strval($website->CalendarHelpText),
            'multi_unit_helptext' => strval($website->HTML5MultiCalendarHelpText),
            'post_slug_vacation_rentals' => sanitize_title_with_dashes($website->WordpressSlugVacationRentals, '', 'sav‌​e')
        );
        $this->db->query($this->arrayToQueryInsert($this->tablePolicies, $a));
    }

    protected function parseOwnerLanguages($languages) {
        if ($languages) {
            foreach ($languages as $language) {
                $a = Array(
                    'language_code' => strval($language->LanguageCode),
                    'cancellation_policy' => strval($language->CancellationPolicy),
                    'deposit_policy' => strval($language->DepositPolicy),
                    'single_unit_helptext' => strval($language->CalendarHelpText),
                    'multi_unit_helptext' => strval($language->HTML5MultiCalendarHelpText),
                    'post_slug_vacation_rentals' => sanitize_title_with_dashes($language->WordpressSlugVacationRentals, '', 'sav‌​e')
                );
                $this->db->query($this->arrayToQueryInsert($this->tablePolicies, $a));
            }
        }
    }

    protected function parseOwnerRotators($rotators) {
        if ($rotators) {
            foreach ($rotators as $rotator) {
                $a = Array(
                    'url' => strval($rotator->URL),
                    'title' => strval($rotator->Title)
                );
                $this->db->query($this->arrayToQueryInsert($this->tableLinkRotators, $a));
            }
        }
    }

    protected function parseOwnerAmenities($amenities) {
        if ($amenities) {
            $languages = $this->db->get_results("SELECT * FROM $this->tableLanguages WHERE enabled=1");
            foreach ($amenities as $amenity) {
                $amenityName = strval($amenity->Name);

                $a = Array(
                    'description' => $amenityName
                );
                $this->db->query($this->arrayToQueryInsert($this->tableSearchableAmenities, $a));

                foreach ($languages as $l) {
                    $translation = strval($amenity->{'Amenity' . strtoupper($l->code)});
                    if ($translation) {
                        $a = Array(
                            'description' => $amenityName,
                            'description_translated' => $translation,
                            'language_code' => strval($l->code),
                            'searchable' => 1
                        );
                        $this->db->query($this->arrayToQueryInsert($this->tableLangAmenities, $a));
                    }
                }
            }
        }
    }

    protected function parseProperty($property, $order) {
        $propertyId = strval($property->ID);
        $propertyName = strval($property->Name);
        $propertyTitle = strval($property->MarketingTitle);
        $propertyDescription = $this->config->get('p_lodgix_display_title') == 'name' ? $propertyName : $propertyTitle;
        $propertyDescriptionLong = strval($property->MarketingTeaser);
        $propertyDetails = html_entity_decode($property->Description);
        $postSlug = sanitize_title_with_dashes($property->WordpressSlug, '', 'sav‌​e');

        $propertyArray = Array(
            'order' => $order,
            'id' => $propertyId,
            'owner_id' => strval($property->OwnerID),
            'currency_symbol' => strval($property->CurrencyCode->attributes()->symbol),
            'currency_code' => strval($property->CurrencyCode),
            'serving_status' => $property->ServingStatus == 'PAUSED' || $property->WordpressStatus == 'PAUSED' ? 0 : 1,
            'display_calendar' => $property->DisplayCalendar == 'NO' ? 0 : 1,
            'allow_booking' => $property->AllowBooking == 'NO' ? 0 : 1,
            'allow_same_day_booking' => $property->AllowSameDayBooking == 'NO' ? 0 : 1,
            'limit_booking_days_advance' => strval($property->LimitBookingDaysAdvance),
            'property_name' => $propertyName,
            'property_title' => $propertyTitle,
            'description' => $propertyDescription,
            'description_long' => $propertyDescriptionLong,
            'details' => $propertyDetails,
            'bedrooms' => strval($property->Bedrooms),
            'bathrooms' => strval($property->Baths),
            'sleeps' => strval($property->MaxGuests),
            'web_address' => strval($property->URL),
            'proptype' => strval($property->PropertyType),
            'check_in' => strval($property->CheckIn),
            'check_out' => strval($property->CheckOut),
            'video_url' => strval($property->VideoURL),
            'virtual_tour_url' => strval($property->VirtualToursURL),
            'date_modified' => date('Y-m-d H:i:s'),
            'post_slug' => $postSlug
        );

        $this->parsePropertyAddress($propertyArray, $property->Address);
        $this->parsePropertyConditions($propertyArray, $property->Conditions->Condition);
        $this->parsePropertyBeds($propertyArray, $property->Beds->Bed);
        $this->parsePropertyPhotos($propertyArray, $propertyId, $property->Photos->Photo);

        $exists = (int)$this->db->get_var($this->db->prepare("SELECT COUNT(*) FROM $this->tableProperties WHERE id=$propertyId", null));
        if ($exists) {
            $this->db->query($this->arrayToQueryUpdate($this->tableProperties, $propertyArray, $propertyId));
        } else {
            $this->db->query($this->arrayToQueryInsert($this->tableProperties, $propertyArray));
        }

        $this->parsePropertyTags($propertyId, $property->Tags->Tag);
        $this->parsePropertyAmenities($propertyId, $property->Amenities->Amenity);

        $this->parsePropertyMergedRates($propertyId, $property->MergedRates->RatePeriod);

        $lowDailyRate = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(nightly), 0) FROM $this->tableMergedRates WHERE property_id=$propertyId", null));
        $lowWeekendRate = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(weekend_nightly), 0) FROM $this->tableMergedRates WHERE property_id=$propertyId", null));
        if ($lowWeekendRate < $lowDailyRate && $lowWeekendRate > 0) {
            $lowDailyRate = $lowWeekendRate;
        }
        $lowWeeklyRate = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(weekly), 0) FROM $this->tableMergedRates WHERE property_id=$propertyId", null));
        $lowMonthlyRate = (int)$this->db->get_var($this->db->prepare("SELECT IFNULL(MIN(monthly), 0) FROM $this->tableMergedRates WHERE property_id=$propertyId", null));
        $this->db->query("UPDATE $this->tableProperties SET min_daily_rate=$lowDailyRate,min_weekly_rate=$lowWeeklyRate,min_monthly_rate=$lowMonthlyRate WHERE id=$propertyId");

        $this->parsePropertyLanguages($propertyId, $propertyDescription, $propertyDescriptionLong, $propertyDetails, $property->Languages->Language);
        $this->parsePropertyTaxes($propertyId, $property->TaxesFeesDeposits->Taxes->Tax);
        $this->parsePropertyFees($propertyId, $property->TaxesFeesDeposits->Fees->Fee);
        $this->parsePropertyDeposits($propertyId, $property->TaxesFeesDeposits->Deposits->Deposit);
        $this->parsePropertyReviews($propertyId, $property->Reviews->Review);
        $this->parsePropertyCategories($propertyId, $property->Categories->CategoryId);
    }

    protected function parsePropertyAddress(&$propertyArray, $address) {
        $propertyArray['country_code'] = strval($address->Country->attributes()->code);
        $propertyArray['state'] = strval($address->State);
        $propertyArray['state_code'] = strval($address->State->attributes()->code);
        $propertyArray['address'] = strval($address->Street1);
        $street2 = strval($address->Street2);
        if ($street2) {
            $propertyArray['address'] .= "\n$street2";
        }
        $propertyArray['city'] = strval($address->City);
        $propertyArray['zip'] = strval($address->PostalCode);
        $propertyArray['latitude'] = $address->Latitude ? strval($address->Latitude) : '-1';
        $propertyArray['longitude'] = $address->Longitude ? strval($address->Longitude) : '-1';
    }

    protected function parsePropertyConditions(&$propertyArray, $conditions) {
        $propertyArray['smoking'] = 0;
        $propertyArray['pets'] = 0;
        $propertyArray['children'] = 0;
        if ($conditions) {
            foreach ($conditions as $condition) {
                $name = strtolower($condition->Name);
                if ($condition->Value == 'Allowed') {
                    $propertyArray[$name] = 1;
                }
            }
        }
    }

    protected function parsePropertyBeds(&$propertyArray, $beds) {
        $text = Array();
        if ($beds) {
            foreach ($beds as $bed) {
                $text[] = "$bed->Quantity $bed->Type(s)";
            }
        }
        $propertyArray['beds_text'] = join(', ', $text);
    }

    protected function parsePropertyPhotos(&$propertyArray, $propertyId, $photos) {
        $position = 1;
        if ($photos) {
            foreach ($photos as $photo) {
                if ($this->config->get('p_lodgix_use_ssl_pictures')) {
                    $url = str_replace('http://pictures.lodgix.com', 'https://d2ern41v4fpcqm.cloudfront.net', strval($photo->URL));
                    $thumbnailUrl = str_replace('http://pictures.lodgix.com', 'https://d2ern41v4fpcqm.cloudfront.net', strval($photo->ThumbnailURL));
                    if (array_key_exists('PreviewURL', $photo)) {
                        $previewUrl = str_replace('http://pictures.lodgix.com', 'https://d2ern41v4fpcqm.cloudfront.net', strval($photo->PreviewURL));
                    } else {
                        $previewUrl = null;
                    }
                } else {
                    $url = str_replace('http://www.lodgix.com', 'http://pictures.lodgix.com', strval($photo->URL));
                    $thumbnailUrl = str_replace('http://www.lodgix.com', 'http://pictures.lodgix.com', strval($photo->ThumbnailURL));
                    if (array_key_exists('PreviewURL', $photo)) {
                        $previewUrl = str_replace('http://www.lodgix.com', 'http://pictures.lodgix.com', strval($photo->PreviewURL));
                    } else {
                        $previewUrl = null;
                    }
                }
                if ($position == 1) {
                    $propertyArray['main_image'] = $url;
                    $propertyArray['main_image_thumb'] = $thumbnailUrl;
                    $propertyArray['main_image_preview'] = $previewUrl ? $previewUrl : $thumbnailUrl;
                }
                $a = Array(
                    'property_id' => $propertyId,
                    'url' => $url,
                    'thumb_url' => $thumbnailUrl,
                    'preview_url' => $previewUrl ? $previewUrl : $thumbnailUrl,
                    'caption' => strval($photo->Title),
                    'position' => $position
                );
                $this->db->query($this->arrayToQueryInsert($this->tablePictures, $a));
                $position++;
            }
        }
    }

    protected function parsePropertyTags($propertyId, $tags) {
        if ($tags) {
            foreach ($tags as $tag) {
                $tag = strtolower(strval($tag));
                if ($tag) {
                    $sql = $this->db->prepare("SELECT id FROM $this->tableTags WHERE tag=%s", $tag);
                    $tagId = (int)$this->db->get_var($sql);
                    if (!$tagId) {
                        $a = Array(
                            'tag' => $tag
                        );
                        $sql = $this->arrayToQueryInsert($this->tableTags, $a);
                        $this->db->query($sql);
                        $tagId = $this->db->insert_id;
                    }
                    if ($tagId) {
                        $sql = $this->db->prepare(
                            "SELECT COUNT(*) FROM $this->tablePropertyTags WHERE property_id=%d AND tag_id=%d",
                            $propertyId,
                            $tagId
                        );
                        $exists = (int)$this->db->get_var($sql);
                        if (!$exists) {
                            $a = Array(
                                'property_id' => $propertyId,
                                'tag_id' => $tagId
                            );
                            $sql = $this->arrayToQueryInsert($this->tablePropertyTags, $a);
                            $this->db->query($sql);
                        }
                    }
                }
            }
        }
    }

    protected function parsePropertyAmenities($propertyId, $amenities) {
        if ($amenities) {
            foreach ($amenities as $amenity) {
                if ($amenity->Value == 'Available') {
                    $a = Array(
                        'property_id' => $propertyId,
                        'description' => strval($amenity->Name)
                    );
                    $this->db->query($this->arrayToQueryInsert($this->tableAmenities, $a));
                }
            }
        }
    }

    protected function parsePropertyMergedRates($propertyId, $mergedRates) {
        if ($mergedRates) {
            foreach ($mergedRates as $rate) {
                $a = Array(
                    'property_id' => $propertyId,
                    'name' => strval($rate->RateName),
                    'from_date' => strval($rate->StartDate),
                    'to_date' => strval($rate->EndDate),
                    'min_stay' => strval($rate->MinimumStay),
                );
                if ($rate->Rates->Rate) {
                    foreach ($rate->Rates->Rate as $mr) {
                        $amount = strval($mr->Amount);
                        $rateType = strval($mr->RateType);
                        if ($rateType == 'NIGHTLY_WEEKDAY') {
                            $a['nightly'] = $amount;
                        } else if ($rateType == 'NIGHTLY_WEEKEND') {
                            $a['weekend_nightly'] = $amount;
                        } else if ($rateType == 'WEEKLY') {
                            $a['weekly'] = $amount;
                        } else if ($rateType == 'MONTHLY') {
                            $a['monthly'] = $amount;
                        }
                    }
                }
                $this->db->query($this->arrayToQueryInsert($this->tableMergedRates, $a));
            }
        }
    }

    protected function parsePropertyLanguages($propertyId, $propertyDescription, $propertyDescriptionLong, $propertyDetails, $propertyLanguages) {
        $passedLanguageCodes = array();
        if ($propertyLanguages) {
            foreach ($propertyLanguages as $language) {
                $lc = strtolower($language->LanguageCode);
                $a = Array(
                    'id' => $propertyId,
                    'language_code' => $lc,
                    'description' => $this->config->get('p_lodgix_display_title') == 'name' ? strval($language->Name) : strval($language->MarketingTitle),
                    'description_long' => strval($language->MarketingTeaser),
                    'details' => html_entity_decode($language->Description)
                );
                $inserted = $this->db->query($this->arrayToQueryInsert($this->tableLangProperties, $a));
                if ($inserted) {
                    array_push($passedLanguageCodes, $lc);
                }
            }
        }
        // Add default values for missing languages
        $languages = $this->db->get_results("SELECT * FROM $this->tableLanguages WHERE enabled=1 and code<>'en'");
        foreach ($languages as $l) {
            if (!in_array($l->code, $passedLanguageCodes)) {
                $a = Array(
                    'id' => $propertyId,
                    'language_code' => strtolower($l->code),
                    'description' => $propertyDescription,
                    'description_long' => $propertyDescriptionLong,
                    'details' => $propertyDetails
                );
                $this->db->query($this->arrayToQueryInsert($this->tableLangProperties, $a));
            }
        }
    }

    protected function parsePropertyTaxes($propertyId, $taxes) {
        if ($taxes) {
            foreach ($taxes as $tax) {
                $a = Array(
                    'property_id' => $propertyId,
                    'title' => strval($tax->Title),
                    'value' => strval($tax->Value),
                    'type' => strval($tax->Type),
                    'frequency' => strval($tax->Frequency),
                    'is_flat' => $tax->IsFlat == 'Yes' ? 1 : 0
                );
                $this->db->query($this->arrayToQueryInsert($this->tableTaxes, $a));
            }
        }
    }

    protected function parsePropertyFees($propertyId, $fees) {
        if ($fees) {
            foreach ($fees as $fee) {
                $a = Array(
                    'property_id' => $propertyId,
                    'title' => strval($fee->Title),
                    'value' => strval($fee->Value),
                    'type' => strval($fee->Type),
                    'is_flat' => $fee->IsFlat == 'Yes' ? 1 : 0,
                    'tax_exempt' => $fee->TaxExempt == 'Yes' ? 1 : 0
                );
                $this->db->query($this->arrayToQueryInsert($this->tableFees, $a));
            }
        }
    }

    protected function parsePropertyDeposits($propertyId, $deposits) {
        if ($deposits) {
            foreach ($deposits as $deposit) {
                $a = Array(
                    'property_id' => $propertyId,
                    'title' => strval($deposit->Title),
                    'value' => strval($deposit->Value)
                );
                $this->db->query($this->arrayToQueryInsert($this->tableDeposits, $a));
            }
        }
    }

    protected function parsePropertyReviews($propertyId, $reviews) {
        if ($reviews) {
            foreach ($reviews as $review) {
                $a = Array(
                    'property_id' => $propertyId,
                    'date' => strval($review->Date),
                    'name' => strval($review->Name),
                    'stars' => strval($review->Stars),
                    'title' => strval($review->Title),
                    'description' => strval($review->Description),
                    'language_code' => strval($review->LanguageCode)
                );
                $this->db->query($this->arrayToQueryInsert($this->tableReviews, $a));
            }
        }
    }

    protected function parsePropertyCategories($propertyId, $categoryIds) {
        if ($categoryIds) {
            foreach ($categoryIds as $categoryId) {
                $a = Array(
                    'property_id' => $propertyId,
                    'category_id' => strval($categoryId)
                );
                $this->db->query($this->arrayToQueryInsert($this->tablePropertyCategories, $a));
            }
        }
    }

    protected function parseCategory($category) {
        $a = Array(
            'category_id' => strval($category->ID),
            'parent_category_id' => strval($category->ParentCategoryID),
            'title' => strval($category->Title),
            'post_slug' => sanitize_title_with_dashes($category->WordpressSlug, '', 'sav‌​e')
        );
        $this->db->query($this->arrayToQueryInsert($this->tableCategories, $a));
    }

    protected function cleanProperties($activeProperties) {
        $properties = $this->db->get_results("SELECT * FROM $this->tableProperties WHERE id not in ($activeProperties)");
        if ($properties) {
            foreach ($properties as $property) {
                wp_delete_post((int)$property->post_id, $force_delete=true);
            }
        }
        $this->db->query("DELETE FROM $this->tableProperties WHERE id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tableAmenities WHERE property_id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tableMergedRates WHERE property_id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tablePictures WHERE property_id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tablePages WHERE property_id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tableLangPages WHERE property_id not in ($activeProperties)");
        $this->db->query("DELETE FROM $this->tableLangProperties WHERE id not in ($activeProperties)");
    }

    protected function arrayToQueryInsert($table, $data) {
        $fields = Array();
        $values = Array();
        foreach ($data as $field => $value) {
            $fields[] = "`$field`";
            $val = @esc_sql($value);
            $values[] = "'$val'";
        }
        $fieldStr = join(',', $fields);
        $valueStr = join(', ', $values);
        $sql = "INSERT INTO `$table` ($fieldStr) VALUES ($valueStr)";
        $sql = str_replace("'None'", 'NULL', $sql);
        $sql = str_replace("'NULL'", 'NULL', $sql);
        return $sql;
    }

    protected function arrayToQueryUpdate($table, $data, $pk) {
        $fieldValues = Array();
        foreach ($data as $field => $value) {
            $val = @esc_sql($value);
            $fieldValues[] = "`$field`='$val'";
        }
        $set = join(',', $fieldValues);
        return "UPDATE `$table` SET $set WHERE id=$pk";
    }

}
