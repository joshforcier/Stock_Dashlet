<?php

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

stock_dashlet_init();

function stock_dashlet_init() 
{	
	$name="Stock Ticker";
	
	$args=array(

		DASHLET_NAME => $name,
		
		DASHLET_VERSION => "1.1",
		DASHLET_DATE => "2018-06-25",
		DASHLET_AUTHOR => "Josh Forcier",
		DASHLET_DESCRIPTION => "Stock Ticker<BR>",
						
		DASHLET_FUNCTION => "stock_dashlet_func",
		
		DASHLET_TITLE => "Stock Ticker",
		
		DASHLET_OUTBOARD_CLASS => "stock_outboardclass",
		DASHLET_INBOARD_CLASS => "stock_inboardclass",
		DASHLET_PREVIEW_CLASS => "stock_previewclass",
		
		DASHLET_CSS_FILE => "",
		DASHLET_REFRESHRATE => 1,
	);

	register_dashlet($name,$args);
}

function stock_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null) 
{
	$output="";

	//$imgbase=get_dashlet_url_base("stock")."/images/";

	switch($mode){
	case DASHLET_MODE_GETCONFIGHTML:
		$output='
			<BR CLASS="nobr" />
			<LABEL FOR="symbol">Please enter stock symbol(s), comma delimited.</LABEL>
			<BR CLASS="nobr" />
			<INPUT TYPE="text" NAME="symbol">
			<BR CLASS="nobr" />
		';
		break;

	case DASHLET_MODE_OUTBOARD:
		break;

	case DASHLET_MODE_INBOARD:

		$stock_symbol = $args["symbol"];
		$symbol_array = explode(',', $stock_symbol);

		function get_price($symbol_array)
		{
			$stock_API = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=" . $symbol_array . "&interval=1min&apikey=1NH2CEEGT5PIUG1Y";
			$json_stock = file_get_contents($stock_API);
			$stock_data = json_decode($json_stock,true);
			$stock_data = array_values($stock_data);
			$current_day = reset($stock_data[1]);
			$get_last_day = array_slice($stock_data[1], 1, 1);
			$last_day = reset($get_last_day);

			$last_price = round($last_day["4. close"], 2);
			$current_price = round($current_day["4. close"], 2);

			$percent_change = (($current_price / $last_price) - 1) * 100;
			$percent_change = round($percent_change, 2);
		
			echo '
	    	<table>
	    		<tr>
	    			<td width="50px">' . $symbol_array . '</td>
	    			<td width="50px">' . $current_price . '</td>
	    			<td width="50px">' . $percent_change . '%</td>
	    		</tr>    		
	    	</table><br>';
    	}

		$x = 0;    	
		do {
			get_price($symbol_array[$x]);
			$x++;
		} while ($x < count($symbol_array));

		break;

	case DASHLET_MODE_PREVIEW:

		break;
	}
			
	return $output;
}
