
  <?php

  require_once  __DIR__ . '/config.php';

  Class Etherscan {

    // protected $config;

    public function __construct(Config $config)
    {
      $this->config = $config->getEtherConfig();
    }

    public function init() {

      $result = array();

      if(isset($_POST['account']) && !empty($_POST['account']) ){

        $acc = trim($_POST['account']);
        $airdropContract = $this->config['airdropContract'];
        $hexTokenAddress = $this->config['hexTokenAddress'];
        $transferTopic = $this->config['transferTopic'];
        $apiKey = $this->config['apiKey'];
        $address= $this->config['address'];
        $topic = $this->config['topic'];

        if(!empty($address) && !empty($topic) && !empty($airdropContract) && !empty($acc) && !empty($apiKey) ){
          $stats = $this->getAirdropStats($address,$topic,$airdropContract,$acc,$apiKey);
        }
        else {
          $stats = "invalid";
        }

        if(!empty($address) && !empty($topic) && !empty($airdropContract) && !empty($apiKey) ) {
          $total = $this->getTotalAirdropped($address, $topic, $airdropContract, $apiKey);
        }
        else {
          $total = "invalid";  
        }    

        $result['status'] = 200;
        $result['stats'] = $stats;
        $result['total'] = $total;
        
      }
      else {
        $result['status'] = 404;
      }

      echo json_encode($result); exit;
    }

    private  function toHexAddress($add): string
    {
      return '0x000000000000000000000000' . substr($add,2);
    }
    

    private  function getAirdropStats($address, $topic, $airdropContract, $acc, $apiKey): string
    {
      $_totalAirdropped = 0;
   
      $cURLConnection = curl_init();

      curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".$this->toHexAddress($airdropContract)."&topic1_2_opr=and&topic2=".$this->toHexAddress($acc)."&apikey=".$apiKey);
      curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
      $res = curl_exec($cURLConnection);
      
      if (curl_errno($cURLConnection)) {
          // this would be your first hint that something went wrong

          return "invalid";
      } else {
        
            $resultStatus = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
          curl_close($cURLConnection);

          if ($resultStatus != 200) {
             return "invalid";
          }
          else
          {

            $jsonArrayResponse = json_decode($res);

            if(!$jsonArrayResponse->result){
              return "invalid";
            }

            $arr = $jsonArrayResponse->result;

            if(is_array($arr))
            {
              foreach($arr as $item)
              {
                if($item->data)
                  $_totalAirdropped += hexdec($item->data);
                else return "invalid";
              }
              return $_totalAirdropped;
            }
           return "invalid";
          }
      }
    }


    private function getTotalAirdropped($address, $topic,$airdropContract,$apiKey ): string
    {

      $_totalAirdropped = 0;

      $cURLConnection = curl_init();

      curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".$this->toHexAddress($airdropContract)."&apikey=".$apiKey);
      curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type:application/json' ));
      $res = curl_exec($cURLConnection);

        if (curl_errno($cURLConnection)) {
          // this would be your first hint that something went wrong
          return "invalid";
        } else {
          // check the HTTP status code of the request

          $resultStatus = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
          curl_close($cURLConnection);

          if ($resultStatus != 200) {
             return "invalid";
          }
          else
          {
            $jsonArrayResponse = json_decode($res);
            if(!$jsonArrayResponse->result)
                return "invalid";
            $arr = $jsonArrayResponse->result;
            if(is_array($arr))
            {
               foreach($jsonArrayResponse->result as $item)
                {
                  $_totalAirdropped += hexdec($item->data);
                }
                return $_totalAirdropped;
            }
            return "invalid";
          }
      }
    }
  }

  $obj = new Etherscan(new Config);
  $obj->init();
?>