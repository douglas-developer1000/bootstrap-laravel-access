<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DateTimeZone;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

#[Signature('build:infinity')]
#[Description('Prepare a build used by Infinity Free hosting')]
final class PrepareInfinityFreeBuild extends Command
{
    /**
     * Execute the console command.
     * 
     * After execute this command you must delete the vendor folder and
     * execute the follow command lines:
     * 
     * - $ composer install
     * - $ composer dump-autoload
     */
    public function handle()
    {
        $this->clearTasksPre();
        $this->cacheTasks();
        $this->runProdDependencies();
        $this->runAssetsBuild();

        $this->runZipProcess();
        $this->clearTasksPos();

        $this->info('End of artisan command!');
    }

    protected function runAssetsBuild()
    {
        $this->info('Command: "npm run build" running...');

        // Define onde o comando será executado
        Process::path(base_path())
            // Executa o comando
            ->run('npm run build');

        $this->info('End of assets build command!');
    }

    public function runZipProcess()
    {
        $zip = new ZipArchive();

        $dt = now();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $dtParsed = $dt->format('d-m-Y--H-i-s');

        $zipFile = $this->buildPath(storage_path('builds'), "build-{$dtParsed}.zip");

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $this->prepareEnvironment($zip);
            $this->zipAllRootFolders($zip);
            $this->zipAllRootFiles($zip);
            $this->buildStorage($zip);
            $zip->close();
        }
        $this->info('File loaded OK!');
    }

    protected function zipAllRootFolders(ZipArchive $zip)
    {
        $this->zipFolders(
            $zip,
            base_path(),
            'app',
            'bootstrap',
            'config',
            'database',
            'public',
            'resources',
            'routes',
            'vendor'
        );
        $this->info('Folders raízes compactados!');
    }

    protected function zipFolders(ZipArchive $zip, string $basePath, string ...$folderNames)
    {
        foreach ($folderNames as $foldername) {
            $folderSource = $this->buildPath($basePath, $foldername);
            $this->addFolderToZip($zip, $folderSource, $foldername);
        }
    }

    protected function addFolderToZip(ZipArchive $zip, string $folderPath, string $zipRootPath)
    {
        $zip->addEmptyDir($zipRootPath);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $this->buildPath($zipRootPath, substr($filePath, \strlen($folderPath) + 1));

            if ($file->isDir()) {
                // Add empty directory
                $zip->addEmptyDir($relativePath);
            } else {
                // Add file
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    protected function zipAllRootFiles(ZipArchive $zip)
    {
        $this->zipFiles(
            $zip,
            base_path(),
            'artisan',
            'composer.json',
            'composer.lock',
            'vite.config.js',
        );
        $this->info('Arquivos raízes compactados!');
    }

    protected function zipFiles(ZipArchive $zip, string $basePath, string ...$filesNames)
    {
        foreach ($filesNames as $filename) {
            $fileSource = $this->buildPath($basePath, $filename);
            $zip->addFile($fileSource, $filename);
        }
    }

    protected function clearTasksPre()
    {
        $this->callSilent('config:clear');
        $this->callSilent('cache:clear');
        $this->callSilent('view:clear');
        $this->callSilent('route:clear');

        $this->info('Limpeza de cache anterior OK!');
    }

    protected function clearTasksPos()
    {
        $this->callSilent('config:clear');
        $this->callSilent('view:clear');
        $this->callSilent('route:clear');

        $this->info('Limpeza de cache posterior OK!');
    }

    protected function cacheTasks()
    {
        $this->callSilent('config:cache');
        $this->callSilent('view:cache');
        $this->callSilent('route:cache');

        $this->info('Cache config e cache route carregados OK!');
    }

    protected function runProdDependencies()
    {
        $this->info('Inicializando limpeza do composer...');

        // Define onde o comando será executado
        Process::path(base_path())
            // Executa o comando
            ->run('composer install --no-dev --optimize-autoloader');

        $this->info('Limpeza e carregamento limpo do composer OK!');
    }

    protected function prepareEnvironment(ZipArchive $zip)
    {
        $this->callSilent('key:generate', [
            '--env' => 'production'
        ]);
        $fileSource = $this->buildPath(base_path(), '.env.production');
        $zip->addFile($fileSource, '.env');
        $this->info('Arquivo .env de produção copiado OK!');
    }

    /**
     * Manage the storage app to deploy into hosting server.
     * 
     * Before the upload of zip file, inside of the server:
     * 
     * - All must be deleted, except the 'storage' folder must be kept.
     * - Inside of the 'storage' folder, only the 'framework/views' folder must be deleted.
     */
    protected function buildStorage(ZipArchive $zip)
    {
        $zip->addEmptyDir($this->buildPath('', 'storage'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'app'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'app', 'public'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'framework'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'framework', 'cache'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'framework', 'session'));
        $zip->addEmptyDir($this->buildPath('', 'storage', 'logs'));
        $this->zipFolders($zip, base_path(), $this->buildPath('', 'storage', 'framework', 'views'));

        $this->info('Carregamento do storage OK!');
    }

    protected function buildPath(...$list)
    {
        return implode(DIRECTORY_SEPARATOR, $list);
    }
}
