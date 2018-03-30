<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Line;
use Illuminate\Support\Facades\Redis;

class HitbtcBinanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bline';

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

        $profit = array();
        $profit[0]=round((($orderbooks[0][3][0]-$orderbooks[4][3][0])/$orderbooks[4][3][0]*100),3);
        $profit[1]=round((($orderbooks[0][3][1]-$orderbooks[4][3][0])/$orderbooks[4][3][0]*100),3);
        $profit[2]=round((($orderbooks[4][3][0]-$orderbooks[0][3][0])/$orderbooks[0][3][0]*100),3);
        $profit[3]=round((($orderbooks[4][3][1]-$orderbooks[0][3][0])/$orderbooks[0][3][0]*100),3);

        if (max($profit)>0.2) {
            $message ="Pair: Bitcoin - Ethereum\r\n";
            $message .="Hitbtc (ซื้อ) -> Binance (ขาย)\r\n";

            $message .= "Limit-Market: ".$profit[0]."%";
            $message .= "\r\nLimit-Limit: ".$profit[1]."%";

            $message .="\r\n\r\nHitbtc (ขาย) -> Binance (ซื้อ)";
            $message .= "\r\nLimit-Market: ".$profit[2]."%";
            $message .= "\r\nLimit-Limit: ".$profit[3]."%";

            Line::pushMessage('C25cf6c120577cb6086ec575eb40cf6c6',$message);
        } 
        
    }
}
