<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Line;

use Illuminate\Http\Request;

class LivePriceController extends Controller
{
    public function sendliveprice()
    {
        $orderbooks= array();
        for ($i = 0; $i < 7 ; $i++) { 
            for ($j = 0; $j < 4 ; $j++) { 
                $orderbooks[$i][$j][0] = json_decode(Redis::get(''.$i.".".$j))[0];
                $orderbooks[$i][$j][1] = json_decode(Redis::get(''.$i.".".$j))[1];
                $orderbooks[$i]['exchange'] = Redis::get(''.$i.".e");
            }
        }

        $message ="Bitcoin Price in USD (% vs Bx)\r\n";

        $message .= $orderbooks[2]['exchange'].": ";
        $message .= round($orderbooks[2][0][0],2)." (0%)\r\n";

        foreach ($orderbooks as $key => $orderbook) {
        
            if ($key != 2) {
                $message .= $orderbook['exchange'].": ";
                $message .= round($orderbook[0][0],2)." (".round(($orderbook[0][0]-$orderbooks[2][0][0])/$orderbooks[2][0][0]*100,2)."%)\r\n";
            }
        };

        $message .="\r\nEthereum Price in USD (% vs Bx)\r\n";

        $message .= $orderbooks[2]['exchange'].": ";
        $message .= round($orderbooks[2][1][0],2)." (0%)\r\n";

        foreach ($orderbooks as $key => $orderbook) {
        
            if ($key != 2) {
                $message .= $orderbook['exchange'].": ";
                $message .= round($orderbook[1][0],2)." (".round(($orderbook[1][0]-$orderbooks[2][1][0])/$orderbooks[2][1][0]*100,2)."%)\r\n";
            }
        };

        $message .="\r\nRipple Price in USD (% vs Bx)\r\n";

        $message .= $orderbooks[2]['exchange'].": ";
        $message .= round($orderbooks[2][2][0],4)." (0%)\r\n";

        foreach ($orderbooks as $key => $orderbook) {
        
            if ($key != 0) {
                if ($key != 2) {
                    $message .= $orderbook['exchange'].": ";
                    $message .= round($orderbook[2][0],4)." (".round(($orderbook[2][0]-$orderbooks[2][2][0])/$orderbooks[2][2][0]*100,2)."%)\r\n";
                }
            }
        };

        Line::pushMessage('Ua2b3dd43fdfaf129015087ee98896a5a',$message);
    }

    public function test()
    {
        $orderbooks= array();
        for ($i = 0; $i < 7 ; $i++) { 
            for ($j = 0; $j < 4 ; $j++) { 
                $orderbooks[$i][$j][0] = json_decode(Redis::get(''.$i.".".$j))[0];
                $orderbooks[$i][$j][1] = json_decode(Redis::get(''.$i.".".$j))[1];
                $orderbooks[$i]['exchange'] = Redis::get(''.$i.".e");
            }
        }

        $profit = array();
        $profit[0]=round((($orderbooks[0][3][0]-$orderbooks[4][3][0])/$orderbooks[4][3][0]*100),4);
        $profit[1]=round((($orderbooks[0][3][1]-$orderbooks[4][3][0])/$orderbooks[4][3][0]*100),4);
        $profit[2]=round((($orderbooks[4][3][0]-$orderbooks[0][3][0])/$orderbooks[0][3][0]*100),4);
        $profit[3]=round((($orderbooks[4][3][1]-$orderbooks[0][3][0])/$orderbooks[0][3][0]*100),4);
        $message ="";

        $message .= "Limit-Market: ";
        $message .= "\r\nLimit-Limit: ";

        $message .= "\r\nLimit-Market: ";
        $message .= "\r\nLimit-Limit: ";

        Line::pushMessage('Ua2b3dd43fdfaf129015087ee98896a5a',$message);
    }
}
