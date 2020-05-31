
  <?php 
  $airdropContract = '0xc4e1b40cf87bd8a7a1d785276c42113e0ff50f3d';
  $hexTokenAddress = '0x8c6fB3075D144C2C1877A2fcd92297729fBE8b80';
  $transferTopic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
  $apiKey = '48H8MJEP5FA5WMVMVF599PSQX287QAZX46';
  $address = '0x2b591e99afE9f32eAA6214f7B7629768c40Eeb39';
  $topic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

   function toHexAddress($add){
    return '0x000000000000000000000000' . substr($add,2);
  }

  $acc = $_POST['account'];
  $type = $_POST['type'];
   
  $stats = getAirdropStats($address,$topic,$airdropContract,$acc,$apiKey);
  $total = getTotalAirdropped($address, $topic,$airdropContract,$apiKey);
  echo $stats . '&&' . $total;

 function getAirdropStats($address,$topic,$airdropContract,$acc,$apiKey) {
    $_totalAirdropped = 0;
 
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".toHexAddress($airdropContract)."&topic1_2_opr=and&topic2=".toHexAddress($acc)."&apikey=".$apiKey);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
    $res = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $jsonArrayResponse = json_decode($res);
   
    $arr = $jsonArrayResponse->result;
    foreach($arr as $item)
    {
      $_totalAirdropped += hexdec($item->data);
    }
    return $_totalAirdropped;
}


  function getTotalAirdropped($address, $topic,$airdropContract,$apiKey ) {
    $_totalAirdropped = 0;

    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, "https://api.etherscan.io/api?module=logs&action=getLogs&fromBlock=10011880&toBlock=latest&address=".$address."&topic0=".$topic."&topic0_1_opr=and&topic1=".toHexAddress($airdropContract)."&apikey=".$apiKey);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type:application/json' ));
    $res = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $jsonArrayResponse = json_decode($res);
    
    foreach($jsonArrayResponse->result as $item)
    {
      $_totalAirdropped += hexdec($item->data);
    }
    return $_totalAirdropped;
  }

 
?>