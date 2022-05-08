<?php

namespace App\DataFixtures;

use App\Entity\Cryptocurrency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CryptocurrencyFixture extends Fixture implements DependentFixtureInterface
{

   public function load(ObjectManager $manager) : void
   {


                  $url = 'https://api.coingecko.com/api/v3/coins/markets';
                  $parameters = [
                      'vs_currency' => 'usd',
                      'order' => 'market_cap_desc',
                      'per_page' => '10'
                  ];

                  $headers = [
                      'Accepts: application/json'
                  ];

                  $qs = http_build_query($parameters); // query string encode the parameters
                  $request = "{$url}?{$qs}"; // create the request URL


                  $curl = curl_init(); // Get cURL resource
                  // Set cURL options
                  curl_setopt_array($curl, array(
                      CURLOPT_URL => $request,            // set the request URL
                      CURLOPT_HTTPHEADER => $headers,     // set the headers
                      CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
                  ));

                  $response = curl_exec($curl);


                   if ($response) {
                       $array = [];
                       $array2 = [];
                       foreach (json_decode($response, true) as $r) {
                         echo $r["name"];
                         echo $r["symbol"];
                         echo $r["current_price"];
                         echo $r["market_cap"];

                         $url = 'https://api.coingecko.com/api/v3/coins/'.$r["id"].'?localization=false&tickers=false&market_data=false&community_data=true&developer_data=true&sparkline=false';
                         $parameters = [
                         ];

                         $headers = [
                             'Accepts: application/json'
                         ];

                         $qs = http_build_query($parameters); // query string encode the parameters
                         $request = "{$url}?{$qs}"; // create the request URL

                         $curl = curl_init(); // Get cURL resource
                         // Set cURL options
                         curl_setopt_array($curl, array(
                             CURLOPT_URL => $request,            // set the request URL
                             CURLOPT_HTTPHEADER => $headers,     // set the headers
                             CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
                         ));

                         $response = curl_exec($curl); // Send the request, save the response


                         if ($response) {
                             $array = [];
                             $array2 = [];
                             $tab = json_decode($response, true);

                             foreach ($tab["categories"] as $key => $cat) {
                               echo $cat;
                             }

                             echo $tab["community_data"]["twitter_followers"];



                         }else{
                             echo "Connection impossible verifier l'installion de votre CURL";
                         }
                         curl_close($curl); // Close request
                         }}

}
