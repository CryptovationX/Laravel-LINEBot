<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Line;
use Illuminate\Support\Facades\Redis;

class LivePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderbooks= array();
        for ($i = 0; $i < 7 ; $i++) { 
            for ($j = 0; $j < 4 ; $j++) { 
                $orderbooks[$i][$j][0] = json_decode(Redis::get(''.$i.".".$j))[0];
                $orderbooks[$i][$j][1] = json_decode(Redis::get(''.$i.".".$j))[1];
                $orderbooks[$i]['exchange'] = Redis::get(''.$i.".e");
            }
        }

        $message ="Bitcoin Price in USD (% เทียบ Bx)\r\n";

        $message .= $orderbooks[2]['exchange'].": ";
        $message .= round($orderbooks[2][0][0],2)." (0%)\r\n";

        foreach ($orderbooks as $key => $orderbook) {
        
            if ($key != 2) {
                $message .= $orderbook['exchange'].": ";
                $message .= round($orderbook[0][0],2)." (".round(($orderbook[0][0]-$orderbooks[2][0][0])/$orderbooks[2][0][0]*100,2)."%)\r\n";
            }
        };

        $message .="\r\nEthereum Price in USD (% เทียบ Bx)\r\n";

        $message .= $orderbooks[2]['exchange'].": ";
        $message .= round($orderbooks[2][1][0],2)." (0%)\r\n";

        foreach ($orderbooks as $key => $orderbook) {
        
            if ($key != 2) {
                $message .= $orderbook['exchange'].": ";
                $message .= round($orderbook[1][0],2)." (".round(($orderbook[1][0]-$orderbooks[2][1][0])/$orderbooks[2][1][0]*100,2)."%)\r\n";
            }
        };

        $message .="\r\nRipple Price in USD (% เทียบ Bx)\r\n";

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

        Line::pushMessage('C25cf6c120577cb6086ec575eb40cf6c6',$message);
    }
}
