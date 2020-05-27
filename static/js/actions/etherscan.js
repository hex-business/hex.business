
  const airdropContract = '0xc4e1b40cf87bd8a7a1d785276c42113e0ff50f3d';
  const hexTokenAddress = '0x8c6fB3075D144C2C1877A2fcd92297729fBE8b80';
  const transferTopic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
  const apiKey = '48H8MJEP5FA5WMVMVF599PSQX287QAZX46';
  const address = '0x2b591e99afE9f32eAA6214f7B7629768c40Eeb39';
  const topic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

   const toHexAddress = (add) => {
    return '0x000000000000000000000000' + add.substring(2);
  }

  async function getAirdropStats(acc) {
    console.log("account");
    console.log(acc);
    console.log(airdropContract);
    var _totalAirdropped = 0;

    await fetch(
      `https://api.etherscan.io/api?module=logs&action=getLogs
      &fromBlock=10011880
      &toBlock=latest
      &address=${address}
      &topic0=${topic}
      &topic0_1_opr=and
      &topic1=${toHexAddress(airdropContract)}
      &topic1_2_opr=and
      &topic2=${toHexAddress(acc[0])}
      &apikey=${apiKey}`,
      {
        method: "GET",
        mode:'cors',
        headers: {
          'Content-Type': 'application/json',
        }
      }
    )
    .then(res =>  res.json())
    .then(response => {
      response.result.forEach(l => {
        _totalAirdropped += parseInt(l.data);
      });
      return _totalAirdropped;
      })
      .catch(error => console.log("API ERROR:" + error));
        return _totalAirdropped;
      }





  async function getTotalAirdropped() {

    var _totalAirdropped = 0;

     await fetch(
      `https://api.etherscan.io/api?module=logs&action=getLogs
&fromBlock=10011880
&toBlock=latest
&address=${address}
&topic0=${topic}
&topic0_1_opr=and
&topic1=${toHexAddress(airdropContract)}
&apikey=${apiKey}}`,
      {
        method: "GET",
        mode:'cors',

  headers: {
       'Content-Type': 'application/json',

  }
      }
    )
      .then(res =>  res.json())
      .then(response => {
        console.log(response);
        response.result.forEach(l => {
          _totalAirdropped += parseInt(l.data);
        });

        console.log(_totalAirdropped);
        
 
      })
      .catch(error => console.log("API ERROR:" + error));

      return _totalAirdropped;
      }