<?php
  require __DIR__.'/../vendor/autoload.php';
  include_once __DIR__.'/base.php';
  require_once  __DIR__ . '/config.php';
  
  Class Etherscan extends Base
  {

    private $config;
    protected $client;
    protected $promise;

    public function __construct(Config $config)
    {
      $this->config = $config;
      $this->client = new GuzzleHttp\Client();
    }

    /**
     * response etherscan api results
     *
     * @param  int  $id
     * @return Json
    */

    public function init(): void {

      if(isset($_POST['account']) && !empty($_POST['account']) ){

        $this->verifyToken();
        $acc = trim($_POST['account']);

        $stats = $this->getAirdropStats($acc);
        $total = $this->getTotalAirdropped();

        $result['status'] = 200;
        $result['stats'] = $stats;
        $result['total'] = $total;
        echo json_encode($result);
      }      
    }

    public function initwithoutJS($acc): array {

      $result = array();

      try {
            if(isset($acc) && !empty($acc))
              $stats = $this->getAirdropStats($acc);
            else
            $stats = "invalid";
      } catch (Exception $e) {
          $stats = "invalid";
      }

      try {
          $total = $this->getTotalAirdropped();
      } catch (Exception $e){
          $total = "invalid";
      }

      $result['status'] = 200;
      $result['stats'] = $stats;
      $result['total'] = $total;

      return $result;

    }

    /**
     * response HexAddress
     *
     * @param  string  $add
     * @return string
    */

    private function toHexAddress(string $add): string
    {
      if (!empty($add) && strlen($add) >= 3) {
        return '0x000000000000000000000000' . substr($add, 2);  
      }

    }

    
    private function getAirdropDetail(string $acc, string $airdropContract): string {

        $apiKey          = $this->config->getEtherConfigEtherApiKey();
        $address         = $this->config->getEtherConfigAddress();
        $topic           = $this->config->getEtherConfigTopic();

        $url = "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".$this->toHexAddress($airdropContract)."&topic1_2_opr=and&topic2=".$this->toHexAddress($acc)."&apikey=".$apiKey;

        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = $this->client->sendAsync($request)->then(function ($response) {
            return $response->getBody();
        });

        return $promise->wait();  
    }


    private function getAirdropTotalDetail(string $airdropContract): string {

      $apiKey          = $this->config->getEtherConfigEtherApiKey();
      $address         = $this->config->getEtherConfigAddress();
      $topic           = $this->config->getEtherConfigTopic();

      $url = "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=" . $address . "&topic0=" . $topic . "&topic0_1_opr=and&topic1=" . $this->toHexAddress($airdropContract) . "&apikey=" . $apiKey;

        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = $this->client->sendAsync($request)->then(function ($response) {
            return $response->getBody();
        });

        return $promise->wait();  
    }
    /**
     * response HexAddress
     *
     * @param  string  $add
     * @return string
    */

    private  function getAirdropStats(string $acc):int
    {

      if (empty($acc)) {
        throw new InvalidArgumentException("no account in airdrop stats");
      }
      else {
        $airdropContracts = $this->config->getEtherConfigAirDropContract();
        $airdropContracts = explode(",", $airdropContracts);

        $total = 0;

        foreach ($airdropContracts as $airdropContract) {
          
          $response =  $this->getAirdropDetail($acc, $airdropContract);

          if (!empty($response)) {
            $response = json_decode($response);

            if ($response->status == 1) {
              $result = $response->result;
              foreach ($result as $item) {
                $total = $total + hexdec($item->data);
              }
            }
          }
        }

        return $total;

      }

    }

    /**
     * Response Total Airdropped
     *
     * @return int
    */

    private function getTotalAirdropped():string
    {

      $airdropContracts = $this->config->getEtherConfigAirDropContract();
      $airdropContracts = explode(",", $airdropContracts);

      $total = 0;

      foreach ($airdropContracts as $airdropContract) {
        
        $response =  $this->getAirdropTotalDetail($airdropContract);

        if (!empty($response)) {
          $response = json_decode($response);

          if ($response->status == 1) {
            $result = $response->result;
            foreach ($result as $item) {
              $total = $total + hexdec($item->data);
            }
          }
        }
      }

      return $total;
    }
  }

  $etherscan = new Etherscan(new Config);
  $etherscan->init();