		let accounts;


		async function getAccounts() {

			let web3 = new Web3(window.ethereum);
			accounts = await web3.eth.getAccounts();
			var divs = await getAirdropStats(accounts);
			divs = divs / 100000000;

		    var total = await getTotalAirdropped();
		    total = total / 100000000;
						
		    moneyInstance.methods.totalSupply().call().then(totalSupply => {
		    	
			    moneyInstance.methods._maxSupply().call().then(maxSupply => {

				    moneyInstance.methods.balanceOf(accounts[0]).call().then(balanceOf => {
				    	
					    moneyInstance.methods.tokenFrozenBalances(accounts[0]).call().then(tokenFrozenBalances => {
					      	
					       	moneyInstance.methods.calcFreezingRewards().call({from: accounts[0]}).then(freezingReward => {

							    moneyInstance.methods.lockedTokens().call().then(lockedToken => {

									moneyInstance.methods.totalFrozenTokenBalance().call().then(frzoneTokenBalance => {
										
										moneyInstance.methods.totalHXYTransformed().call().then(hxyTransformed => {

											moneyInstance.methods.totalHeartsTransformed().call().then(heartsTransformed => {

											    tokenInstance.methods.allowance(accounts[0], moneyAddress).call().then(allowance => {

													web3.eth.getBalance(accounts[0]).then(function ( metamaskEthBalance ) {

														allowance = (allowance / 100000000) ?? 0;
														hxyTransformed = (hxyTransformed / 100000000) ?? 0;
														heartsTransformed = (heartsTransformed / 100000000) ?? 0;
														tokenFrozenBalances = (tokenFrozenBalances /100000000)??0;
														balanceOf = (balanceOf /100000000)??0;
														let interest = (tokenFrozenBalances == 0 ? 0 : (freezingReward / 100000000) ?? 0);
														interest = interest.toLocaleString('en-GB');
														let calculating_supply = ((totalSupply / 100000000) ?? 0) - ((frzoneTokenBalance / 100000000) ?? 0)  - ((lockedToken / 100000000) ?? 0);
														metamaskEthBalance = (metamaskEthBalance/100000000)??0;
														totalSupply = Math.floor((totalSupply / 100000000) ?? 0);
														totalSupply = totalSupply.toLocaleString('en-GB');
														lockedToken = (lockedToken / 100000000) ?? 0;
														lockedToken = lockedToken.toLocaleString('en-GB');
														frzoneTokenBalance = (frzoneTokenBalance / 100000000) ?? 0;
														frzoneTokenBalance = frzoneTokenBalance.toLocaleString('en-GB');
														calculating_supply = calculating_supply.toLocaleString('en-GB');
														tokenFrozenBalances = tokenFrozenBalances.toLocaleString('en-GB');
														hxyTransformed = hxyTransformed.toLocaleString('en-GB');
														heartsTransformed = heartsTransformed.toLocaleString('en-GB');
														total = total.toLocaleString('en-GB');
														maxSupply = (maxSupply / 100000000) ?? 0;
														maxSupply = maxSupply.toLocaleString('en-GB');
														divs = divs.toLocaleString('en-GB');

														let refereal_url = window.location.origin + "/?r=" + accounts[0];

														
														document.getElementById("balance").innerHTML = balanceOf.toLocaleString('en-GB') + " HXY";

														document.getElementById("total_supply").innerHTML = totalSupply + " HXY";
														document.getElementById("locked_tokens").innerHTML = lockedToken + " HXY";
														document.getElementById("frzoneTokenBalance").innerHTML = frzoneTokenBalance + " HXY";
														document.getElementById("calculating_supply").innerHTML = calculating_supply + " HXY";
														document.getElementById("approved_amount").innerHTML = allowance + " HEX";
														document.getElementById("total_approved").innerHTML = total + " HXY";
														document.getElementById("interest").innerHTML = interest + " HXY";
														document.getElementById("tokenFrozenBalances").innerHTML = tokenFrozenBalances + " HXY";
														document.getElementById("maxSupply").innerHTML = maxSupply + " HXY";
														document.getElementById("your_airdropped_divs").innerHTML = divs + " HXY";
														document.getElementById("total_hxy_conversion").innerHTML = hxyTransformed + " HXY";
														document.getElementById("total_hex_conversion").innerHTML = heartsTransformed + " HXY";
														
														document.getElementById("referal_url").innerHTML = refereal_url;
														

													});

											    });
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
			setInterval(()=> getAccounts(), 20000);
		}