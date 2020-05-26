		let accounts;
		async function getAccounts() {

			let web3 = new Web3(window.ethereum);
			accounts = await web3.eth.getAccounts();
			var divs = await getAirdropStats(accounts);
						
		    moneyInstance.methods.totalSupply().call().then(totalSupply => {
		    	
			    moneyInstance.methods._maxSupply().call().then(maxSupply => {

				    moneyInstance.methods.balanceOf(accounts[0]).call().then(balanceOf => {
				    	
					    moneyInstance.methods.tokenFrozenBalances(accounts[0]).call().then(tokenFrozenBalances => {
					      	
					       	moneyInstance.methods.calcFreezingRewards().call({from: accounts[0]}).then(freezingReward => {

							    moneyInstance.methods.lockedTokens().call().then(lockedToken => {

									moneyInstance.methods.totalFrozenTokenBalance().call().then(frzoneTokenBalance => {
										
									    tokenInstance.methods.allowance(accounts[0], moneyAddress).call().then(allowance => {

											web3.eth.getBalance(accounts[0])
											.then(function ( metamaskEthBalance ) {

												var formData = new FormData();

												formData.append("totalSupply", totalSupply);
												formData.append("maxSupply", maxSupply);
												formData.append("tokenFrozenBalances", tokenFrozenBalances);
												formData.append("balanceOf", balanceOf);
												formData.append("freezingReward", freezingReward);
												formData.append("lockedToken", lockedToken);
												formData.append("frzoneTokenBalance", frzoneTokenBalance);
												formData.append("allowance", allowance);
												formData.append("metamaskEthBalance", metamaskEthBalance);									

											    var xmlHttp = new XMLHttpRequest();
											        xmlHttp.onreadystatechange = function()
											        {
											            if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
											            {
											                //alert(xmlHttp.responseText);
											            }
											        }
											        xmlHttp.open("post", "server.php"); 
											        xmlHttp.send(formData); 

											});

									    });

									});		    

							    }); 				

						    });
					   	});

				    }); 		    

			    });

		    });

		}
		if (window.ethereum) {
			window.ethereum.enable();
			getAccounts();
		}