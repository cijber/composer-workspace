<?php

namespace Cijber\WorkspacePlugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;


class Plugin implements PluginInterface {

    protected Composer $composer;
    protected IOInterface $io;
    protected ?Workspace $workspace = null;

    public function activate(Composer $composer, IOInterface $io) {
        $this->composer = $composer;
        $this->io       = $io;

        $workspace = Workspace::findFor(getcwd());
        if ($workspace === null) {
            $io->notice("[cijber/workspace] No workspace found");
        } else {
            $this->registerWorkspace($workspace);
        }
    }

    public function deactivate(Composer $composer, IOInterface $io) {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io) {
        // TODO: Implement uninstall() method.
    }

    function onTest() {
    }

    private function registerWorkspace(Workspace $workspace) {
        $this->io->write("<info>[cijber/workspace]</info> Using workspace: <comment>{$workspace->getName()}</comment>");
        $this->workspace = $workspace;

        if (count($workspace->missing) > 0) {
            $this->io->warning("[cijber/workspace] Missing following packages in workspace: " . implode(", ", array_map(fn($x) => "<fg=red;bg=yellow;options=bold>$x</>", $workspace->missing)));
        }

        $this->composer->getRepositoryManager()->addRepository($this->workspace->createRepository($this->io, $this->composer));
    }
}