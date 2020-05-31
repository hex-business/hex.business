<?php
class Config
{
	public static  function getConfig()
	{
		return [
			'cachelife' => 60, //life in seconds
			'etherscanApiKey' => '8XSYDGEUHVQWM1BXWH3VX5XK9BEB2FYZ5Y',
			'rateDataUrl' => 'https://hexvisionbusinessapi.azurewebsites.net/api/extendedStats',

		];
	}

	public static function getEtherConfig()
	{
		return [
		  "airdropContract" => '0xc4e1b40cf87bd8a7a1d785276c42113e0ff50f3d',
		  "hexTokenAddress" => '0x8c6fB3075D144C2C1877A2fcd92297729fBE8b80',
		  "transferTopic" => '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef',
		  "apiKey" => '48H8MJEP5FA5WMVMVF599PSQX287QAZX46',
		  "address" => '0x2b591e99afE9f32eAA6214f7B7629768c40Eeb39',
		  "topic" => '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef',
		];
	}
}
