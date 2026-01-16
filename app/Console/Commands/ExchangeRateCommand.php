<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;


class ExchangeRateCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'no description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        try {

            $xml = null;
            $url = "https://www.tcmb.gov.tr/kurlar/today.xml";


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);

            if ($data !== false) {
                $xml = simplexml_load_string($data);
            }

            curl_close($ch);

            if(empty($xml)){
                throw new \Exception('currency-001');
            }



            $values = ["USD","AUD","DKK","EUR","GBP","CHF","SEK","CAD","KWD","NOK","SAR","JPY","BGN","RON","RUB","IRR","CNY","PKR","QAR","KRW","AZN","AED"];

            $s = 0;

            foreach ($values as $value) {

                $rate = $xml->Currency[$s]->ForexSelling;

                DB::table('currencies')->where('code', $value)->update(['convert_rate' => $rate]);

                $s++;

            } 


            $this->info("success");

          
            
        } catch (\Illuminate\Database\QueryException $queryException) {

            Log::info("Exchange Log: ", ["result" => $queryException->getMessage()]);

        } catch (\Exception $exception) {

            Log::info("Exchange Log: ", ["result" => $exception->getMessage()]);

        }





    }
}
