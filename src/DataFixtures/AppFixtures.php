<?php

namespace App\DataFixtures;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Cryptocurrency;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public const CATEGORY_REFERENCE = 'category-';
    public const USER_REFERENCE = 'user-';
    public const CRPT_REFERENCE = 'crpt-';
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
      $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {

      $obj_User = new User();
      $obj_User->setUserPseudo('root');
      $obj_User->setUserPassword(password_hash("ROOTmalzkejrht167", PASSWORD_BCRYPT));
      $obj_User->setUserMail('admin@admin.fr');
      $obj_User->setUserRole(array('ROLE_ADMIN'));  //ROLE_ADMIN : administrateur ROLE_USER : utilisateur inscrit ROLE:UNLOGGED : anonyme
      $manager->persist($obj_User);
      $this->addReference(self::USER_REFERENCE.'9999', $obj_User);

      $obj_User = new User();
      $obj_User->setUserPseudo('YXOwOX');
      $obj_User->setUserPassword(password_hash("3LGLxfizc2", PASSWORD_BCRYPT));
      $obj_User->setUserMail('thibault-lb@hotmail.fr');
      $obj_User->setUserRole(array('ROLE_ADMIN'));  //ROLE_ADMIN : administrateur ROLE_USER : utilisateur inscrit ROLE:UNLOGGED : anonyme
      $manager->persist($obj_User);
      $this->addReference(self::USER_REFERENCE.'0', $obj_User);

      $obj_User = new User();
      $obj_User->setUserPseudo('EGAIN');
      $obj_User->setUserPassword(password_hash("default28", PASSWORD_BCRYPT));
      $obj_User->setUserMail('Louise.Egain@outlook.fr');
      $obj_User->setUserRole(array('ROLE_ADMIN'));  //ROLE_ADMIN : administrateur ROLE_USER : utilisateur inscrit ROLE:UNLOGGED : anonyme
      $manager->persist($obj_User);
      $this->addReference(self::USER_REFERENCE.'1', $obj_User);

      $obj_User = new User();
      $obj_User->setUserPseudo('USER_TEST_A');
      $obj_User->setUserPassword(password_hash("testdefault28", PASSWORD_BCRYPT));
      $obj_User->setUserMail('USERA@hotmail.fr');
      $obj_User->setUserRole(array('ROLE_USER'));  //ROLE_ADMIN : administrateur ROLE_USER : utilisateur inscrit ROLE:UNLOGGED : anonyme
      $manager->persist($obj_User);
      $this->addReference(self::USER_REFERENCE.'2', $obj_User);

      $obj_User = new User();
      $obj_User->setUserPseudo('USER_TEST_B');
      $obj_User->setUserPassword(password_hash("testdefault28", PASSWORD_BCRYPT));
      $obj_User->setUserMail('USERB@hotmail.fr');
      $obj_User->setUserRole(array('ROLE_USER'));  //ROLE_ADMIN : administrateur ROLE_USER : utilisateur inscrit ROLE:UNLOGGED : anonyme
      $manager->persist($obj_User);
      $this->addReference(self::USER_REFERENCE.'3', $obj_User);

      $manager->flush();




      $cat_url = 'https://api.coingecko.com/api/v3/coins/categories';
      $cat_parameters = [
      ];

      $cat_headers = [
          'Accepts: application/json'
      ];

      $cat_qs = http_build_query($cat_parameters); // query string encode the parameters
      $cat_request = "{$cat_url}?{$cat_qs}"; // create the request URL

      $cat_curl = curl_init(); // Get cURL resource
      // Set cURL options
      curl_setopt_array($cat_curl, array(
          CURLOPT_URL => $cat_request,            // set the request URL
          CURLOPT_HTTPHEADER => $cat_headers,     // set the headers
          CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
      ));

      $cat_response = curl_exec($cat_curl); // Send the request, save the response

      if ($cat_response) {

          $cat_tab = json_decode($cat_response, true);

          foreach ($cat_tab as $key => $value) {
            $obj_cat = new Category();
            $obj_cat->setCatName($value["name"]);
            if(!$value["content"] == null){
              $obj_cat->setCatDesc($value["content"]);
            }
            else {
              $obj_cat->setCatDesc("pas de description");
            }
            $manager->persist($obj_cat);
            $this->addReference(self::CATEGORY_REFERENCE.$value["name"], $obj_cat);
            $manager->flush();
          }
          $obj_cat = new Category();
          $obj_cat->setCatName("DEFAULT");
          $obj_cat->setCatDesc("Cryptomonnaie sans catégorie");
          $manager->persist($obj_cat);
          $this->addReference(self::CATEGORY_REFERENCE.'DEFAULT', $obj_cat);
          $manager->flush();
      }
      else
      {
          echo "Connection impossible verifier l'installion de votre CURL";
      }



//------------------------------------------------------------------------
//------------------------Currencies Fixtures-----------------------------
//------------------------------------------------------------------------


        $url = 'https://api.coingecko.com/api/v3/coins/markets';
        $parameters = [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => '50'    //Count of retrieved currencies
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
             $itera = 0;
             foreach (json_decode($response, true) as $r) {
               $crpt = new Cryptocurrency();
               $crpt->setCrptName($r["name"]);                //set currency a name
               $crpt->setCrptSymbol($r["symbol"]);            //set the currency its symbol
               $crpt->setCrptPrice($r["current_price"]);      //set the currency its current price
               $crpt->setCrptMarketCap($r["market_cap"]);     //set the currency's marketcap

               for ($i=0; $i <= 2 ; $i++) {
                 $crpt->addCrptFan($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
               }

               //-----------------------------------------------------------
               //-------- specific request on the current currency ---------
               //-----------------------------------------------------------

               $url = 'https://api.coingecko.com/api/v3/coins/'.$r["id"].'?localization=false&tickers=false&market_data=false&community_data=true&developer_data=true&sparkline=false';
               //the parameters are set in the URL

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


               if ($response)
               {
                  $tab = json_decode($response, true);                                     //trasnform the query's result from a json to an array
                  foreach ($tab["categories"] as $key => $cat_Name) {

                    try {
                      $crpt->addCrptCategory($manager->merge($this->getReference(self::CATEGORY_REFERENCE.$cat_Name)));
                    } catch (\Exception $e) {

                    }

                  }

                }
                else
                {
                    echo "Connection impossible verifier l'installion de votre CURL";
                }

                $crpt->setCrptTwitterFollowers($tab["community_data"]["twitter_followers"]); //fetch and add the twitter followers count for the current currency


               curl_close($curl); // Close request
               $manager->persist($crpt);
               $this->addReference(self::CRPT_REFERENCE.$itera, $crpt);
               $itera += 1;
            }
          }
          else
          {
              echo "Connection impossible verifier l'installion de votre CURL";
          }

          $manager->flush();

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);

          $obj_com = new Comment();
          $obj_com->setComText("Lorem ipsum dolor sit amet. Ut debitis temporibus ex repellat molestias et esse voluptatem et perspiciatis iusto non impedit est architecto ipsa quo officiis perspiciatis. Ad dolor perspiciatis qui nobis quia qui inventore distinctio est sunt laborum.");
          $obj_com->setComSubject($manager->merge($this->getReference(self::CRPT_REFERENCE.rand(0,9))));
          $obj_com->setComOwner($manager->merge($this->getReference(self::USER_REFERENCE.rand(0,3))));
          $obj_com->setComDateTime(new \DateTime('NOW'));
          $manager->persist($obj_com);



          $manager->flush();


    }
}
