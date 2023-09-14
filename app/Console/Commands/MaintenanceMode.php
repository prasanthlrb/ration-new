<?php

namespace App\Console\Commands;

use App\Models\BusinessSetting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MaintenanceMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm:r';

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
     * @return int
     */
    public function handle()
    {

        //php artisan migrate --pretend  ---- Use for see upcoming changes
        //php artisan migrate:status  ----    Use for see which table remains
        //php artisan migrate   ----    Use for migrate the latest migrations [not effect on old migrations]
        //php artisan migrate:fresh   ----   Remove all tables and run

        if (!env('DEMO')) {
            dd("You are not in demo");
            return;
        }

        //Every hour reset data
        dispatch(function () {
            Artisan::call("m:r");
        })->delay(now()->addHours(6));

        try {
            //Maintenance mode on
            Artisan::call('down');
            $this->resetAllImages();

//        $this->resetAllImages();
//            Artisan::call('cache:clear');
            Artisan::call('db:wipe');
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed', ['class' => 'TestDatabaseSeeder']);
//        Artisan::call('passport:install');


            //Maintenance mode off
            Artisan::call('up');

            Artisan::call('optimize:clear');

        } catch (Exception $e) {
            dd($e);
        }

    }

    public function resetAllImages()
    {
        try {
            File::deleteDirectory('storage/app/public/admin_avatars');
            File::deleteDirectory('storage/app/public/delivery_boy_avatars');
            File::deleteDirectory('storage/app/public/seller_avatars');
            File::deleteDirectory('storage/app/public/customer_avatars');
            File::deleteDirectory('storage/app/public/shop_covers');
            File::deleteDirectory('storage/app/public/category_images');
            File::deleteDirectory('storage/app/public/product_images');
            File::deleteDirectory('storage/app/public/home_banner_images');

            File::copyDirectory('dummy/demo_images', 'storage/app/public/');
        } catch (Exception $e) {
            dd($e);
        }
    }

//    public function resetAllImages(){
//        File::copyDirectory('public/storage/demo_images', 'public/storage/');
//    }
}
