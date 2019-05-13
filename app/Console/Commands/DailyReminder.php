<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Mail;

class DailyReminder extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'daily:reminder';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Send email to all the users at the 9am daily';

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
        $users = User::all();
        foreach ($users as $user) {
            Mail::raw("
            MY DIARY

            Dear $user->name , this is a quick reminder to add a new entry to your diary.

            **Note if you are not subscribed to MyDiary, please ignore this mail.", function($message) use ($user) {
            $message->from('dailyreminder.no.reply@gmail.com');
            $message->to($user->email)->subject(' Daily Reminder');
            });
        }

        $this->info('Daily Reminder has been send successfully');
   }
}
