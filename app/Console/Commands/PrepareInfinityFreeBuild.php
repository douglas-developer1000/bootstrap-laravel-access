<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DateTimeZone;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
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
        $this->runBashCommand(
            'npm run build',
            'Command: "npm run build" running...',
            'End of assets build command!'
        );
    }

    public function runZipProcess()
    {
        $zip = new ZipArchive();

        $dt = now();
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        $dtParsed = $dt->format('d-m-Y--H-i-s');

        $zipFile = $this->buildPath(storage_path('builds'), "build-{$dtParsed}.zip");

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = collect($this->prepareEnvironment($zip));
            $this->zipAllRootFolders($zip);
            $this->zipAllRootFiles($zip);
            $this->buildStorage($zip);
            $zip->close();

            $files->each(fn($file) => File::delete($file));
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

    protected function runBashCommand(string $command, string ...$messages)
    {
        if (isset($messages[0])) {
            $this->info($messages[0]);
        }
        // Define onde o comando será executado
        Process::path(base_path())
            // Executa o comando
            ->run($command);

        if (isset($messages[1])) {
            $this->info($messages[1]);
        }
    }

    protected function runProdDependencies()
    {
        $this->runBashCommand(
            'composer install --no-dev --optimize-autoloader',
            'Inicializando limpeza do composer...',
            'Limpeza e carregamento limpo do composer OK!'
        );
    }

    protected function defineEnvScriptParser(string $fileLocalSource, string $fileProdSource, string $envOutputFilePath): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return \implode([
                '$envBase = Get-Content "',
                $fileLocalSource,
                '" | ForEach-Object { ($_ -split "=",2) }; $envProd = Get-Content "',
                $fileProdSource,
                '" | ForEach-Object { ($_ -split "=",2) }; $merged = @{}; ',
                'foreach ($line in $envBase) { $merged[$line[0]] = $line[1] }; ',
                'foreach ($line in $envProd) { $merged[$line[0]] = $line[1] }; ',
                '$merged.GetEnumerator() | ForEach-Object { "$($_.Key)=$($_.Value)" } | Set-Content "',
                $envOutputFilePath,
                '"'
            ]);
        }
        return \implode(' ', [
            'awk -F= \'FNR==NR { prod[$1]=$0; next } { if ($1 in prod) { print prod[$1] } else { print $0 } }\'',
            $fileProdSource,
            $fileLocalSource,
            '>',
            $envOutputFilePath
        ]);
    }

    /**
     * Prepare the environment to deploy
     * 
     * @return string[] The temporary file list generated by environment setup
     */
    protected function prepareEnvironment(ZipArchive $zip): array
    {
        $this->callSilent('key:generate', [
            '--env' => 'production'
        ]);
        $this->info('Nova chave APP_KEY gerada!');
        $envFilePath = $this->buildPath(storage_path('builds'), '.env.merged');
        $fileLocalSource = $this->buildPath(base_path(), '.env');
        $fileProdSource = $this->buildPath(base_path(), '.env.production');

        $script = $this->defineEnvScriptParser(
            $fileLocalSource,
            $fileProdSource,
            $envFilePath
        );
        $this->runBashCommand(
            $script,
            'Gerando arquivo .env para deploy...',
            'Arquivo .env para deploy gerado!'
        );
        $zip->addFile($envFilePath, '.env');
        $this->info('Arquivo .env para deploy copiado no zip!');
        $this->info('Arquivo temporário .env.merged removido!');

        return [$envFilePath];
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
