<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Notice;
use App\Models\Customer;
use Log;

class ThreeDaysCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:threedays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification to user after 3 days';

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
     * @return int
     */
    public function handle()
    {
        Log::info("Three Days Cron is working");
        $startDate = time();
        $endeDate = date('Y-m-d H:i:s', strtotime('-3 days', $startDate));
        $beginDate = date('Y-m-d H:i:s', strtotime('-3 days -1 hours', $startDate));
        
        $notices = Notice::whereNotNull('customer_id')
            ->where('created_at', '<=', $endeDate)
            ->where('created_at', '>=', $beginDate)
            ->where('agree', 0)
            ->get();
            
        foreach($notices as $n) {
            $m = Customer::where('member_no', $n->customer_id)->first();
            if ($m != null && $m->fcm_token != null && $m->fcm_flag == 1) {
                $client = new Client(['base_uri' => 'https://fcm.googleapis.com/fcm/']);
                $client->request('POST', 'send', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer AAAAP4epmpI:APA91bHjVCcjOcurbg5YYqVpx9cx_KLPMrmrw6l4pTIja0pMEw0SJLkUP6X0x5YZMHFHmpDmrJtVubC71VgPA_ZWB2NlQpgLm_kLT4mBGJfXlduAB-hVu0nwJvuB-TCgQvj7BP-Wc79q',
                    ],
                    'json' => [
                        'to' => $m->fcm_token,
                        'data' => [
                            'type' => 'notify',
                            'notify' => $n->id,
                        ],
                        'notification' => [
                            // 'title' => $n->title,
                            // 'body' => $n->content,
                            'title' => 'Wハルトしませんか！？',
                            'body' => 'ハルト施工から3日が経ちました。 Wハルトのお申込みは施工店舗まで！',
                        ]
                    ],
                ]);
            }
            $n->agree = 1;
            $n->save();
        }
    }
}
