<?php

namespace Cijber\WorkspacePlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PathRepository;
use Composer\Repository\RepositoryInterface;


class Workspace {

    public array $missing = [];


    public function __construct(
      public string $name,
      public array $packages = [],
    ) {
    }

    public function createRepository(IOInterface $io, Composer $composer): RepositoryInterface {
        $repos = [];
        foreach ($this->packages as $package) {
            $repos[] = new PathRepository(["url" => $package], $io, $composer->getConfig());
        }

        return new CompositeRepository($repos);
    }

    public function getName(): string {
        return $this->name;
    }

    public static function findFor(string $directory): ?Workspace {
        $workspaceDirectory = $directory;
        while ($workspaceDirectory !== '/') {
            $workspaceConfig = $workspaceDirectory . "/workspace.cijber.json";
            if (file_exists($workspaceDirectory . "/workspace.cijber.json")) {
                return Workspace::open($workspaceConfig, $directory);
            }

            $workspaceDirectory = dirname($workspaceDirectory);
        }

        return null;
    }

    public static function fromArray(array $workspaceArray, string $context): Workspace {
        $name     = $workspaceArray['name'] ?? "[untitled]";
        $packages = $workspaceArray['packages'] ?? [];

        $context = rtrim($context, '/') . '/';
        $full    = [];
        $missing = [];
        foreach ($packages as $package) {
            $packagePath = $context . $package;
            if ( ! is_dir($packagePath)) {
                $missing[] = $package;
                continue;
            }

            $full[] = realpath($packagePath);
        }

        $ws          = new Workspace($name, $full);
        $ws->missing = $missing;

        return $ws;
    }

    public static function open(string $workspaceConfig, string $packageDirectory): Workspace {
        $json = file_get_contents($workspaceConfig);
        $data = JsonFile::parseJson($json, $workspaceConfig);

        return Workspace::fromArray($data, dirname($workspaceConfig));
    }
}