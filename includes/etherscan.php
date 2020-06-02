
  <?php

  require_once  __DIR__ . '/config.php';

  Class Etherscan {


    private $config;

    public function __construct(Config $config)
    {
      $this->config = $config->getEtherConfig();
    }

    public function init() {

      $result = array();

        if(!isset($_POST['account']) && empty($_POST['account']) ){
            $result['status'] = 400;
        }

        $acc = trim($_POST['account']);
        $airdropContract = $this->config['airdropContract'];
        $hexTokenAddress = $this->config['hexTokenAddress'];
        $transferTopic = $this->config['transferTopic'];
        $apiKey = $this->config['apiKey'];
        $address= $this->config['address'];
        $topic = $this->config['topic'];

        try {
            $stats = $this->getAirdropStats($address,$topic,$airdropContract,$acc,$apiKey);
        } catch (Exception $e) {
            $stats = "invalid";
        }

        try {
            $total = $this->getTotalAirdropped($address, $topic,$airdropContract,$apiKey);
        } catch (Exception $e){
            $total = "invalid";
        }

        $result['status'] = 200;
        $result['stats'] = $stats;
        $result['total'] = $total;
        


      echo json_encode($result); exit;
    }

    private  function toHexAddress($add): string
    {
      return '0x000000000000000000000000' . substr($add,2);
    }
    

    private  function getAirdropStats($address, $topic, $airdropContract, $acc, $apiKey):int
    {
        if(empty($address) || empty($topic) || empty($airdropContract) || empty($acc) || empty($apiKey)) {
            throw new InvalidArgumentException("invalid");
        }


        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".$this->toHexAddress($airdropContract)."&topic1_2_opr=and&topic2=".$this->toHexAddress($acc)."&apikey=".$apiKey);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
        $res = curl_exec($cURLConnection);

        //an error occurred with the curl
        if(curl_errno($cURLConnection)) {
          throw new InvalidArgumentException("invalid");
        }

        $resultStatus = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        curl_close($cURLConnection);
        if ($resultStatus!==200) {
          throw new InvalidArgumentException("invalid");
        }

        return $this->processTotalAirDropped($res);
    }

    private function processTotalAirDropped(string $result):int
    {
        if(empty($result)) {
            throw new InvalidArgumentException("invalid");
        }
        $_totalAirdropped = 0;
        $jsonArrayResponse = json_decode($result);

        if(!property_exists($jsonArrayResponse, 'result') || !is_array($jsonArrayResponse->result)) {
            throw new InvalidArgumentException( "invalid");
        }

        foreach($jsonArrayResponse->result as $item) {
            if(!property_exists($item, 'data')) {
                throw new InvalidArgumentException("Invalid");
            }

            $_totalAirdropped += hexdec($item->data);

        }

        return $_totalAirdropped;
    }


    private function getTotalAirdropped($address, $topic,$airdropContract,$apiKey ):int
    {
        if (empty($address) || empty($topic) || empty($airdropContract) || empty($apiKey)) {
            throw new InvalidArgumentException("invalid");
        }


        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=" . $address . "&topic0=" . $topic . "&topic0_1_opr=and&topic1=" . $this->toHexAddress($airdropContract) . "&apikey=" . $apiKey);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $res = curl_exec($cURLConnection);

        if (curl_errno($cURLConnection)) {
            throw new InvalidArgumentException("invalid");
        }

        $resultStatus = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        curl_close($cURLConnection);

        if ($resultStatus !== 200) {
            throw new InvalidArgumentException("invalid");
        }

        return $this->processTotalAirDropped($res);
    }

  }

  $obj = new Etherscan(new Config);
  $obj->init();
?>