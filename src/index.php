<?php 
declare(strict_types=1);

require 'config.php';
require 'database.php';

$db     = new Database();
$handle = curl_init(); 

curl_setopt_array($handle, [CURLOPT_ENCODING => '', CURLOPT_RETURNTRANSFER => 1]);

if (Config::UPDATE_ITEM_LIST) 
{
	curl_setopt($handle, CURLOPT_URL, 'https://api.nexushub.co/wow-classic/v1/items/faerlina-horde');

	$exec     = json_decode(curl_exec($handle), true);
	$item_ids = [];

	foreach ($exec['data'] as $row) {
		$item_ids[] = (int) $row['itemId'];
	}

	if ($item_ids) 
	{
		$item_meta  = []; 
		$item_count = count($item_ids);

		foreach ($item_ids as $key => $item_id) 
		{
			curl_setopt(
				$handle, 
				CURLOPT_URL, 
				sprintf('https://api.nexushub.co/wow-classic/v1/items/faerlina-horde/%d', $item_id)
			);
	
			do 
			{			
				// Rate-limit (4 reqs per sec.)
				usleep(250000);

				$exec = json_decode(curl_exec($handle), true);

				if (isset($exec['error'])) 
				{	
					printf("%s\n", $exec['reason']);
					
					if ($exec['error'] === 'Rate limit exceeded.') 
					{
						// Ex. "[reason] => Max requests per interval reached. You need to wait 3873ms to continue."
						preg_match('/\d+/', $exec['reason'], $matches);

						usleep($matches[0] * 1000);
					} elseif ($exec['error'] === 'Not found.') {
						continue 2;
					}
				}
			} while (isset($exec['error']));

			$item_meta[] = sprintf(
				"%d,'%s',%d,'%s','%s'", 
				$item_id, 
				$db->mysql->real_escape_string($exec['name']), 
				(int) $exec['sellPrice'], 
				$db->mysql->real_escape_string($exec['tags'][0]), 
				$db->mysql->real_escape_string($exec['tags'][1])
			);

			printf("%d/%d\r", $key + 1, $item_count);
		}

		if ($item_meta) 
		{
			// Using ON DUPLICATE KEY UPDATE vs. INSERT IGNORE to prevent potential errors from being ignored.
			$db->query(
				sprintf("
					INSERT INTO 
						items (itemid, name, vendor_price, rarity, category) 
					VALUES 
						(%s) AS v 
					ON DUPLICATE KEY UPDATE 
						name         = v.name, 
						vendor_price = v.vendor_price,
						rarity       = v.rarity, 
						category     = v.category",
					implode('),(', $item_meta)
				)
			);
		}
	}
}

if (Config::UPDATE_AUCTION_HISTORY) 
{
	$item_ids   = $db->getColumn("SELECT itemid FROM items ORDER BY itemid ASC"); // Sort by primary key to improve auction_history insertion performance. 
	$item_count = count($item_ids);

	foreach ($item_ids as $key => $item_id) 
	{
		curl_setopt(
			$handle, 
			CURLOPT_URL, 
			sprintf('https://api.nexushub.co/wow-classic/v1/items/faerlina-horde/%d/prices?timerange=999', $item_id)
		);

		do 
		{			
			// Rate-limit (4 reqs per sec.)
			usleep(250000);

			$exec = json_decode(curl_exec($handle), true);

			if (isset($exec['error'])) 
			{	
				printf("%s\n", $exec['reason']);
				
				if ($exec['error'] === 'Rate limit exceeded.') 
				{
					preg_match('/\d+/', $exec['reason'], $matches);

					usleep($matches[0] * 1000);
				} elseif ($exec['error'] === 'Not found.') {
					continue 2;
				}
			}
		} while (isset($exec['error']));

		$data = [];

		foreach ($exec['data'] as $row) 
		{
			$scan_time = DateTime::createFromFormat('Y-m-d\TH:i:s.000\Z', $row['scannedAt'], new DateTimeZone('UTC'));
			$data[]    = sprintf(
				"%d,%d,%d,%d,%d,'%s'", 
				$item_id, 
				$scan_time->getTimestamp(),
				$row['marketValue'], 
				$row['minBuyout'], 
				$row['quantity'], 
				$row['scannedAt']
			);
		}

		if ($data) 
		{
			$db->query(
				sprintf("
					INSERT INTO 
						auction_history (itemid, scan_ts, market_value, min_buyout, quantity, scan_time) 
					VALUES 
						(%s) AS v 
					ON DUPLICATE KEY UPDATE 
						market_value = v.market_value, 
						min_buyout   = v.min_buyout,
						quantity     = v.quantity, 
						scan_time    = v.scan_time",
					implode('),(', $data)
				)
			);
		} 

		printf("%d/%d\r", $key + 1, $item_count);
	}
}