function getWeb3() { return  new Web3(Web3.givenProvider)};
let web3 = getWeb3();

const tokenInstance = new web3.eth.Contract(JSON.parse(tokenABI), tokenAddress);
const moneyInstance = new web3.eth.Contract(JSON.parse(moneyContractABI), moneyAddress);