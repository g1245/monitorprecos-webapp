<?php

namespace App\Services\Awin;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class FeedDownloadService
{
    private const FEED_URL_TEMPLATE =
        'https://productdata.awin.com/datafeed/download'
        . '/apikey/317c85e8b0d74301ba7d7472617b6c84'
        . '/language/pt/fid/{feed_id}/rid/0/hasEnhancedFeeds/0'
        . '/columns/aw_deep_link,product_name,aw_product_id,merchant_product_id,'
        . 'merchant_image_url,description,merchant_category,search_price,merchant_name,'
        . 'merchant_id,category_name,category_id,aw_image_url,currency,store_price,'
        . 'delivery_cost,merchant_deep_link,language,last_updated,display_price,'
        . 'data_feed_id,brand_name,brand_id,colour,product_short_description,'
        . 'specifications,condition,product_model,model_number,dimensions,keywords,'
        . 'promotional_text,product_type,commission_group,'
        . 'merchant_product_category_path,merchant_product_second_category,'
        . 'merchant_product_third_category,rrp_price,saving,savings_percent,'
        . 'base_price,base_price_amount,base_price_text,product_price_old,'
        . 'delivery_restrictions,delivery_weight,warranty,terms_of_contract,'
        . 'delivery_time,in_stock,stock_quantity,valid_from,valid_to,is_for_sale,'
        . 'web_offer,pre_order,stock_status,size_stock_status,size_stock_amount,'
        . 'merchant_thumb_url,large_image,alternate_image,aw_thumb_url,'
        . 'alternate_image_two,alternate_image_three,alternate_image_four,'
        . 'reviews,average_rating,rating,number_available,'
        . 'custom_1,custom_2,custom_3,custom_4,custom_5,custom_6,custom_7,custom_8,custom_9,'
        . 'ean,isbn,upc,mpn,parent_product_id,product_GTIN,basket_link,'
        . 'Fashion%3Asuitable_for,Fashion%3Acategory,Fashion%3Asize,'
        . 'Fashion%3Amaterial,Fashion%3Apattern,Fashion%3Aswatch'
        . '/format/csv/delimiter/%2C/compression/gzip/adultcontent/1/';

    private const STORAGE_DISK = 'local';
    private const BASE_DIR = 'awin';

    /**
     * Download the AWIN feed gzip for the given feed ID, decompress it,
     * and return the path (relative to the storage disk) of the resulting CSV.
     *
     * @param  string  $feedId
     * @return string  Relative path to the decompressed CSV file.
     *
     * @throws \RuntimeException
     */
    public function download(string $feedId): string
    {
        $url = str_replace('{feed_id}', $feedId, self::FEED_URL_TEMPLATE);

        $gzRelative = self::BASE_DIR . "/{$feedId}.csv.gz";
        $csvRelative = self::BASE_DIR . "/{$feedId}.csv";

        $gzAbsolute = Storage::disk(self::STORAGE_DISK)->path($gzRelative);
        $csvAbsolute = Storage::disk(self::STORAGE_DISK)->path($csvRelative);

        Storage::disk(self::STORAGE_DISK)->makeDirectory(self::BASE_DIR);

        $client = new Client(['timeout' => 120]);
        $client->get($url, ['sink' => $gzAbsolute]);

        $this->decompress($gzAbsolute, $csvAbsolute);

        @unlink($gzAbsolute);

        return $csvRelative;
    }

    /**
     * Decompress a gzip file to the target path.
     *
     * @throws \RuntimeException
     */
    private function decompress(string $gzPath, string $targetPath): void
    {
        $gz = gzopen($gzPath, 'rb');

        if ($gz === false) {
            throw new \RuntimeException("Unable to open gzip file: {$gzPath}");
        }

        $out = fopen($targetPath, 'wb');

        if ($out === false) {
            gzclose($gz);
            throw new \RuntimeException("Unable to write CSV file: {$targetPath}");
        }

        try {
            while (!gzeof($gz)) {
                $chunk = gzread($gz, 65536);

                if ($chunk === false) {
                    break;
                }

                fwrite($out, $chunk);
            }
        } finally {
            gzclose($gz);
            fclose($out);
        }
    }
}
