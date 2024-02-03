<?php

namespace App\Console\Commands;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class clearObsoleteChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clear_obsolete_chunks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears obsolete chunks from failed uploads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        date_default_timezone_set(
            ini_get('date.timezone')
        );

        $timezone = date_default_timezone_get();

        $minimalTime = new DateTimeImmutable(
            Config::get('chunk-upload.clear.timestamp'), new DateTimeZone($timezone)
        );

        $files = Storage::files('chunks');

        foreach ($files as $file) {
            $fileModificationTime = new DateTimeImmutable(
                date('Y-m-d H:i:s', Storage::lastModified($file)),
                new DateTimeZone($timezone)
            );

            if ($fileModificationTime
                    ->format('Y-m-d H:i:s') <= $minimalTime
                    ->format('Y-m-d H:i:s')) {
                Storage::delete($file);

                echo $file . " was deleted successfully.\n";
            }
        }
    }
}
