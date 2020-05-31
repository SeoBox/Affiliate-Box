<?php

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\Configuration;

class Amazon
{

    public static function get_amazon_url($asin)
    {
        $affiliate_settings = get_field('amazon_affiliate_settings', 'option');
        $tag = $affiliate_settings['associate_id'] ?? '';

        return "http://www.amazon.com/dp/" . $asin . "/ref=nosim?tag=" . $tag;
    }

    static function get_locales()
    {
        return apply_filters(__FUNCTION__, array(
            'US' => __('United States'),
            'AU' => __('Australia'),
            'BR' => __('Brazil'),
            'CA' => __('Canada'),
            'CN' => __('China'),
            'FR' => __('France'),
            'DE' => __('Germany'),
            'IT' => __('Italy'),
            'IN' => __('India'),
            'JP' => __('Japan'),
            'ES' => __('Spain'),
            'UK' => __('United Kingdom'),
        ));
    }

    static function get_locale_hosts()
    {
        return apply_filters(__FUNCTION__, array(
            'US' => 'webservices.amazon.com',
            'AU' => 'webservices.amazon.com.au',
            'BR' => 'webservices.amazon.com.br',
            'CA' => 'webservices.amazon.ca',
            'FR' => 'webservices.amazon.fr',
            'DE' => 'webservices.amazon.de',
            'IN' => 'webservices.amazon.in',
            'IT' => 'webservices.amazon.it',
            'JP' => 'webservices.amazon.co.jp',
            'MX' => 'webservices.amazon.com.mx',
            'ES' => 'webservices.amazon.es',
            'TR' => 'webservices.amazon.com.tr',
            'AE' => 'webservices.amazon.ae',
            'UK' => 'webservices.amazon.co.uk',
        ));
    }

    static function get_locale_regions()
    {
        return apply_filters(__FUNCTION__, array(
            'US' => 'us-east-1',
            'AU' => 'us-west-2',
            'BR' => 'us-east-1',
            'CA' => 'us-east-1',
            'FR' => 'eu-west-1',
            'DE' => 'eu-west-1',
            'IN' => 'eu-west-1',
            'IT' => 'eu-west-1',
            'JP' => 'us-west-2',
            'MX' => 'us-east-1',
            'ES' => 'eu-west-1',
            'TR' => 'eu-west-1',
            'AE' => 'eu-west-1',
            'UK' => 'eu-west-1',
        ));
    }

    static function get_locale_host($locale)
    {
        $locale = self::get_locale($locale);
        $locale_hosts = self::get_locale_hosts();

        return isset($locale_hosts[$locale]) ? $locale_hosts[$locale] : current($locale_hosts);
    }

    static function get_locale_region($locale)
    {
        $locale = self::get_locale($locale);
        $locale_regions = self::get_locale_regions();

        return isset($locale_regions[$locale]) ? $locale_regions[$locale] : current($locale_regions);
    }


    static function get_locale($locale)
    {
        $locale = strtoupper($locale);
        $locales = self::get_locales();

        return isset($locales[$locale]) ? $locale : key($locales);
    }

    static function api_config($locale, $access_key = null, $secret_key = null)
    {
        $locale = self::get_locale($locale);

        $affiliate_settings = get_field('amazon_affiliate_settings', 'option');
        $access_key = $affiliate_settings['access_key'] ?? '';
        $secret_key = $affiliate_settings['secret_key'] ?? '';

        $config = new Configuration();
        $config->setAccessKey($access_key);
        $config->setSecretKey($secret_key);
        $config->setHost(Amazon::get_locale_host($locale));
        $config->setRegion(Amazon::get_locale_region($locale));

        return $config;
    }

    static function parseResponse($browseNodes)
    {
        $mappedResponse = array();
        foreach ($browseNodes as $browseNode) {
            $mappedResponse[$browseNode->getASIN()] = $browseNode;
        }
        return $mappedResponse;
    }

    static function get_transient_key($identifier, $locale)
    {
        return "affiliate_box_{$identifier}_{$locale}";
    }


    public static function get_images($identifier, $locale = null)
    {
        $locale = self::get_locale($locale);
        $transient = get_transient(self::get_transient_key($identifier, $locale));
        if ($transient) {
            return $transient;
        }

        $identifiers = is_array($identifier) ? array_unique($identifier) : array_unique(array($identifier));
        $affiliate_settings = get_field('amazon_affiliate_settings', 'option');
        $associate_tag = $affiliate_settings['associate_id'] ?? '';

        $config = Amazon::api_config($locale);
        $instance = new DefaultApi(new GuzzleHttp\Client(), $config);

        $resources = array(
            GetItemsResource::IMAGESPRIMARYSMALL,
            GetItemsResource::IMAGESPRIMARYMEDIUM,
            GetItemsResource::IMAGESPRIMARYLARGE,
            GetItemsResource::IMAGESVARIANTSSMALL,
            GetItemsResource::IMAGESVARIANTSMEDIUM,
            GetItemsResource::IMAGESVARIANTSLARGE,
        );

        $request = new GetItemsRequest();
        $request->setItemIds($identifiers);
        $request->setPartnerTag($associate_tag);
        $request->setPartnerType(PartnerType::ASSOCIATES);
        $request->setResources($resources);

        try {
            $getItemsResponse = $instance->getItems($request);
            if ($getItemsResponse->getItemsResult() != null) {
                if ($getItemsResponse->getItemsResult()->getItems() != null) {
                    $responseList = self::parseResponse($getItemsResponse->getItemsResult()->getItems());

                    foreach ($identifiers as $asin) {
                        $item = $responseList[$asin];
                        if ($item == null) {
                            echo "Item not found, check errors", PHP_EOL;
                            continue;
                        }
                        $images = $item->getImages();
                        if ($images == null) {
//                            echo "No images found";
                            continue;
                        }

                        $primary = $images->getPrimary();
                        if ($primary == null) {
//                            echo "No primary image found";
                            continue;
                        }

                        $result = [
                            'large' => $primary->getLarge()->getUrl(),
                            'medium' => $primary->getMedium()->getUrl(),
                            'small' => $primary->getSmall()->getUrl()
                        ];
                        set_transient(self::get_transient_key($asin, $locale), $result, DAY_IN_SECONDS);
                        return $result;

                    }
                }
            }
        } catch (ApiException $e) {
            error_log($e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return [];
//        $data  = json_decode((string) $response, true);
//        var_dump($data);
    }

}