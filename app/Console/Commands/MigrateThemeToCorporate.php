<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateThemeToCorporate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:migrate-corporate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old Cyberpunk UI variables and classes to the new Corporate Light theme across all Blade files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting UI theme migration to Corporate layout...');
        
        $viewsPath = resource_path('views');
        if (!File::exists($viewsPath)) {
            $this->error('Views directory not found!');
            return Command::FAILURE;
        }

        $files = File::allFiles($viewsPath);
        $count = 0;

        $replacements = [
            'text-white-50' => 'text-muted',
            'class="text-white ' => 'class="text-dark ',
            'class="text-white"' => 'class="text-dark"',
            'text-white mb-2' => 'text-dark mb-2',
            'text-white mb-3' => 'text-dark mb-3',
            'text-white mb-4' => 'text-dark mb-4',
            'text-white small' => 'text-dark small',
            'text-white mt-1' => 'text-dark mt-1',
            'text-white mt-2' => 'text-dark mt-2',
            'text-white mt-3' => 'text-dark mt-3',
            'text-white fw-bold' => 'text-dark fw-bold',
            'text-white fw-semibold' => 'text-dark fw-semibold',
            'text-white d-flex' => 'text-dark d-flex',
            'h1 class="text-white"' => 'h1 class="text-dark"',
            'h2 class="text-white"' => 'h2 class="text-dark"',
            'h3 class="text-white"' => 'h3 class="text-dark"',
            'h4 class="text-white"' => 'h4 class="text-dark"',
            'h5 class="text-white"' => 'h5 class="text-dark"',
            'h6 class="text-white"' => 'h6 class="text-dark"',

            // Card resets
            'glass-card' => 'card shadow-sm border-0',
            'bg-transparent text-white' => 'bg-white text-dark',
            'border-white border-opacity-10' => 'border-light',
            
            // Adjust specific colors
            'rgba(255,255,255,0.03)' => '#f8fafc',
            'rgba(255, 255, 255, 0.03)' => '#f8fafc',
            'rgba(255,255,255,0.05)' => '#f1f5f9',
            'rgba(255, 255, 255, 0.05)' => '#f1f5f9',
            'rgba(255, 255, 255, 0.1)' => '#e2e8f0',
            'rgba(0,0,0,0.2)' => '#ffffff',
            'rgba(0, 0, 0, 0.2)' => '#ffffff',
            'rgba(0,0,0,0.15)' => '#ffffff',
            'rgba(0, 0, 0, 0.15)' => '#ffffff',
        ];

        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $path = $file->getPathname();
                $originalContent = File::get($path);
                
                $newContent = strtr($originalContent, $replacements);
                
                if ($newContent !== $originalContent) {
                    File::put($path, $newContent);
                    $count++;
                    $this->line("\nModified: {$file->getRelativePathname()}");
                }
            }
            $bar->advance();
        }

        $bar->finish();
        
        $this->newLine(2);
        $this->info("Migration completed successfully. {$count} file(s) updated.");

        return Command::SUCCESS;
    }
}
